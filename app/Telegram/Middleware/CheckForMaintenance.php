<?php

namespace App\Telegram\Middleware;

use SergiX44\Nutgram\Nutgram;
use SergiX44\Nutgram\Telegram\Attributes\ParseMode;

class CheckForMaintenance
{
    public function __invoke(Nutgram $bot, $next): void
    {
        if (app()->isDownForMaintenance() && $bot->user()?->id !== config('telegram.dev.id')) {
            if ($bot->isCallbackQuery()) {
                $bot->answerCallbackQuery();
            }

            $bot->sendMessage(message('maintenance'), [
                'parse_mode' => ParseMode::HTML,
            ]);
            return;
        }

        $next($bot);
    }
}
