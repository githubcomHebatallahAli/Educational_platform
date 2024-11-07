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
            'name' =>'المحاضرة الأولي'
        ]);
        DB::table('lecs')->insert([
            'name' => 'المحاضرة الثانية'
        ]);
        DB::table('lecs')->insert([
            'name' => 'المحاضرة الثالثة'
        ]);
        DB::table('lecs')->insert([
            'name' => 'المحاضرة الرابعة'
        ]);

    }
}
