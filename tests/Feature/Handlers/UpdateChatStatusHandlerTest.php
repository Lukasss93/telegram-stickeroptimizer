<?php

use SergiX44\Nutgram\Telegram\Attributes\ChatMemberStatus;
use SergiX44\Nutgram\Telegram\Attributes\UpdateTypes;

it('updates chat status: member', function () {

    bot()
        ->hearUpdateType(UpdateTypes::MY_CHAT_MEMBER, [
            'chat' => ['id' => 321],
            'from' => ['id' => 321],
            'new_chat_member' => ['status' => ChatMemberStatus::MEMBER],
        ])
        ->reply()
        ->assertNoReply();

    $this->assertDatabaseHas('chats', [
        'chat_id' => 321,
        'blocked_at' => null,
    ]);

    $this->assertDatabaseHas('statistics', [
        'action' => 'chat.unblocked',
        'category' => 'chat status',
    ]);

});

it('updates chat status: banned', function () {
    $now = now()->addMinute();
    $this->travelTo($now);

    bot()
        ->hearUpdateType(UpdateTypes::MY_CHAT_MEMBER, [
            'chat' => ['id' => 321],
            'from' => ['id' => 321],
            'new_chat_member' => ['status' => ChatMemberStatus::KICKED],
        ])
        ->reply()
        ->assertNoReply();

    $this->assertDatabaseHas('chats', [
        'chat_id' => 321,
        'blocked_at' => $now->toDateTimeString(),
    ]);

    $this->assertDatabaseHas('statistics', [
        'action' => 'chat.blocked',
        'category' => 'chat status',
    ]);

    $this->travelBack();

});
