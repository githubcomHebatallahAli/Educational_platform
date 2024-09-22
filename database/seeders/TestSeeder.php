<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class TestSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('tests')->insert([
            'name' => 'امتحان الأسبوع الأول'
        ]);
        
        DB::table('tests')->insert([
            'name' => 'امتحان الأسبوع الثاني'
        ]);

        DB::table('tests')->insert([
            'name' => 'امتحان الأسبوع الثالث'
        ]);

        DB::table('tests')->insert([
            'name' => 'امتحان الأسبوع الرابع'
        ]);

        DB::table('tests')->insert([
            'name' => 'امتحان شامل'
        ]);
    }
}
