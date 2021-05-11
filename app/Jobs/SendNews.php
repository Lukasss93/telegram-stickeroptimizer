<?php

namespace App\Jobs;

use App\Jobs\Middleware\RateLimited;
use App\Models\Chat;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Str;
use SergiX44\Nutgram\Nutgram;
use SergiX44\Nutgram\Telegram\Exceptions\TelegramException;

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
     * @throws TelegramException
     */
    public function handle(): void
    {
        /** @var Nutgram $bot */
        $bot = app(Nutgram::class);

        try {
            //forward message
            $bot->forwardMessage($this->chat_id, config('bot.news.channel_id'), $this->message_id);
        } catch (TelegramException $e) {
            if (Str::contains($e->getMessage(), 'user is deactivated')) {
                Chat::find($this->chat_id)?->delete();

                return;
            }

            if (!Str::contains($e->getMessage(), ['bot was blocked by the user'])) {
                throw $e;
            }
        }
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
