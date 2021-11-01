<?php

namespace App\Jobs;

use App\Exceptions\TelegramUserBlockedException;
use App\Exceptions\TelegramUserDeactivatedException;
use App\Jobs\Middleware\RateLimited;
use App\Models\Chat;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use SergiX44\Nutgram\Nutgram;
use Throwable;

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
        $this->onQueue('news');
        $this->chat_id = $chat_id;
        $this->message_id = $message_id;
    }

    /**
     * Execute the job.
     *
     * @return void
     * @throws Throwable
     */
    public function handle(): void
    {
        /** @var Nutgram $bot */
        $bot = app(Nutgram::class);

        try {
            //forward message
            $bot->forwardMessage($this->chat_id, config('bot.channel'), $this->message_id);
        } catch (TelegramUserBlockedException) {
            Chat::where('chat_id', $this->chat_id)->update(['blocked_at' => now()]);
        } catch (TelegramUserDeactivatedException) {
            Chat::find($this->chat_id)?->delete();
        }
    }

    /**
     * Get the middleware the job should pass through.
     *
     * @return array
     */
    public function middleware(): array
    {
        return [new RateLimited(2000)];
    }
}
