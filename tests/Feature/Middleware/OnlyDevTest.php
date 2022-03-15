<?php

use App\Telegram\Middleware\OnlyDev;
use SergiX44\Nutgram\Nutgram;

beforeEach(function () {
    bot()
        ->overrideMiddleware([])
        ->onText('foo', fn (Nutgram $bot) => $bot->sendMessage('bar'))
        ->middleware(OnlyDev::class);
});

it('blocks user', function () {
    bot()
        ->hearText('foo')
        ->reply()
        ->assertNoReply();
});

it('allows user', function () {
    config()->set('developer.id', 123456789);

    bot()
        ->hearMessage([
            'chat' => ['id' => 123456789],
            'from' => ['id' => 123456789],
            'text' => 'foo',
        ])
        ->reply()
        ->assertReplyText('bar');
});
