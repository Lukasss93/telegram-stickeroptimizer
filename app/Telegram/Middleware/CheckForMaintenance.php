<?php


namespace App\Telegram\Middleware;

use SergiX44\Nutgram\Nutgram;
use SergiX44\Nutgram\Telegram\Attributes\ParseMode;
use SergiX44\Nutgram\Telegram\Attributes\UpdateTypes;

class CheckForMaintenance
{
    public function __invoke(Nutgram $bot, $next): void
    {
        if (app()->isDownForMaintenance()) {
            if ($bot->update()?->getType() === UpdateTypes::CALLBACK_QUERY) {
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
