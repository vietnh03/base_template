<?php

namespace Database\Seeders;

use App\Models\Customer;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Carbon\Carbon;

class CustomerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Fake 10 customers
        for ($i = 1; $i <= 10; $i++) {
            DB::table('customers')->insert([
                'id' => Str::uuid()->toString(),
                'full_name' => 'Nguyễn Văn An',
                'phone_number' => '0987654321',
                'email' => 'nguyen.van.an@example.com',
                'customer_type' => 'Individual',
                'customer_status' => 'VIP',
                'assigned_staff_id' => Str::uuid()->toString(),
                'address' => '123 Nguyễn Trãi, Quận 1, TP.HCM',
                'date_of_birth' => '1985-05-15',
                'gender' => 'Male',
                'source' => 'Referral',
                'notes' => 'Khách hàng VIP, mua nhiều sản phẩm cao cấp',
            ]);
        }
    }
}
