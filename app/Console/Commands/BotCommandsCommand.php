<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Collection;
use SergiX44\Nutgram\Nutgram;

class BotCommandsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'bot:commands';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Build the bot commands';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle(): int
    {
        $this->warn('Building commands...');

        /** @var Nutgram $bot */
        $bot = app(Nutgram::class);

        //get bot commands and remove /donate if disabled
        $commands = collect(config('telegram.bot.commands', []))
            ->when(!config('telegram.bot.donations.enabled'), function (Collection $collection) {
                return $collection->reject(fn ($item) => $item['command'] === 'donate');
            });

        //set commands
        $bot->setMyCommands($commands->toArray());

        $this->info('Done.');

        return 0;
    }
}
