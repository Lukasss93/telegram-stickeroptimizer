<?php

use App\Models\Chat;
use App\Telegram\Middleware\SetLocale;
use SergiX44\Nutgram\Nutgram;

beforeEach(function () {
    bot()->onText('foo', function (Nutgram $bot) {
        $bot->sendMessage('bar');
    });
});

it('sets locale from chat settings', function () {
    $chat = Chat::factory()->create();
    $chat->settings()->set('language', 'de');
    $chat->refresh();

    bot()->setData(Chat::class, $chat);

    bot()
        ->overrideMiddleware(SetLocale::class)
        ->hearText('foo')
        ->reply();

    expect(app()->getLocale())->toBe('de');
});

it('sets locale from fallback', function () {
    $chat = Chat::factory()->create();

    bot()->setData(Chat::class, $chat);

    bot()
        ->overrideMiddleware(SetLocale::class)
        ->hearText('foo')
        ->reply();

    expect(app()->getLocale())->toBe(config('app.locale'));
});
