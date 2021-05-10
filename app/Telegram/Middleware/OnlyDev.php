<?php

namespace App\Telegram\Middleware;

use SergiX44\Nutgram\Nutgram;

class OnlyDev
{
    public function __invoke(Nutgram $bot, $next): void
    {
        if ($bot->user()?->id !== config('developer.id')) {
            return;
        }

        $next($bot);
    }
}
