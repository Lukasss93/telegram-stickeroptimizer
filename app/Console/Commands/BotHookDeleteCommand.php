<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use SergiX44\Nutgram\Nutgram;

class BotHookDeleteCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'bot:hook:delete';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Delete the bot webhook';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle(): int
    {
        /** @var Nutgram $bot */
        $bot = app(Nutgram::class);

        $bot->deleteWebhook();

        return 0;
    }
}
