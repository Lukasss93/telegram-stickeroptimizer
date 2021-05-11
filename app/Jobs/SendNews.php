<?php

namespace App\Jobs;

use App\Jobs\Middleware\RateLimited;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use SergiX44\Nutgram\Nutgram;

class SendNews implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private string $chat_id;
    private int $message_id;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(string $chat_id, int $message_id)
    {
        $this->onQueue(config('bot.news.queue'));
        $this->chat_id = $chat_id;
        $this->message_id = $message_id;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(): void
    {
        /** @var Nutgram $bot */
        $bot = app(Nutgram::class);

        $bot->forwardMessage($this->chat_id, config('bot.news.channel_id'), $this->message_id);
    }

    /**
     * Get the middleware the job should pass through.
     *
     * @return array
     */
    public function middleware(): array
    {
        return [new RateLimited(config('bot.news.rate'))];
    }
}
