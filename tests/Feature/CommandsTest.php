<?php

use Illuminate\Support\Facades\Cache;
use SergiX44\Nutgram\Telegram\Attributes\ParseMode;
use SergiX44\Nutgram\Telegram\Attributes\UpdateTypes;
use SergiX44\Nutgram\Telegram\Types\Keyboard\InlineKeyboardButton;
use SergiX44\Nutgram\Telegram\Types\Keyboard\InlineKeyboardMarkup;
use SergiX44\Nutgram\Telegram\Types\Keyboard\ReplyKeyboardRemove;

it('sends /start command', function () {
    bot()
        ->hearText('/start')
        ->reply()
        ->assertReplyText(message('start'));

    $this->assertDatabaseHas('statistics', [
        'action' => 'start',
        'category' => 'command',
    ]);
});

it('sends /help command', function () {
    bot()
        ->hearText('/help')
        ->reply()
        ->assertReplyText(message('start'));

    $this->assertDatabaseHas('statistics', [
        'action' => 'help',
        'category' => 'command',
    ]);
});

it('sends /about command', function () {
    bot()
        ->hearText('/about')
        ->reply()
        ->assertReplyText(message('about'));

    $this->assertDatabaseHas('statistics', [
        'action' => 'about',
        'category' => 'command',
    ]);
});

it('sends /stats command with filled content', function () {
    $stats = [
        'stickers_new' => 1,
        'stickers_total' => 2,
        'users_new_today' => 3,
        'users_active_today' => 4,
        'users_total' => 5,
        'last_update' => 6,
    ];

    Cache::put('stats', $stats);

    bot()
        ->hearText('/stats')
        ->reply()
        ->assertReplyText(message('stats.full', $stats));

    $this->assertDatabaseHas('statistics', [
        'action' => 'stats',
        'category' => 'command',
    ]);

    Cache::forget('stats');
});

it('sends /stats command with empty content', function () {
    bot()
        ->hearText('/stats')
        ->reply()
        ->assertReplyText(message('stats.empty'));

    $this->assertDatabaseHas('statistics', [
        'action' => 'stats',
        'category' => 'command',
    ]);
});

it('sends /privacy command', function () {
    bot()
        ->hearText('/privacy')
        ->reply()
        ->assertReplyMessage([
            'text' => message('privacy'),
            'parse_mode' => ParseMode::HTML,
            'disable_web_page_preview' => true,
            'reply_markup' => InlineKeyboardMarkup::make()
                ->addRow(InlineKeyboardButton::make(trans('privacy.title'), config('bot.privacy'))),
        ]);

    $this->assertDatabaseHas('statistics', [
        'action' => 'privacy',
        'category' => 'command',
    ]);
});

it('sends /cancel command', function () {
    bot()
        ->hearUpdateType(UpdateTypes::MESSAGE, [
            'text' => '/cancel',
            'from' => ['id' => 123],
            'chat' => ['id' => 321],
        ])
        ->reply()
        ->assertNoConversation(123, 321)
        ->assertCalled('sendMessage')
        ->assertReplyMessage([
            'text' => 'Removing keyboard...',
            'reply_markup' => ReplyKeyboardRemove::make(true),
        ])
        ->assertCalled('deleteMessage');

    $this->assertDatabaseHas('statistics', [
        'action' => 'cancel',
        'category' => 'command',
    ]);
});
