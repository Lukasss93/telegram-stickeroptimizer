<?php

use App\Jobs\OptimizeStickerJob;
use Illuminate\Support\Facades\Queue;
use SergiX44\Nutgram\Telegram\Properties\UpdateType;

it('creates an optimized sticker from sticker', function () {

    Queue::fake();

    bot()
        ->hearUpdateType(UpdateType::MESSAGE, [
            'message_id' => 1,
            'from' => ['id' => 123],
            'chat' => ['id' => 321],
            'sticker' => [
                'file_id' => 'abc123',
                'file_unique_id' => 'abcdef123',
                'width' => 512,
                'height' => 512,
                'is_animated' => false,
                'is_video' => false,
                'file_size' => 1024,
            ],
        ])
        ->reply();

    Queue::assertPushed(OptimizeStickerJob::class);

    $this->assertDatabaseHas('statistics', [
        'action' => 'handler.sticker',
    ]);

});

it('does not creates an optimized sticker from an animated sticker', function () {

    Queue::fake();

    bot()
        ->hearUpdateType(UpdateType::MESSAGE, [
            'message_id' => 1,
            'from' => ['id' => 123],
            'chat' => ['id' => 321],
            'sticker' => [
                'file_id' => 'abc123',
                'file_unique_id' => 'abcdef123',
                'width' => 512,
                'height' => 512,
                'is_animated' => true,
                'is_video' => false,
                'file_size' => 1024,
            ],
        ])
        ->reply()
        ->assertReplyText(trans('common.animated_not_supported'));

    Queue::assertNotPushed(OptimizeStickerJob::class);

    $this->assertDatabaseMissing('statistics', [
        'action' => 'handler.sticker',
    ]);

});
