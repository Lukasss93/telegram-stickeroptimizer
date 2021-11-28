<?php

namespace App\Jobs;

use App\Enums\TelegramLimit;
use App\Exceptions\TelegramWrongFileIdException;
use App\ImageFilters\ScaleFilter;
use App\ImageFilters\WatermarkFilter;
use App\Models\Chat;
use Glorand\Model\Settings\Exceptions\ModelSettingsException;
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

class OptimizeStickerJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private int $chatID;
    private int $replyID;
    private string $fileID;

    public function __construct(int $chatID, int $replyID, string $fileID)
    {
        $this->chatID = $chatID;
        $this->replyID = $replyID;
        $this->fileID = $fileID;
    }

    /**
     * Handle job logic
     * @throws ModelSettingsException
     */
    public function handle(Nutgram $bot): void
    {
        $file = null;

        try {
            $file = $bot->getFile($this->fileID);

            //check if file exists
            if ($file === null) {
                throw new InvalidArgumentException('Invalid file');
            }

            //set sending status
            $bot->sendChatAction(ChatActions::UPLOAD_PHOTO, [
                'chat_id' => $this->chatID,
            ]);

            //get chat settings
            $chatSettings = Chat::find($this->chatID)?->settings();

            //check if resource is an animated webp
            if (isAnAnimatedWebp(fopen($file->url(), 'rb'))) {
                throw new NotReadableException();
            }

            //load image
            $image = Image::make($file->url());

            //scale image
            $image->filter(ScaleFilter::make());

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

        } catch (TelegramWrongFileIdException | NotReadableException) {
            $bot->sendMessage(trans('common.invalid_file'));
        } catch (InvalidArgumentException) {
            $bot->sendMessage(trans('common.invalid_file'));

            if ($file !== null) {
                $bot->sendDocument($file->file_id, [
                    'chat_id' => config('developer.id'),
                ]);
            }
        }
    }
}
