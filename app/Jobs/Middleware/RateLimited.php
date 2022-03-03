<?php

namespace App\Jobs\Middleware;

class RateLimited
{
    private int $ms;

    public function __construct(int $ms)
    {
        $this->ms = $ms;
    }

    /**
     * Process the queued job.
     *
     * @param mixed $job
     * @param callable $next
     */
    public function handle(mixed $job, callable $next): void
    {
        if(!app()->runningUnitTests()){
            usleep($this->ms * 1000);
        }

        $next($job);
    }
}
