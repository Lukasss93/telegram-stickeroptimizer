<?php

use App\Telegram\Middleware\CollectChat;
use Illuminate\Support\Carbon;
use SergiX44\Nutgram\Nutgram;
use SergiX44\Nutgram\Telegram\Attributes\UpdateTypes;

beforeEach(function () {
    bot()->onText('foo', function (Nutgram $bot) {
        $bot->sendMessage('bar');
    });
});

it('fails if user is unset', function () {
    bot()
        ->overrideMiddleware(CollectChat::class)
        ->hearUpdateType(UpdateTypes::MESSAGE, ['text' => 'foo'])
        ->reply()
        ->assertNoReply();
});

it('passes if chat type is not private', function () {
    bot()
        ->overrideMiddleware(CollectChat::class)
        ->hearMessage([
            'chat' => ['type' => 'group'],
            'from' => ['id' => 1234567890],
            'text' => 'foo',
        ])
        ->reply()
        ->assertReplyText('bar');

    $this->assertDatabaseHas('chats', [
        'chat_id' => 1234567890,
        'started_at' => null,
    ]);
});

it('passes if chat type is private', function () {

    $time = now();
    Carbon::setTestNow($time);

    bot()
        ->overrideMiddleware(CollectChat::class)
        ->hearMessage([
            'chat' => ['type' => 'private'],
            'from' => ['id' => 1234567890],
            'text' => 'foo',
        ])
        ->reply()
        ->assertReplyText('bar');

    $this->assertDatabaseHas('chats', [
        'chat_id' => 1234567890,
        'started_at' => $time->toDateTimeString(),
    ]);
});
