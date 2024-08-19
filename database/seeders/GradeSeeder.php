<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class GradeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('grades')->insert([
            'grade' => 'firstGrade',
        ]);


DB::table('grades')->insert([
            'grade' => 'secondGrade',
        ]);

DB::table('grades')->insert([
            'grade' => 'thirdGrade',
        ]);
    }
}
