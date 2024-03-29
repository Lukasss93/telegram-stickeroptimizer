<?php

use SergiX44\Nutgram\Telegram\Properties\UpdateType;
use SergiX44\Nutgram\Telegram\Types\Keyboard\InlineKeyboardButton;
use SergiX44\Nutgram\Telegram\Types\Keyboard\InlineKeyboardMarkup;

beforeEach(function () {
    $this->test = bot()
        ->willStartConversation()
        ->hearUpdateType(UpdateType::MESSAGE, [
            'text' => '/feedback',
            'from' => [
                'id' => 123,
                'first_name' => 'foo',
                'last_name' => 'bar',
                'username' => 'foobar',
            ],
            'chat' => ['id' => 321],
        ])
        ->reply()
        ->assertReplyMessage([
            'text' => message('feedback.ask'),
            'reply_markup' => InlineKeyboardMarkup::make()
                ->addRow(InlineKeyboardButton::make(
                    text: trans('common.cancel'),
                    callback_data: 'feedback.cancel'
                )),
        ])
        ->assertActiveConversation();
});

it('sends /feedback command', function () {
    $this->test;
});

it('gets feedback', function () {
    config(['developer.id' => 321]);

    $this->test
        ->hearText('wow')
        ->reply()
        ->assertReplyMessage([
            'text' => message('feedback.received', [
                'from' => "foo bar",
                'username' => 'foobar',
                'user_id' => 123,
                'message' => 'wow',
            ]),
            'chat_id' => 321,
        ])
        ->assertReply('deleteMessage', index: 1)
        ->assertReplyText(message('feedback.thanks'), 2)
        ->assertNoConversation();

    $this->assertDatabaseHas('statistics', [
        'action' => 'feedback.sent',
    ]);
});

it('gets invalid feedback + it cancels feedback', function () {
    $this->test
        ->hearMessage(['document' => []])
        ->reply()
        ->assertReplyText(message('feedback.wrong'))
        ->assertReplyMessage([
            'text' => message('feedback.ask'),
            'reply_markup' => InlineKeyboardMarkup::make()
                ->addRow(InlineKeyboardButton::make(
                    text: trans('common.cancel'),
                    callback_data: 'feedback.cancel'
                )),
        ], 1)
        ->assertActiveConversation()
        ->hearCallbackQueryData('feedback.cancel')
        ->reply()
        ->assertReply('answerCallbackQuery')
        ->assertReply('deleteMessage', index: 1)
        ->assertReplyText(message('feedback.cancelled'), 2)
        ->assertNoConversation();

    $this->assertDatabaseHas('statistics', [
        'action' => 'feedback.cancelled',
    ]);
});
