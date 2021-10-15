<?php

namespace App\Telegram\Middleware;

use App\Models\Chat;
use Illuminate\Support\Facades\App;
use SergiX44\Nutgram\Nutgram;

class SetLocale
{
    public function __invoke(Nutgram $bot, $next): void
    {
        $chat = $bot->getData(Chat::class);

        App::setLocale($chat->settings()->get('language') ?? config('app.locale'));

        $next($bot);
    }
}
