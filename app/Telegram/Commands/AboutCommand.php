<?php


namespace App\Telegram\Commands;

use SergiX44\Nutgram\Nutgram;
use SergiX44\Nutgram\Telegram\Attributes\ParseMode;

class AboutCommand
{
    public function __invoke(Nutgram $bot):void
    {
        $bot->sendMessage(message('about'), [
            'parse_mode' => ParseMode::HTML,
            'disable_web_page_preview' => true,
        ]);

        stats('about', 'command');
    }
}
