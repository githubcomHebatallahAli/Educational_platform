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
        // $schedule->call(function () {
        //     Log::info('Scheduler is running successfully');
        //     // جلب كل الامتحانات التي انتهت
        //     $exams = Exam::whereNotNull('deadLineExam')->get();

        //     foreach ($exams as $exam) {
        //         // حساب تاريخ 3 أيام بعد نهاية الامتحان
        //         $threeDaysAfterEndDate = Carbon::parse($exam->deadLineExam)->addDays(3);
        //         $currentDate = Carbon::now();

        //         // إذا مرت 3 أيام على نهاية الامتحان
        //         if ($currentDate->greaterThanOrEqualTo($threeDaysAfterEndDate)) {
        //             // جلب الطلاب الذين لم يقدموا الامتحان
        //             $studentsWithoutAnswers = Student::whereDoesntHave('answers', function ($query) use ($exam) {
        //                 $query->where('exam_id', $exam->id);
        //             })->get();

        //             foreach ($studentsWithoutAnswers as $student) {
        //                 // تحديث جدول student_exams لتسجيل الغياب
        //                 StudentExam::updateOrCreate(
        //                     ['student_id' => $student->id, 'exam_id' => $exam->id],
        //                     ['score' => 'Absent', 'has_attempted' => false]
        //                 );
        //             }
        //         }
        //     }
        // })->everyMinute();  // تنفيذ المهمة كل دقيقه

        $schedule->command('app:mark-missed-exams')->dailyAt('03:00');
    }

}
