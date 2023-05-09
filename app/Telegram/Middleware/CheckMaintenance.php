<?php

namespace App\Telegram\Middleware;

use App\Models\Chat;
use SergiX44\Nutgram\Nutgram;
use SergiX44\Nutgram\Telegram\Properties\ParseMode;

class CheckMaintenance
{
    public function __invoke(Nutgram $bot, $next): void
    {
        $chat = $bot->get(Chat::class);

        if (app()->isDownForMaintenance()) {

            if ($chat->chat_id === config('developer.id')) {
                $bot->sendMessage(
                    text: '<b>⚠ MAINTENANCE MODE ENABLED ⚠</b>',
                    parse_mode: ParseMode::HTML,
                );
                $next($bot);

                return;
            }

            if ($bot->isCallbackQuery()) {
                $bot->answerCallbackQuery(
                    text: trans('maintenance.title'),
                    show_alert: true,
                );

                return;
            }

            $bot->sendMessage(
                text: message('maintenance'),
                parse_mode: ParseMode::HTML,
            );

            return;
        }

        $next($bot);
    }
}
