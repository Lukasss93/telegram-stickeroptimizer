<?php

namespace Tests\Feature;

use App\Jobs\SendNews;
use App\Models\Chat;
use Exception;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Queue;
use SergiX44\Nutgram\Nutgram;
use SergiX44\Nutgram\Telegram\Exceptions\TelegramException;
use SergiX44\Nutgram\Telegram\Types\Message;
use Tests\TestCase;

class SendNewsJobTest extends TestCase
{
    use DatabaseTransactions;

    public function testSendNewsDispatched(): void
    {
        Queue::fake();
        SendNews::dispatch(12345689, 123);
        Queue::assertPushedOn('news', SendNews::class);
    }

    public function testSendNewsRun(): void
    {
        $chat = Chat::create(['chat_id' => 123456789, 'type' => 'private', 'first_name' => 'Test User']);

        $this->mock(Nutgram::class)
            ->shouldReceive('forwardMessage')
            ->andReturn(new Message());

        SendNews::dispatch($chat->chat_id, 123);

        $this->assertDatabaseHas('chats', ['chat_id' => $chat->chat_id]);

    }

    public function testSendNewsThrowsTelegramExceptionUserIsDeactivated(): void
    {
        $chat = Chat::create(['chat_id' => 123456789, 'type' => 'private', 'first_name' => 'Test User']);

        $this->mock(Nutgram::class)
            ->shouldReceive('forwardMessage')
            ->andThrowExceptions([new TelegramException('Forbidden: user is deactivated')]);

        SendNews::dispatch($chat->chat_id, 123);

        $this->assertDatabaseMissing('chats', ['chat_id' => $chat->chat_id]);

    }

    public function testSendNewsThrowsTelegramExceptionBotBlockedByUser(): void
    {
        $chat = Chat::create(['chat_id' => 123456789, 'type' => 'private', 'first_name' => 'Test User']);

        $this->mock(Nutgram::class)
            ->shouldReceive('forwardMessage')
            ->andThrowExceptions([new TelegramException('Forbidden: bot was blocked by the user')]);

        SendNews::dispatch($chat->chat_id, 123);

        $this->assertDatabaseHas('chats', ['chat_id' => $chat->chat_id]);

    }

    public function testSendNewsThrowsException(): void
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Another exception');

        $chat = Chat::create(['chat_id' => 123456789, 'type' => 'private', 'first_name' => 'Test User']);

        $this->mock(Nutgram::class)
            ->shouldReceive('forwardMessage')
            ->andThrowExceptions([new Exception('Another exception')]);

        SendNews::dispatch($chat->chat_id, 123);

        $this->assertDatabaseHas('chats', ['chat_id' => $chat->chat_id]);

    }
}
