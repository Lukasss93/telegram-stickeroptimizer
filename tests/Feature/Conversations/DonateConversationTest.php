<?php

use App\Facades\ImageUtils;
use SergiX44\Nutgram\Telegram\Properties\UpdateType;
use SergiX44\Nutgram\Telegram\Types\Keyboard\InlineKeyboardButton;
use SergiX44\Nutgram\Telegram\Types\Keyboard\InlineKeyboardMarkup;

beforeEach(function () {
    $this->test = bot()
        ->willStartConversation()
        ->hearText('/donate')
        ->reply()
        ->assertReplyMessage([
            'text' => message('donate.main'),
        ])
        ->assertActiveConversation();

    $this->assertDatabaseHas('statistics', [
        'action' => 'command.donate',
    ]);
});

it('sends /donate command', function () {
    $this->test;
});

it('get amount', function () {
    $this->test
        ->hearText('93')
        ->reply()
        ->assertReply('sendInvoice')
        ->assertNoConversation();


    $this->assertDatabaseHas('statistics', [
        'action' => 'donate.invoice',
        'value' => json_encode(['value' => 93]),
    ]);

});

it('get invalid amount', function () {
    $this->test
        ->hearText('foo')
        ->reply()
        ->assertReplyText(message('donate.invalid'))
        ->assertActiveConversation();
});
