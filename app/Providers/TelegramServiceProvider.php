<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use SergiX44\Nutgram\Nutgram;

class TelegramServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot(): void
    {
        /** @var Nutgram $bot */
        $bot = $this->app['nutgram'];
        require base_path('routes/telegram.php');
    }
}
