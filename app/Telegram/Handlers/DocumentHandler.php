<?php

namespace App\Telegram\Handlers;

use App\Enums\TelegramLimit;
use App\Jobs\OptimizeStickerJob;
use App\Jobs\OptimizeVideoStickerJob;
use SergiX44\Nutgram\Nutgram;

class DocumentHandler
{
    public function __invoke(Nutgram $bot): void
    {
        $replyID = $bot->messageId();
        $fileSize = $bot->message()->document->file_size;
        $fileID = $bot->message()->document->file_id;
        $mime = $bot->message()->document->mime_type;

        if ($mime === 'video/webm') {
            $this->optimizeVideo($bot, $replyID, $fileID, $fileSize);

            return;
        }

        OptimizeStickerJob::dispatchSync($bot->chatId(), $replyID, $fileID, $fileSize);

        stats('handler.document', ['mime' => $mime]);
    }

    protected function optimizeVideo(Nutgram $bot, ?int $replyID, string $fileID, ?int $fileSize): void
    {
        if ($fileSize >= TelegramLimit::DOWNLOAD->value) {
            $bot->sendMessage(
                text: trans('common.too_large_file'),
                reply_to_message_id: $replyID,
                allow_sending_without_reply: true,
            );

            return;
        }

        //send status message
        $statusMessage = $bot->sendMessage(
            text: trans('optimize.running'),
            chat_id: $bot->chatId(),
            reply_to_message_id: $replyID,
            allow_sending_without_reply: true,
        );

        OptimizeVideoStickerJob::dispatch($bot->chatId(), $replyID, $fileID, $statusMessage?->message_id)
            ->onQueue('video');
    }
}
