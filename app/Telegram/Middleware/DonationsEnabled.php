<?php

namespace App\Telegram\Middleware;

use SergiX44\Nutgram\Nutgram;

class DonationsEnabled
{
    public function __invoke(Nutgram $bot, $next): void
    {
        if (!config('donation.enabled')) {
            return;
        }

        $next($bot);
    }
}
