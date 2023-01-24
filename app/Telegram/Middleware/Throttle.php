<?php

namespace App\Telegram\Middleware;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\RateLimiter;
use SergiX44\Nutgram\Nutgram;

class Throttle
{
    protected const CACHE_KEY = 'throttle';

    protected string $key;
    protected int $attempts;
    protected int $decay;

    public function __construct(int $attempts = 5, int $decay = 1, string $key = 'default')
    {
        $this->attempts = $attempts;
        $this->decay = $decay;
        $this->key = $key;
    }

    public static function with(int $attempts, int $decay = 1, string $key = 'default'): self
    {
        return new self($attempts, $decay, $key);
    }

    public function as(string $key): self
    {
        $this->key = $key;

        return $this;
    }

    protected function getRateLimitKey(int|string $userID): string
    {
        return self::CACHE_KEY.':'.$this->key.':'.$userID;
    }

    public function __invoke(Nutgram $bot, $next): void
    {
        // if user id is not set, skip this middleware
        if ($bot->userId() === null) {
            $next($bot);

            return;
        }

        // if rate limit is disabled, skip this middleware
        if (!config('bot.rate_limit.enabled')) {
            $next($bot);

            return;
        }

        // get the rate limit key
        $rateLimitKey = $this->getRateLimitKey($bot->userId());

        // use the rate limiter
        $executed = RateLimiter::attempt(
            key: $rateLimitKey,
            maxAttempts: $this->attempts,
            callback: fn () => $next($bot),
            decaySeconds: $this->decay,
        );

        if($executed) {
            // if the rate limit is not exceeded, remove the cache key
            Cache::forget($rateLimitKey.':exceeded');
        }

        // if the rate limit is exceeded, send a message to the user only once
        if (!$executed && !Cache::has($rateLimitKey.':exceeded')) {

            // get the number of seconds until the rate limit is reset
            $seconds = RateLimiter::availableIn($rateLimitKey);

            // if the message is a callback query, answer it
            if ($bot->isCallbackQuery()) {
                $bot->answerCallbackQuery();
            }

            // send a message to the user
            $bot->sendMessage("Too many messages sent!\nYou may try again in $seconds seconds.");

            // set the cache key to prevent sending the message again
            Cache::put($rateLimitKey.':exceeded', 1);
        }
    }
}
