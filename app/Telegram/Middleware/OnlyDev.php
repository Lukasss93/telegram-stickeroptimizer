<?php

namespace App\Telegram\Middleware;

use SergiX44\Nutgram\Nutgram;

class OnlyDev
{
    public function __invoke(Nutgram $bot, $next): void
    {
        if ($bot->user()?->id !== config('telegram.dev.id')) {
            return;
        }

        $next($bot);
    }
}
