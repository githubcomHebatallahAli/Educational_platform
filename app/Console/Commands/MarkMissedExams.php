<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use App\Models\Exam;
use App\Models\StudentExam;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class MarkMissedExams extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:mark-missed-exams';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Mark students who missed exams after deadline';

    public function __construct()
    {
        parent::__construct();
    }

//     public function handle()
// {
//     $students = DB::table('student_courses')->where('course_id', 1)->get();
//     foreach ($students as $student) {
//         $this->info('Student ID: ' . $student->user_id);
//     }

//     Exam::whereNotNull('deadLineExam')
//         ->where('deadLineExam', '<', now())
//         ->chunk(100, function ($exams) {
//             foreach ($exams as $exam) {
//                 $this->info('Exam ID: ' . $exam->id);

//                 // الحصول على الطلاب الذين اشتروا الدورة
//                 $students = $exam->course->students; // Assuming you have a relationship in Course model

//                 if ($students->isEmpty()) {
//                     $this->info('No students found for this exam.');
//                 }

//                 foreach ($students as $student) {
//                     // تحقق ما إذا كان الطالب قد قدم الامتحان
//                     $studentExam = $student->exams()
//                                            ->where('exam_id', $exam->id)
//                                            ->first();

//                     // إذا لم يتم تقديم الامتحان، قم بتحديث السجل
//                     if (!$studentExam) {
//                         $student->exams()->attach($exam->id, [
//                             'score' => null,
//                             'has_attempted' => 0,
//                         ]);
//                         $this->info('Marked student ID ' . $student->id . ' as missed for exam ID ' . $exam->id);
//                     } else {
//                         $this->info('Student ID ' . $student->id . ' has already attempted this exam.');
//                     }
//                 }
//             }

//             $this->info('Missed exams marking process completed.');
//         });
// }


public function handle()
{
    Exam::whereNotNull('deadLineExam')
        ->where('deadLineExam', '<', now())
        ->chunk(100, function ($exams) {
            foreach ($exams as $exam) {
                $this->info('Exam ID: ' . $exam->id);

                // الحصول على الطلاب الذين اشتروا الدورة الخاصة بالامتحان
                $students = $exam->course->students; // هنا يتم استرداد الطلاب من العلاقة الموجودة

                if ($students->isEmpty()) {
                    $this->info('No students found for this exam.');
                }

                foreach ($students as $student) {
                    // تحقق ما إذا كان الطالب قد قدم الامتحان
                    $studentExam = $student->exams()
                                           ->where('exam_id', $exam->id)
                                           ->first();

                    // إذا لم يتم تقديم الامتحان، قم بتحديث السجل
                    if (!$studentExam) {
                        $student->exams()->attach($exam->id, [
                            'score' => null,
                            'has_attempted' => 0,
                        ]);
                        $this->info('Marked student ID ' . $student->id . ' as missed for exam ID ' . $exam->id);
                    } else {
                        $this->info('Student ID ' . $student->id . ' has already attempted this exam.');
                    }
                }
            }

            $this->info('Missed exams marking process completed.');
        });
}









    }

