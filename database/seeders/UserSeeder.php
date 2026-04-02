<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Carbon\Carbon;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Fake 10 users
        for ($i = 1; $i <= 10; $i++) {
            DB::table('users')->insert([
                'id' => Str::uuid()->toString(),
                'name' => 'Nguyễn Văn ' . Chr(64 + $i),
                'phone' => '09876543' . str_pad($i, 2, '0', STR_PAD_LEFT),
                'email' => "user{$i}@example.com",
                'gender' => ($i % 2 == 0) ? 'Male' : 'Female',
                'date_of_birth' => '1990-01-' . str_pad($i, 2, '0', STR_PAD_LEFT),
                'status' => 1,
                'email_verified_at' => now(),
                'password' => bcrypt('password'),
                'notes' => 'Người dùng thử nghiệm ' . $i,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
