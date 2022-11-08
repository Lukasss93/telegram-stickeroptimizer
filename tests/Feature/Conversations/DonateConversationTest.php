<?php

use App\Facades\ImageUtils;
use SergiX44\Nutgram\Telegram\Attributes\UpdateTypes;
use SergiX44\Nutgram\Telegram\Types\Keyboard\InlineKeyboardButton;
use SergiX44\Nutgram\Telegram\Types\Keyboard\InlineKeyboardMarkup;

beforeEach(function () {
    $keyboard = InlineKeyboardMarkup::make()
        ->addRow(InlineKeyboardButton::make('Telegram Payment', callback_data: 'donate.telegram'));

    foreach (config('donation.third_party_providers.url') as $service => $value) {
        $keyboard->addRow(InlineKeyboardButton::make($service, url: $value));
    }

    foreach (config('donation.third_party_providers.text') as $service => $value) {
        $keyboard->addRow(InlineKeyboardButton::make($service,
            callback_data: $service));
    }

    $keyboard->addRow(InlineKeyboardButton::make('❌ '.trans('common.close'), callback_data: 'donate.cancel'));

    $this->test = bot()
        ->willStartConversation()
        ->hearText('/donate')
        ->reply()
        ->assertReplyMessage([
            'text' => message('donate.menu'),
            'reply_markup' => $keyboard,
        ])
        ->assertActiveConversation();

    $this->assertDatabaseHas('statistics', [
        'action' => 'donate',
        'category' => 'command',
    ]);
});

it('sends /donate command', function () {
    $this->test;
});

it('clicks on Telegram Payment button + it generates donation invoice', function () {
    $value = '1';

    $this->test
        ->hearCallbackQueryData('donate.telegram')
        ->reply()
        ->assertReplyMessage([
            'text' => message('donate.telegram'),
            'reply_markup' => InlineKeyboardMarkup::make()
                ->addRow(
                    InlineKeyboardButton::make('1$', callback_data: '1'),
                    InlineKeyboardButton::make('5$', callback_data: '5'),
                    InlineKeyboardButton::make('10$', callback_data: '10'),
                    InlineKeyboardButton::make('25$', callback_data: '25'),
                    InlineKeyboardButton::make('50$', callback_data: '50'),
                    InlineKeyboardButton::make('100$', callback_data: '100')
                )
                ->addRow(InlineKeyboardButton::make('🔙 '.trans('common.back'),
                    callback_data: 'donate.telegram.back')),
        ])
        ->assertReply('answerCallbackQuery', index: 1)
        ->hearCallbackQueryData($value)
        ->reply()
        ->assertReplyMessage([
            'title' => trans('donate.donation'),
            'description' => trans('donate.support_by_donating'),
            'payload' => 'donation',
            'provider_token' => config('donation.provider_token'),
            'currency' => 'USD',
            'prices' => json_encode([['label' => "{$value}$", 'amount' => $value * 100]]),
        ], 0, 'sendInvoice')
        ->assertReply('deleteMessage', index: 1)
        ->assertReply('answerCallbackQuery', index: 2);

    $this->assertDatabaseHas('statistics', [
        'action' => 'donate.telegram',
        'category' => 'donation',
    ]);

    $this->assertDatabaseHas('statistics', [
        'action' => 'donate.invoice',
        'category' => 'donation',
        'value' => json_encode(['value' => (int)$value]),
    ]);

});

it('donates via donation invoice', function () {
    bot()
        ->hearUpdateType(UpdateTypes::PRE_CHECKOUT_QUERY)
        ->reply()
        ->assertReply('answerPreCheckoutQuery')
        ->clearCache()
        ->hearMessage(['successful_payment' => []])
        ->reply()
        ->assertReplyText(__('donate.thanks'));

    $this->assertDatabaseHas('statistics', [
        'action' => 'precheckout',
        'category' => 'payment',
    ]);

    $this->assertDatabaseHas('statistics', [
        'action' => 'donation',
        'category' => 'payment',
    ]);
});

it('clicks on a third-party donation button', function () {
    $providerKey = array_key_first(config('donation.third_party_providers.text'));
    $providerValue = Arr::first(config('donation.third_party_providers.text'));

    ImageUtils::shouldReceive('qrcode')->andReturn('test.png');

    $this->test
        ->hearCallbackQueryData($providerKey)
        ->reply()
        ->assertReplyMessage([
            'caption' => message('donate.third', [
                'service' => $providerKey,
                'text' => $providerValue,
            ]),
        ], 0, 'sendPhoto')
        ->assertReply('deleteMessage', index: 1)
        ->assertReply('answerCallbackQuery', index: 2);

    $this->assertDatabaseHas('statistics', [
        'action' => 'donate.third',
        'category' => 'donation',
        'value' => json_encode(['service' => $providerKey]),
    ]);
});

it('closes donate menu', function () {
    $this->test
        ->hearCallbackQueryData('donate.cancel')
        ->reply()
        ->assertReply('deleteMessage')
        ->assertReply('answerCallbackQuery', index: 1);

    $this->assertDatabaseHas('statistics', [
        'action' => 'donate.cancel',
        'category' => 'donation',
    ]);

});
