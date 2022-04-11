<?php

namespace App\Jobs;

use App\Enums\StickerTemplate;
use App\Enums\TelegramLimit;
use App\Exceptions\TooLargeFileException;
use App\Facades\ImageUtils;
use App\ImageFilters\ScaleFilter;
use App\ImageFilters\WatermarkFilter;
use App\Models\Chat;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Str;
use Intervention\Image\Exception\InvalidArgumentException;
use Intervention\Image\Exception\NotReadableException;
use Intervention\Image\Facades\Image;
use SergiX44\Nutgram\Nutgram;
use SergiX44\Nutgram\Telegram\Attributes\ChatActions;
use SergiX44\Nutgram\Telegram\Attributes\ParseMode;
use SergiX44\Nutgram\Telegram\Types\Internal\InputFile;
use Throwable;

class OptimizeStickerJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private int $chatID;
    private int $replyID;
    private string $fileID;
    private int $fileSize;

    /**
     * @param int $chatID
     * @param int $replyID
     * @param string $fileID
     * @param int $fileSize
     */
    public function __construct(int $chatID, int $replyID, string $fileID, int $fileSize)
    {
        $this->chatID = $chatID;
        $this->replyID = $replyID;
        $this->fileID = $fileID;
        $this->fileSize = $fileSize;
    }

    /**
     * Handle job logic
     */
    public function handle(Nutgram $bot): void
    {
        try {
            //check file size
            if ($this->fileSize >= TelegramLimit::DOWNLOAD->value) {
                throw new TooLargeFileException(trans('common.too_large_file'));
            }

            //get file
            $file = $bot->getFile($this->fileID);

            //check if file exists
            if ($file === null) {
                throw new InvalidArgumentException('Invalid file');
            }

            //set sending status
            $bot->sendChatAction(ChatActions::UPLOAD_PHOTO, [
                'chat_id' => $this->chatID,
            ]);

            //check if resource is an animated webp
            if (ImageUtils::isAnAnimatedWebp(fopen($file->url(), 'rb'))) {
                throw new NotReadableException();
            }

            //get chat settings
            $chatSettings = Chat::find($this->chatID)?->settings();

            //load image
            $image = Image::make($file->url());

            //scale image
            $image->filter(ScaleFilter::make(StickerTemplate::from($chatSettings->get('template'))));

            //apply watermark
            $image->filter(WatermarkFilter::make($chatSettings));

            //compress image
            $ext = 'png';
            $stream = $image->stream('png');
            if ($stream->getSize() > TelegramLimit::STICKER_MAX_SIZE->value) {
                $quality = 100;
                do {
                    $stream = $image->stream('webp', $quality);
                    $quality--;
                } while ($stream->getSize() > TelegramLimit::STICKER_MAX_SIZE->value);
                $ext = 'webp';
            }

            //send optimized image
            $bot->sendDocument(InputFile::make($stream->detach(), Str::uuid().'.'.$ext), [
                'caption' => message('donate.caption'),
                'parse_mode' => ParseMode::HTML,
                'chat_id' => $this->chatID,
                'reply_to_message_id' => $this->replyID,
                'allow_sending_without_reply' => true,
            ]);

            //save statistic
            stats('sticker', 'optimized');

        } catch (TooLargeFileException $e) {
            $bot->sendMessage($e->getMessage(), [
                'chat_id' => $this->chatID,
                'reply_to_message_id' => $this->replyID,
                'allow_sending_without_reply' => true,
            ]);
        } catch (Throwable) {
            $bot->sendMessage(trans('common.invalid_file'));
        }
    }
}
