<?php

namespace App\Telegram\Handlers;

use App\Actions\OptimizeVideo;
use App\Jobs\OptimizeStickerJob;
use SergiX44\Nutgram\Nutgram;

class DocumentHandler
{
    public function __construct(
        protected OptimizeVideo $optimizeVideoAction,
    ) {
    }

    public function __invoke(Nutgram $bot): void
    {
        $replyID = $bot->messageId();
        $fileSize = $bot->message()->document->file_size;
        $fileID = $bot->message()->document->file_id;
        $mime = $bot->message()->document->mime_type;

        if ($mime === 'video/webm') {
            $this->optimizeVideoAction->handle($bot, $replyID, $fileID, $fileSize);

            return;
        }

        OptimizeStickerJob::dispatchSync($bot->chatId(), $replyID, $fileID, $fileSize);

        stats('handler.document', ['mime' => $mime]);
    }
}
