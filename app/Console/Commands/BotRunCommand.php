<?php

namespace App\Console\Commands;

use DI\DependencyException;
use DI\NotFoundException;
use Illuminate\Console\Command;
use SergiX44\Nutgram\Nutgram;

class BotRunCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'bot:run';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Start the bot in long polling mode';

    /**
     * Execute the console command.
     *
     * @return int
     * @throws DependencyException
     * @throws NotFoundException
     */
    public function handle(): int
    {
        app(Nutgram::class)->run();
    }
}
