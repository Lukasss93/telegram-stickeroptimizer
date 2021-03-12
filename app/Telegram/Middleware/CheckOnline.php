<?php

namespace App\Telegram\Middleware;

use SergiX44\Nutgram\Nutgram;
use SergiX44\Nutgram\Telegram\Attributes\ParseMode;

class CheckOnline
{
    public function __invoke(Nutgram $bot, $next): void
    {
        if (!config('telegram.bot.online') && $bot->user()?->id !== config('telegram.dev.id')) {
            if ($bot->isCallbackQuery()) {
                $bot->answerCallbackQuery();
            }

            $bot->sendMessage(message('offline'), [
                'parse_mode' => ParseMode::HTML,
            ]);
            return;
        }

        $next($bot);
    }
}
