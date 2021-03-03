<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use SergiX44\Nutgram\Nutgram;

class BotHookSetCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'bot:hook:set {url?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Set the bot webhook';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle(): int
    {
        /** @var Nutgram $bot */
        $bot = app(Nutgram::class);

        $this->warn('Setting webhook to ' . ($this->argument('url') ?? 'NONE'));

        $bot->deleteWebhook();

        if ($this->argument('url') !== null) {
            $bot->setWebhook($this->argument('url'), [
                'max_connections' => 100
            ]);
        }

        $this->call(BotHookInfoCommand::class);

        return 0;
    }
}
