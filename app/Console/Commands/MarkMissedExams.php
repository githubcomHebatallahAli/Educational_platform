<?php

namespace App\Console\Commands;

use App\Models\Exam;
use Illuminate\Console\Command;

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

    /**
     * Execute the console command.
     */
    public function handle()
    {

            // الحصول على الامتحانات التي انتهت
            $exams = Exam::where('deadLineExam', '<', now())->get();

            foreach ($exams as $exam) {
                $students = $exam->students;

                foreach ($students as $student) {
                    $studentExam = $student->exams()
                                           ->where('exam_id', $exam->id)
                                           ->first();

                    // إذا لم يقم الطالب بامتحان، قم بتحديث البيانات
                    if (!$studentExam || is_null($studentExam->pivot->score)) {
                        $student->exams()->updateExistingPivot($exam->id, [
                            'score' => null,
                            'has_attempted' => 0,
                        ]);
                    }
                }
            }

            $this->info('Missed exams marked successfully.');
        }
    }

