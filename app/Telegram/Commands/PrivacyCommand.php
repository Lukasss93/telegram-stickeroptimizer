<?php


namespace App\Telegram\Commands;

use SergiX44\Nutgram\Nutgram;
use SergiX44\Nutgram\Telegram\Attributes\ParseMode;
use SergiX44\Nutgram\Telegram\Types\InlineKeyboardButton;
use SergiX44\Nutgram\Telegram\Types\InlineKeyboardMarkup;

class PrivacyCommand
{
    public function __invoke(Nutgram $bot): void
    {
        $bot->sendMessage(message('privacy'), [
            'parse_mode' => ParseMode::HTML,
            'disable_web_page_preview' => true,
            'reply_markup' => InlineKeyboardMarkup::make()
                ->addRow(
                    InlineKeyboardButton::make(trans('privacy.title'), config('bot.privacy'))
                ),
        ]);
    }
}
