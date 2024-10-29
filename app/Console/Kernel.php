<?php

namespace App\Console;

use Log;
use Carbon\Carbon;
use App\Models\Exam;
use App\Models\Student;
use App\Models\StudentExam;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     */
    // protected function schedule(Schedule $schedule): void
    // {
    //     // $schedule->command('inspire')->hourly();
    //     // $schedule->command('queue:work')->everyMinute(1);
    //     // $schedule->command('queue:restart')->everyMinute(5);
    // }

    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }

    protected function schedule(Schedule $schedule)
    {


        $schedule->command('app:mark-missed-exams')->dailyAt('03:00');
        // $schedule->command('app:mark-missed-exams')->everyMinute();
    }

}
