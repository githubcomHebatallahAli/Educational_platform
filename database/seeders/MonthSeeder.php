<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class MonthSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('months')->insert([
            'name' => 'كورس الشهر الأول',
        ]);
        DB::table('months')->insert([
            'name' =>  'كورس الشهر الثاني'
        ]);

        DB::table('months')->insert([
            'name' => 'كورس الشهر الثالث',
        ]);
        DB::table('months')->insert([
            'name' => 'كورس الشهر الرابع',
        ]);
        DB::table('months')->insert([
            'name' => 'كورس الشهر الخامس',
        ]);
        DB::table('months')->insert([
            'name' => 'كورس الشهر السادس',
        ]);
        DB::table('months')->insert([
            'name' => 'كورس الشهر السابع',
        ]);
        DB::table('months')->insert([
            'name' => 'كورس الشهر الثامن',
        ]);
        DB::table('months')->insert([
            'name' => 'كورس الشهر التاسع',
        ]);
        DB::table('months')->insert([
            'name' => 'كورس الشهر العاشر',
        ]);
        DB::table('months')->insert([
            'name' => 'كورس الشهر الحادي عشر',
        ]);
        DB::table('months')->insert([
            'name' => 'كورس الشهر الثاني عشر',
        ]);
    }
}
