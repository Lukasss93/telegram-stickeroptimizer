<?php

use App\Enums\TelegramLimit;
use App\Jobs\OptimizeStickerJob;
use App\Models\Chat;
use GuzzleHttp\Psr7\Request;
use SergiX44\Nutgram\Telegram\Attributes\ParseMode;
use SergiX44\Nutgram\Testing\FormDataParser;
use SergiX44\Nutgram\Testing\OutgoingResource;

it('fails with too large file', function () {
    OptimizeStickerJob::dispatch(
        chatID: 123,
        replyID: 456,
        fileID: 'abcdef',
        fileSize: 50000000
    );

    bot()->assertReplyMessage([
        'chat_id' => 123,
        'text' => trans('common.too_large_file'),
        'reply_to_message_id' => 456,
        'allow_sending_without_reply' => true,
    ]);
});

it('fails with invalid file', function () {
    partialMockBot(function ($mock) {
        $mock->shouldReceive('getFile')->andReturn(null);
    });

    OptimizeStickerJob::dispatch(
        chatID: 123,
        replyID: 456,
        fileID: 'abcdef',
        fileSize: 5000
    );

    bot()->assertReplyText(trans('common.invalid_file'));
});

it('fails with an animated webp', function () {

    $file = mockFile('Images/animated.webp');

    partialMockBot(function ($mock) use ($file) {
        $mock->shouldReceive('getFile')->andReturn($file);
    });

    OptimizeStickerJob::dispatch(
        chatID: 123,
        replyID: 456,
        fileID: 'abcdef',
        fileSize: $file->file_size,
    );

    bot()
        ->assertReply('sendChatAction')
        ->assertReplyText(trans('common.invalid_file'), 1);
});

it('passes with a valid image', function () {
    bot()->setData(Chat::class, $this->chat);

    $file = mockFile('Images/venice.jpg');

    partialMockBot(function ($mock) use ($file) {
        $mock->shouldReceive('getFile')->andReturn($file);
    });

    OptimizeStickerJob::dispatch(
        chatID: $this->chat->chat_id,
        replyID: 456,
        fileID: 'abcdef',
        fileSize: $file->file_size,
    );

    bot()
        ->assertReply('sendChatAction')
        ->assertReply('sendDocument', [
            'caption' => message('donate.caption'),
            'parse_mode' => ParseMode::HTML,
            'chat_id' => $this->chat->chat_id,
            'reply_to_message_id' => 456,
            'allow_sending_without_reply' => true,
        ], 1)
        ->assertRaw(function (Request $request) {
            /** @var OutgoingResource $document */
            $document = FormDataParser::parse($request)->files['document'];

            //check sticker size
            return $document->getSize() <= TelegramLimit::STICKER_MAX_SIZE->value;
        }, 1);

    $this->assertDatabaseHas('statistics', [
        'action' => 'sticker',
        'category' => 'optimized',
    ]);
});

it('passes with a valid image + watermark', function () {
    $this->chat->settings()->set('watermark.opacity', 100);

    bot()->setData(Chat::class, $this->chat);

    $file = mockFile('Images/venice.jpg');

    partialMockBot(function ($mock) use ($file) {
        $mock->shouldReceive('getFile')->andReturn($file);
    });

    OptimizeStickerJob::dispatch(
        chatID: $this->chat->chat_id,
        replyID: 456,
        fileID: 'abcdef',
        fileSize: $file->file_size,
    );

    bot()
        ->assertReply('sendChatAction')
        ->assertReply('sendDocument', [
            'caption' => message('donate.caption'),
            'parse_mode' => ParseMode::HTML,
            'chat_id' => $this->chat->chat_id,
            'reply_to_message_id' => 456,
            'allow_sending_without_reply' => true,
        ], 1)
        ->assertRaw(function (Request $request) {
            /** @var OutgoingResource $document */
            $document = FormDataParser::parse($request)->files['document'];

            //check sticker size
            return $document->getSize() <= TelegramLimit::STICKER_MAX_SIZE->value;
        }, 1);

    $this->assertDatabaseHas('statistics', [
        'action' => 'sticker',
        'category' => 'optimized',
    ]);
});
