<?php

use App\Models\Chat;
use App\Telegram\Middleware\CheckMaintenance;
use SergiX44\Nutgram\Nutgram;

beforeEach(function () {
    $this->artisan('down');

    bot()->overrideMiddleware(CheckMaintenance::class);
    bot()->onText('foo', fn (Nutgram $bot) => $bot->sendMessage('bar'));
    bot()->onCallbackQueryData('aaa', fn (Nutgram $bot) => $bot->sendMessage('bbb'));
});

afterEach(function () {
    $this->artisan('up');
});

it('checks maintenance is off', function () {
    $this->artisan('up');

    bot()
        ->hearText('foo')
        ->reply()
        ->assertReplyText('bar');
});

it('checks maintenance is on', function () {
    bot()->setData(Chat::class, $this->chat);

    bot()
        ->hearText('foo')
        ->reply()
        ->assertReplyText(message('maintenance'));
});

it('checks maintenance is on but user is developer', function () {
    config()->set('developer.id', $this->chat->chat_id);
    bot()->setData(Chat::class, $this->chat);

    bot()
        ->hearMessage([
            'chat' => ['id' => 123456789],
            'from' => ['id' => 123456789],
            'text' => 'foo',
        ])
        ->reply()
        ->assertReplyText('<b>⚠ MAINTENANCE MODE ENABLED ⚠</b>')
        ->assertReplyText('bar', 1);
});

it('checks maintenance is on but bot hears a callback query', function () {
    bot()->setData(Chat::class, $this->chat);

    bot()
        ->hearCallbackQueryData('aaa')
        ->reply()
        ->assertReply('answerCallbackQuery', [
            'text' => trans('maintenance.title'),
            'show_alert' => true,
        ]);
});
