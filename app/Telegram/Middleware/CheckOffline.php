<?php

namespace App\Telegram\Middleware;

use App\Models\Chat;
use SergiX44\Nutgram\Nutgram;
use SergiX44\Nutgram\Telegram\Attributes\ParseMode;

class CheckOffline
{
    public function __invoke(Nutgram $bot, $next): void
    {
        $chat = $bot->getData(Chat::class);

        if (!config('bot.online')) {

            if ($chat->chat_id === config('developer.id')) {
                $bot->sendMessage('<b>⚠ OFFLINE MODE ENABLED ⚠</b>', [
                    'parse_mode' => ParseMode::HTML,
                ]);
                $next($bot);

                return;
            }

            if ($bot->isCallbackQuery()) {
                $bot->answerCallbackQuery([
                    'text' => trans('maintenance.offline'),
                    'show_alert' => true,
                ]);

                return;
            }

            $bot->sendMessage(message('offline'), [
                'parse_mode' => ParseMode::HTML,
            ]);

            return;
        }

        $next($bot);
    }
}
