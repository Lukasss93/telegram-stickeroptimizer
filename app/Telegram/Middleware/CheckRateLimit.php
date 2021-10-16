<?php

namespace App\Telegram\Middleware;

use Illuminate\Support\Facades\RateLimiter;
use SergiX44\Nutgram\Nutgram;

class CheckRateLimit
{
    protected string $key = 'bot-rate';

    public function __invoke(Nutgram $bot, $next): void
    {
        if (!config('bot.rate_limit.enabled')) {
            $next($bot);

            return;
        }

        $rateLimitKey = $this->getRateLimitKey($bot->userId());

        $executed = RateLimiter::attempt(
            $rateLimitKey,
            config('bot.rate_limit.attempts'),
            fn () => $next($bot),
            config('bot.rate_limit.decay'),
        );

        if (!$executed) {
            $seconds = RateLimiter::availableIn($rateLimitKey);

            $message = "Too many messages sent!\nYou may try again in $seconds seconds.";

            if ($bot->isCallbackQuery()) {
                $bot->answerCallbackQuery([
                    'text' => $message,
                    'show_alert' => true,
                ]);

                return;
            }

            $bot->sendMessage($message);

            return;
        }
    }

    protected function getRateLimitKey(int $userID): string
    {
        return $this->key.':'.$userID;
    }
}
