<?php

namespace Tests\Feature;

use App\Exceptions\TelegramUserBlockedException;
use App\Exceptions\TelegramUserDeactivatedException;
use App\Jobs\SendNews;
use App\Models\Chat;
use Exception;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Queue;
use SergiX44\Nutgram\Nutgram;
use SergiX44\Nutgram\Telegram\Types\Message\Message;

uses(DatabaseTransactions::class);

it('dispatches job', function () {
    Queue::fake();
    SendNews::dispatch(12345689, 123);
    Queue::assertPushedOn('news', SendNews::class);
});

it('runs job', function () {
    $chat = Chat::create(['chat_id' => 123456789, 'first_name' => 'Test User']);

    $this->mock(Nutgram::class)
        ->shouldReceive('forwardMessage')
        ->andReturn(new Message(bot()));

    SendNews::dispatch($chat->chat_id, 123);

    $this->assertDatabaseHas('chats', ['chat_id' => $chat->chat_id]);
});

it('throws TelegramUserDeactivatedException', function () {
    $chat = Chat::create(['chat_id' => 123456789, 'first_name' => 'Test User']);

    $this->mock(Nutgram::class)
        ->shouldReceive('forwardMessage')
        ->andThrowExceptions([new TelegramUserDeactivatedException('Forbidden: user is deactivated')]);

    SendNews::dispatch($chat->chat_id, 123);

    $this->assertDatabaseMissing('chats', ['chat_id' => $chat->chat_id]);
});

it('throws TelegramUserBlockedException', function () {
    $chat = Chat::create(['chat_id' => 123456789, 'first_name' => 'Test User']);

    $this->mock(Nutgram::class)
        ->shouldReceive('forwardMessage')
        ->andThrowExceptions([new TelegramUserBlockedException('Forbidden: bot was blocked by the user')]);

    SendNews::dispatch($chat->chat_id, 123);

    $this->assertDatabaseHas('chats', ['chat_id' => $chat->chat_id]);
});

it('throws Exception', function () {
    $chat = Chat::create(['chat_id' => 123456789, 'first_name' => 'Test User']);

    $this->mock(Nutgram::class)
        ->shouldReceive('forwardMessage')
        ->andThrowExceptions([new Exception('Another exception')]);

    SendNews::dispatch($chat->chat_id, 123);

    $this->assertDatabaseHas('chats', ['chat_id' => $chat->chat_id]);
})->throws(Exception::class, 'Another exception');
