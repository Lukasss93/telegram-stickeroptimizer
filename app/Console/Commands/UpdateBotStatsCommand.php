<?php

namespace App\Console\Commands;

use App\Models\Statistic;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;

class UpdateBotStatsCommand extends Command
{
    protected $signature = 'stats:update';

    protected $description = 'Update bot statistics cache';

    public function handle(): int
    {
        $this->warn('Updating bot stats...');

        Cache::put('stats', Statistic::getStatsForBot());

        $this->info('Bot stats updated successfully.');

        return 0;
    }
}
