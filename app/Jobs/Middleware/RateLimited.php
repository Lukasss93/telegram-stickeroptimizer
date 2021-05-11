<?php

namespace App\Jobs\Middleware;

class RateLimited
{
    private $ms;

    public function __construct($ms)
    {
        $this->ms = $ms;
    }

    /**
     * Process the queued job.
     *
     * @param mixed $job
     * @param callable $next
     * @return mixed
     */
    public function handle($job, $next)
    {
        usleep($this->ms * 1000);
        $next($job);
    }
}
