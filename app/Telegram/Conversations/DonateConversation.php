<?php

namespace App\Telegram\Conversations;

use JsonException;
use Psr\SimpleCache\InvalidArgumentException;
use SergiX44\Nutgram\Conversations\InlineMenu;
use SergiX44\Nutgram\Nutgram;
use SergiX44\Nutgram\Telegram\Attributes\ParseMode;
use SergiX44\Nutgram\Telegram\Types\Keyboard\InlineKeyboardButton;

class DonateConversation extends InlineMenu
{
    /**
     * Open donation menu
     * @param Nutgram $bot
     * @throws InvalidArgumentException
     */
    public function start(Nutgram $bot): void
    {
        $this->menuText(message('donate.menu'), [
            'parse_mode' => ParseMode::HTML,
            'disable_web_page_preview' => true,
        ]);

        $this->clearButtons();
        $this->addButtonRow(InlineKeyboardButton::make('Telegram Payment',
            callback_data: 'donate.telegram@menuTelegram'));

        foreach (config('donation.third_party_providers.url') as $service => $value) {
            $this->addButtonRow(InlineKeyboardButton::make($service, url: $value));
        }

        foreach (config('donation.third_party_providers.text') as $service => $value) {
            $this->addButtonRow(InlineKeyboardButton::make($service,
                callback_data: "donate.third.$service@menuThirdParty"));
        }

        $this->addButtonRow(InlineKeyboardButton::make('❌ '.__('common.close'),
            callback_data: 'donate.cancel@end'));

        $this->showMenu();

        stats('donate', 'command');
    }

    /**
     * Telegram payment actions
     * @throws InvalidArgumentException
     */
    public function menuTelegram(): void
    {
        $this->menuText(message('donate.telegram'), [
            'parse_mode' => ParseMode::HTML,
            'disable_web_page_preview' => true,
        ]);

        $this->clearButtons();
        $this->addButtonRow(
            InlineKeyboardButton::make('1€', callback_data: 'donate.telegram.value.1@donationInvoice'),
            InlineKeyboardButton::make('5€', callback_data: 'donate.telegram.value.5@donationInvoice'),
            InlineKeyboardButton::make('10€', callback_data: 'donate.telegram.value.10@donationInvoice'),
            InlineKeyboardButton::make('25€', callback_data: 'donate.telegram.value.25@donationInvoice'),
            InlineKeyboardButton::make('50€', callback_data: 'donate.telegram.value.50@donationInvoice'),
            InlineKeyboardButton::make('100€', callback_data: 'donate.telegram.value.100@donationInvoice')
        );

        $this->addButtonRow(InlineKeyboardButton::make('🔙 '.trans('app.back'),
            callback_data: 'donate.telegram.back@start'));

        $this->showMenu();

        stats('donate.telegram', 'donation');
    }

    /**
     * Telegram invoice actions
     * @param Nutgram $bot
     * @throws InvalidArgumentException
     * @throws JsonException
     */
    public function donationInvoice(Nutgram $bot): void
    {
        $value = (int)last(explode('.', $bot->callbackQuery()->data));

        $this->bot->sendInvoice(
            __('donate.donation'),
            __('donate.support_by_donating'),
            'donation',
            config('donation.provider_token'),
            'EUR',
            [['label' => "{$value}€", 'amount' => $value * 100]]
        );

        $this->end();

        stats('donate.invoice', 'donation', ['value' => $value]);
    }

    /**
     * Third-party providers actions
     * @param Nutgram $bot
     * @throws InvalidArgumentException
     */
    public function menuThirdParty(Nutgram $bot): void
    {
        $service = last(explode('.', $bot->callbackQuery()->data));

        $text = config("donation.third_party_providers.text.$service");

        $photo = qrcode($text, $service, true);

        $this->bot->sendPhoto($photo, [
            'caption' => message('donate.third', [
                'service' => $service,
                'text' => $text,
            ]),
            'parse_mode' => ParseMode::HTML,
        ]);

        $this->end();

        stats('donate.third', 'donation', ['service' => $service]);
    }

    protected function closing(Nutgram $bot): void
    {
        parent::closing($bot);

        stats('donate.cancel', 'donation');
    }
}
