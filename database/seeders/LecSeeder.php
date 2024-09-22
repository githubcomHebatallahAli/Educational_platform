<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class LecSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('lecs')->insert([
            'name' =>'الدرس الأول'
        ]);
        DB::table('lecs')->insert([
            'name' => 'الدرس الثاني'
        ]);
        DB::table('lecs')->insert([
            'name' => 'الدرس الثالث'
        ]);
        DB::table('lecs')->insert([
            'name' => 'الدرس الرابع'
        ]);
       
    }
}
