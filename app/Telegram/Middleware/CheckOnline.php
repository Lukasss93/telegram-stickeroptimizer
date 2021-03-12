<?php

namespace App\Telegram\Middleware;

use SergiX44\Nutgram\Nutgram;

class CheckOnline
{
    public function __invoke(Nutgram $bot, $next): void
    {
        if (!config('telegram.bot.online') && $bot->user()?->id !== config('telegram.dev.id')) {
            return;
        }

        $next($bot);
    }
}
