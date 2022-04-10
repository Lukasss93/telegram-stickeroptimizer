<?php

use App\Models\Chat;
use App\Telegram\Middleware\CheckOffline;
use SergiX44\Nutgram\Nutgram;

beforeEach(function () {
    bot()->setData(Chat::class, $this->chat);

    bot()->overrideMiddleware(CheckOffline::class);
    bot()->onText('foo', fn (Nutgram $bot) => $bot->sendMessage('bar'));
    bot()->onCallbackQueryData('aaa', fn (Nutgram $bot) => $bot->sendMessage('bbb'));
});

it('checks offline is off', function () {
    config()->set('bot.online', true);

    bot()
        ->hearText('foo')
        ->reply()
        ->assertReplyText('bar');
});

it('checks offline is on', function () {
    config()->set('bot.online', false);

    bot()
        ->hearText('foo')
        ->reply()
        ->assertReplyText(message('offline'));
});

it('checks offline is on but user is developer', function () {
    config()->set('bot.online', false);
    config()->set('developer.id', 123456789);

    bot()
        ->hearMessage([
            'chat' => ['id' => 123456789],
            'from' => ['id' => 123456789],
            'text' => 'foo',
        ])
        ->reply()
        ->assertReplyText('<b>⚠ OFFLINE MODE ENABLED ⚠</b>')
        ->assertReplyText('bar', 1);
});

it('checks offline is on but bot hears a callback query', function () {
    config()->set('bot.online', false);

    bot()
        ->hearCallbackQueryData('aaa')
        ->reply()
        ->assertReply('answerCallbackQuery', [
            'text' => trans('maintenance.offline'),
            'show_alert' => true,
        ]);
});
