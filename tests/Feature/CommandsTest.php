<?php

use Illuminate\Support\Facades\Cache;
use SergiX44\Nutgram\Telegram\Properties\ParseMode;
use SergiX44\Nutgram\Telegram\Properties\UpdateType;
use SergiX44\Nutgram\Telegram\Types\Keyboard\InlineKeyboardButton;
use SergiX44\Nutgram\Telegram\Types\Keyboard\InlineKeyboardMarkup;
use SergiX44\Nutgram\Telegram\Types\Keyboard\ReplyKeyboardRemove;

it('sends /start command', function () {
    bot()
        ->hearText('/start')
        ->reply()
        ->assertReplyText(message('start'));

    $this->assertDatabaseHas('statistics', [
        'action' => 'command.start',
    ]);
});

it('sends /help command', function () {
    bot()
        ->hearText('/help')
        ->reply()
        ->assertReplyText(message('start'));

    $this->assertDatabaseHas('statistics', [
        'action' => 'command.help',
    ]);
});

it('sends /about command', function () {
    bot()
        ->hearText('/about')
        ->reply()
        ->assertReplyText(message('about'));

    $this->assertDatabaseHas('statistics', [
        'action' => 'command.about',
    ]);
});

it('sends /stats command with filled content', function () {
    $stats = [
        'stickers_optimized' => [
            'yesterday' => 0,
            'today' => 0,
            'week' => 0,
            'month' => 0,
            'year' => 0,
            'total' => 0,
        ],
        'videos_optimized' => [
            'yesterday' => 0,
            'today' => 0,
            'week' => 0,
            'month' => 0,
            'year' => 0,
            'total' => 0,
        ],
        'active_users' => [
            'yesterday' => 0,
            'today' => 0,
            'week' => 0,
            'month' => 0,
            'year' => 0,
        ],
        'users' => [
            'yesterday' => 0,
            'today' => 0,
            'week' => 0,
            'month' => 0,
            'year' => 0,
            'total' => 0,
        ],
        'last_update' => '0',
    ];

    Cache::put('stats', $stats);

    bot()
        ->hearText('/stats')
        ->reply()
        ->assertReplyText(message('stats.template', [
            'title' => __('stats.category.optimized.stickers'),
            ...$stats['stickers_optimized'],
            'lastUpdate' => $stats['last_update'],
        ]));

    $this->assertDatabaseHas('statistics', [
        'action' => 'command.stats',
    ]);

    Cache::forget('stats');
});

it('sends /stats command with empty content', function () {
    bot()
        ->hearText('/stats')
        ->reply()
        ->assertReplyText(message('stats.empty'));

    $this->assertDatabaseHas('statistics', [
        'action' => 'command.stats',
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
        'action' => 'command.privacy',
    ]);
});

it('sends /cancel command', function () {
    bot()
        ->hearUpdateType(UpdateType::MESSAGE, [
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
        'action' => 'command.cancel',
    ]);
});
