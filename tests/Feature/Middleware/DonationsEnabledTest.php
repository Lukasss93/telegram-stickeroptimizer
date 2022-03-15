<?php

use App\Telegram\Middleware\DonationsEnabled;
use SergiX44\Nutgram\Nutgram;

beforeEach(function () {
    bot()
        ->overrideMiddleware([])
        ->onText('foo', fn (Nutgram $bot) => $bot->sendMessage('bar'))
        ->middleware(DonationsEnabled::class);
});

it('blocks user', function () {
    config()->set('donation.enabled', false);

    bot()
        ->hearText('foo')
        ->reply()
        ->assertNoReply();
});

it('allows user', function () {
    config()->set('donation.enabled', true);

    bot()
        ->hearText('foo')
        ->reply()
        ->assertReplyText('bar');
});
