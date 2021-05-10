<?php

namespace App\Telegram\Conversations;

use Illuminate\Support\Collection;
use SergiX44\Nutgram\Nutgram;
use SergiX44\Nutgram\Telegram\Attributes\ParseMode;
use SergiX44\Nutgram\Telegram\Types\InlineKeyboardButton;
use SergiX44\Nutgram\Telegram\Types\InlineKeyboardMarkup;

class DonateConversation extends OneMessageConversation
{
    protected ?string $step = 'donationMenu';

    /**
     * Donation menu
     * @param Nutgram $bot
     */
    public function donationMenu(Nutgram $bot): void
    {
        $keyboard = InlineKeyboardMarkup::make();

        collect([])
            ->when(config('bot.donations.providers.github') !== null, function (Collection $collection) {
                return $collection->push(InlineKeyboardButton::make('Github Sponsor', config('bot.donations.providers.github')));
            })
            ->when(config('bot.donations.providers.paypal') !== null, function (Collection $collection) {
                return $collection->push(InlineKeyboardButton::make('Paypal Donation', config('bot.donations.providers.paypal')));
            })
            ->when(config('bot.donations.providers.telegram') !== null, function (Collection $collection) {
                return $collection->push(InlineKeyboardButton::make('Telegram Payment', callback_data: 'donate.telegram'));
            })->each(fn ($item) => $keyboard->addRow($item));

        $keyboard->addRow(InlineKeyboardButton::make('❌ '.trans('common.close'), callback_data: 'donate.cancel'));

        $message = $this->sendOrEditMessage(message('donate.menu'), [
            'parse_mode' => ParseMode::HTML,
            'reply_markup' => $keyboard,
            'disable_web_page_preview' => true,
        ]);

        $this->updateLastMessageStatus('menuHandler', $message);
    }

    public function menuHandler(Nutgram $bot): void
    {
        $data = $bot->callbackQuery()?->data;

        if ($data === 'donate.telegram') {
            $this->donationTelegram($bot);
        } elseif ($data === 'donate.cancel') {
            $this->cancelDonation($bot);
        } elseif ($data === 'donate.telegram.back') {
            $this->donationMenu($bot);
        } elseif (str_starts_with($data, 'donate.telegram.value.')) {
            $value = (int) last(explode('.', $data));

            $bot->sendInvoice(
                __('donate.donation'),
                __('donate.support_by_donating'),
                'donation',
                config('bot.donations.providers.telegram'),
                'donation',
                'EUR',
                [['label' => "{$value}€", 'amount' => $value * 100]]
            );

        } elseif ($bot->isCallbackQuery()) {
            $bot->answerCallbackQuery();
        }
    }

    public function donationTelegram(Nutgram $bot): void
    {
        $message = $this->sendOrEditMessage(message('donate.telegram'), [
            'parse_mode' => ParseMode::HTML,
            'reply_markup' => InlineKeyboardMarkup::make()
                ->addRow(
                    InlineKeyboardButton::make('1€', callback_data: 'donate.telegram.value.1'),
                    InlineKeyboardButton::make('5€', callback_data: 'donate.telegram.value.5'),
                    InlineKeyboardButton::make('10€', callback_data: 'donate.telegram.value.10'),
                    InlineKeyboardButton::make('25€', callback_data: 'donate.telegram.value.25'),
                    InlineKeyboardButton::make('50€', callback_data: 'donate.telegram.value.50'),
                    InlineKeyboardButton::make('100€', callback_data: 'donate.telegram.value.100'),
                )->addRow(
                    InlineKeyboardButton::make('🔙 '.trans('common.back'), callback_data: 'donate.telegram.back')
                ),
        ]);

        $this->updateLastMessageStatus('menuHandler', $message);
    }

    public function cancelDonation(Nutgram $bot): void
    {
        if ($bot->isCallbackQuery()) {
            $bot->answerCallbackQuery();
        }

        if ($this->chatId !== null && $this->messageId !== null) {
            $bot->deleteMessage($this->chatId, $this->messageId);
        }

        $this->end();
    }
}
