<?php

namespace App\Console;

use App\Console\Commands\UpdateBotStatsCommand;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        //
    ];

    /**
     * Define the application's command schedule.
     *
     * @param Schedule $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        //update the bot stattistics
        $schedule
            ->command(UpdateBotStatsCommand::class)
            ->everyFiveMinutes();

        //delete too old backups
        $schedule
            ->command('backup:clean')
            ->when(fn () => config('backup.enabled'))
            ->evenInMaintenanceMode()
            ->dailyAt(config('backup.clean_at'));

        //save database backup
        $schedule
            ->command('backup:run', ['--only-db' => true])
            ->when(fn () => config('backup.enabled'))
            ->evenInMaintenanceMode()
            ->dailyAt(config('backup.run_at'));
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
