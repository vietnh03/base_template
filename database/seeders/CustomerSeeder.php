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
                'name' => 'Nguyễn Văn ' . Chr(64 + $i),
                'phone' => '09876543' . str_pad($i, 2, '0', STR_PAD_LEFT),
                'email' => "customer{$i}@example.com",
                'gender' => ($i % 2 == 0) ? 'Male' : 'Female',
                'date_of_birth' => '1990-01-' . str_pad($i, 2, '0', STR_PAD_LEFT),
                'status' => 1,
                'is_verified' => true,
                'password' => bcrypt('password'),
                'notes' => 'Khách hàng thử nghiệm ' . $i,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
