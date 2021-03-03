<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use SergiX44\Nutgram\Nutgram;

class BotHookInfoCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'bot:hook:info';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Get the bot webhook info';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle(): int
    {
        /** @var Nutgram $bot */
        $bot = app(Nutgram::class);

        $response=$bot->getWebhookInfo();

        $this->table(['Info', 'Value'], [
            ['url', $response->url,],
            ['has_custom_certificate', $response->has_custom_certificate,],
            ['pending_update_count', $response->pending_update_count,],
            ['ip_address', $response->ip_address,],
            ['last_error_date', $response->last_error_date,],
            ['last_error_message', $response->last_error_message,],
            ['max_connections', $response->max_connections,],
            ['allowed_updates', $response->allowed_updates,],
        ]);

        return 0;
    }
}
