<?php

use App\Jobs\OptimizeStickerJob;
use SergiX44\Nutgram\Telegram\Attributes\UpdateTypes;

it('creates an optimized sticker from document', function () {

    Queue::fake();

    bot()
        ->hearUpdateType(UpdateTypes::MESSAGE, [
            'message_id' => 1,
            'from' => ['id' => 123],
            'chat' => ['id' => 321],
            'document' => [
                'file_id' => 'abc123',
                'file_unique_id' => 'abcdef123',
                'width' => 1000,
                'height' => 500,
                'file_size' => 2048,
            ],
        ])
        ->reply();

    Queue::assertPushed(OptimizeStickerJob::class);

    $this->assertDatabaseHas('statistics', [
        'action' => 'document',
        'category' => 'handler',
    ]);

});
