<?php

namespace Database\Seeders;


use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class PaymobMethodSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('paymob_methods')->insert([
            'payment_method' => 'Online Card',
            'api_key' => 'ZXlKaGJHY2lPaUpJVXpVeE1pSXNJblI1Y0NJNklrcFhWQ0o5LmV5SmpiR0Z6Y3lJNklrMWxjbU5vWVc1MElpd2ljSEp2Wm1sc1pWOXdheUk2TVRBd05EYzVPQ3dpYm1GdFpTSTZJbWx1YVhScFlXd2lmUS5JTWVwenJtM1hNWE10V3JUNDRpUS0zVmN0TW5rVktMSVByekRPUGs5YjVxRWJWOENxU0VmR2Q2dXRULWhnY3g2OVZpbFl3WXZfNF9EVmNBUzc0eEF5Zw==',
            'integration_id' => '4871116',
            'currency' => 'EGP',
            'status' => 'active',
        ]);
        DB::table('paymob_methods')->insert([
            'payment_method' => 'Mobile Wallet',
            'api_key' => 'ZXlKaGJHY2lPaUpJVXpVeE1pSXNJblI1Y0NJNklrcFhWQ0o5LmV5SmpiR0Z6Y3lJNklrMWxjbU5vWVc1MElpd2ljSEp2Wm1sc1pWOXdheUk2TVRBd05EYzVPQ3dpYm1GdFpTSTZJbWx1YVhScFlXd2lmUS5JTWVwenJtM1hNWE10V3JUNDRpUS0zVmN0TW5rVktMSVByekRPUGs5YjVxRWJWOENxU0VmR2Q2dXRULWhnY3g2OVZpbFl3WXZfNF9EVmNBUzc0eEF5Zw==',
            'integration_id' => '4873707',
            'currency' => 'EGP',
            'status' => 'active',
        ]);


        }
    }

