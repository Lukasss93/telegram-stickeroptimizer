<?php

namespace App\Jobs;

use App\Enums\StickerTemplate;
use App\ImageFilters\WatermarkFilter;
use App\Models\Chat;
use FFMpeg\Coordinate\Dimension;
use FFMpeg\Coordinate\FrameRate;
use FFMpeg\Coordinate\TimeCode;
use FFMpeg\Filters\Video\ResizeFilter;
use FFMpeg\Filters\Video\VideoFilters;
use FFMpeg\Format\Video\WebM;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;
use InvalidArgumentException;
use ProtoneMedia\LaravelFFMpeg\Filters\WatermarkFactory;
use ProtoneMedia\LaravelFFMpeg\Support\FFMpeg;
use SergiX44\Nutgram\Nutgram;
use SergiX44\Nutgram\Telegram\Types\Internal\InputFile;
use Throwable;

class OptimizeVideoStickerJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected const int FILESIZE_LIMIT = 256;
    protected const int LENGTH_LIMIT = 3;
    protected const int FPS = 30;

    public function __construct(
        protected int $chatID,
        protected int $replyID,
        protected string $fileID,
        protected ?int $statusMessageId,
    ) {
        $this->onQueue('video');
    }

    public function handle(Nutgram $bot): void
    {
        //initialize disks
        $tmpDisk = Storage::disk('temp');
        $convertedDisk = Storage::disk('converted');

        //file names
        $videoFilename = uniqid('', true).'.webm';
        $watermarkFilename = uniqid('', true).'.png';

        try {
            //get file from telegram
            $file = $bot->getFile($this->fileID);

            //check if file exists
            if ($file === null) {
                throw new InvalidArgumentException('Invalid file');
            }

            //get chat + settings
            $chat = Chat::find($this->chatID);
            $chatSettings = $chat->settings();
            $template = StickerTemplate::from($chatSettings->get('template'));

            //output format
            $format = new WebM(videoCodec: 'libvpx-vp9');
            $format->setKiloBitrate(self::FILESIZE_LIMIT);

            //open file
            $ffmpeg = FFMpeg::openUrl($file->url(), []);

            //get video stream
            $videoStream = $ffmpeg->getVideoStream();

            //check if video stream exists
            if ($videoStream === null) {
                throw new InvalidArgumentException('Invalid video stream');
            }

            //original dimensions
            $originalWidth = $videoStream->getDimensions()->getWidth();
            $originalHeight = $videoStream->getDimensions()->getHeight();

            //resized dimensions
            $resizedDimensions = $this->resizeDimensions($originalWidth, $originalHeight, $template->getSide());
            $resizedWidth = $resizedDimensions->getWidth();
            $resizedHeight = $resizedDimensions->getHeight();

            //build filters
            $ffmpeg
                ->resize($template->getSide(), $template->getSide(), ResizeFilter::RESIZEMODE_INSET)
                ->addFilter(fn (VideoFilters $filters) => $filters
                    ->clip(TimeCode::fromSeconds(0), TimeCode::fromSeconds(self::LENGTH_LIMIT))
                    ->framerate(new FrameRate(self::FPS), 30)
                )
                ->addFilter('-an');

            //create watermark
            if ($chatSettings->get('watermark.opacity') > 0) {
                $ffmpeg->addWatermark(function (WatermarkFactory $watermark) use (
                    $watermarkFilename,
                    $tmpDisk,
                    $chatSettings,
                    $resizedHeight,
                    $resizedWidth
                ) {
                    Image::canvas($resizedWidth, $resizedHeight)
                        ->filter(WatermarkFilter::make($chatSettings))
                        ->save($tmpDisk->path($watermarkFilename), 100, 'png');

                    //add the watermark
                    $watermark
                        ->fromDisk($tmpDisk)
                        ->open($watermarkFilename);
                });
            }

            //save video
            $ffmpeg
                ->export()
                ->toDisk($convertedDisk)
                ->inFormat($format)
                ->save($videoFilename)
                ->cleanupTemporaryFiles();

            //send converted file
            $bot->sendDocument(
                document: InputFile::make($convertedDisk->readStream($videoFilename)),
                chat_id: $this->chatID,
                reply_to_message_id: $this->replyID,
                allow_sending_without_reply: true,
            );

            //save statistic
            stats('video.optimized');
        } catch (Throwable $e) {
            report($e);
            $bot->sendMessage(
                text: trans('optimize.error'),
                chat_id: $this->chatID,
                reply_to_message_id: $this->replyID,
                allow_sending_without_reply: true,
            );
        } finally {
            //delete status message
            if ($this->statusMessageId !== null) {
                $bot->deleteMessage($this->chatID, $this->statusMessageId);
            }

            //delete converted file
            if ($convertedDisk->exists($videoFilename)) {
                $convertedDisk->delete($videoFilename);
            }

            //delete watermark
            if ($tmpDisk->exists($watermarkFilename)) {
                $tmpDisk->delete($watermarkFilename);
            }
        }
    }

    protected function resizeDimensions(int $width, int $height, int $limit): Dimension
    {
        if ($width > $height) {
            $height = (int)(($height * $limit) / $width);
            $width = $limit;
        } else {
            $width = (int)(($width * $limit) / $height);
            $height = $limit;
        }

        return new Dimension($width, $height);
    }
}
