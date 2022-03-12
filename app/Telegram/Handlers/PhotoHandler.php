<?php

namespace App\Telegram\Handlers;

use App\Jobs\OptimizeStickerJob;
use SergiX44\Nutgram\Nutgram;

class PhotoHandler
{
    public function __invoke(Nutgram $bot)
    {
        $replyID = $bot->messageId();
        $file = end($bot->message()->photo);
        $fileSize = $file->file_size;
        $fileID = $file->file_id;

        OptimizeStickerJob::dispatchSync($bot->chatId(), $replyID, $fileID, $fileSize);

        stats('photo', 'handler');
    }
}
