<?php

namespace App\Jobs;

use App\Enums\StickerTemplate;
use App\Enums\TelegramLimit;
use App\Exceptions\TooLargeFileException;
use App\Facades\ImageUtils;
use App\ImageFilters\ScaleFilter;
use App\ImageFilters\TrimTransparentPixelsFilter;
use App\ImageFilters\WatermarkFilter;
use App\Models\Chat;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Str;
use Intervention\Image\Exception\NotReadableException;
use Intervention\Image\Facades\Image;
use InvalidArgumentException;
use SergiX44\Nutgram\Nutgram;
use SergiX44\Nutgram\Telegram\Properties\ChatAction;
use SergiX44\Nutgram\Telegram\Properties\ParseMode;
use SergiX44\Nutgram\Telegram\Types\Internal\InputFile;
use Throwable;

class OptimizeStickerJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        protected int $chatID,
        protected int $replyID,
        protected string $fileID,
        protected int $fileSize,
    ) {
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
            $bot->sendChatAction(
                action: ChatAction::UPLOAD_PHOTO,
                chat_id: $this->chatID,
            );

            //check if resource is an animated webp
            if (ImageUtils::isAnAnimatedWebp(fopen($file->url(), 'rb'))) {
                throw new NotReadableException();
            }

            //get chat settings
            $chatSettings = Chat::find($this->chatID)?->settings();

            //load image
            $image = Image::make($file->url());

            //remove adjacent transparent pixels
            $image->filter(TrimTransparentPixelsFilter::make($chatSettings));

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
            $bot->sendDocument(
                document: InputFile::make($stream->detach(), Str::uuid().'.'.$ext),
                chat_id: $this->chatID,
                caption: message('donate.caption'),
                parse_mode: ParseMode::HTML,
                reply_to_message_id: $this->replyID,
                allow_sending_without_reply: true,
            );

            //save statistic
            stats('sticker.optimized');

        } catch (TooLargeFileException $e) {
            $bot->sendMessage(
                text: $e->getMessage(),
                chat_id: $this->chatID,
                reply_to_message_id: $this->replyID,
                allow_sending_without_reply: true,
            );
        } catch (Throwable) {
            $bot->sendMessage(trans('common.invalid_file'));
        }
    }
}
