<?php

namespace App\Telegram\Handlers;

use App\Actions\OptimizeVideo;
use SergiX44\Nutgram\Nutgram;

class VideoHandler
{
    public function __construct(
        protected OptimizeVideo $optimizeVideoAction,
    ) {
    }

    public function __invoke(Nutgram $bot): void
    {
        $replyID = $bot->messageId();
        $fileSize = $bot->message()->video->file_size;
        $fileID = $bot->message()->video->file_id;

        $this->optimizeVideoAction->handle($bot, $replyID, $fileID, $fileSize);

        stats('handler.video');
    }
}
