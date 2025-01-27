<?php

namespace App\Console\Commands;

use App\Enums\Stats;
use Illuminate\Console\Command;

class UpdateBotStatsCommand extends Command
{
    protected $signature = 'stats:update';

    protected $description = 'Update bot statistics cache';

    public function handle(): int
    {
        $this->warn('Updating bot stats...');

        Stats::cache();

        $this->info('Bot stats updated successfully.');

        return 0;
    }
}
