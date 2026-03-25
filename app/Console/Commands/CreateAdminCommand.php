<?php

namespace App\Console\Commands;

use App\Models\Admin;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

/**
 *  php artisan admin:create --email="admin@gmail.com" --role=admin
 */

class CreateAdminCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'admin:create
                            {--name= : Admin name}
                            {--email= : Admin email}
                            {--password= : Admin password}
                            {--role=admin : Admin role (super_admin or admin)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new admin account';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Creating new admin account...');
        $this->newLine();

        // Get input with validation
        $name = $this->option('name') ?: $this->ask('Admin name');
        $email = $this->option('email') ?: $this->ask('Admin email');
        $password = $this->option('password') ?: $this->secret('Admin password (min 8 characters)');
        $role = $this->option('role');

        // Validate role
        if (!in_array($role, ['super_admin', 'admin'])) {
            $role = $this->choice(
                'Select admin role',
                ['super_admin', 'admin'],
                1
            );
        }

        // Validate input
        $validator = Validator::make([
            'name' => $name,
            'email' => $email,
            'password' => $password,
            'role' => $role,
        ], [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:admins,email',
            'password' => 'required|string|min:8',
            'role' => 'required|in:super_admin,admin',
        ]);

        if ($validator->fails()) {
            foreach ($validator->errors()->all() as $error) {
                $this->error($error);
            }
            return 1;
        }

        try {
            // Create admin
            $admin = Admin::create([
                'name' => $name,
                'email' => $email,
                'password' => Hash::make($password),
                'role' => $role,
                'status' => 'active',
            ]);

            $this->newLine();
            $this->info('✓ Admin account created successfully!');
            $this->newLine();

            // Display admin details
            $this->table(
                ['Field', 'Value'],
                [
                    ['ID', $admin->id],
                    ['Name', $admin->name],
                    ['Email', $admin->email],
                    ['Role', $admin->role],
                    ['Status', $admin->status],
                    ['Created At', $admin->created_at],
                ]
            );

            return 0;
        } catch (\Exception $e) {
            $this->error('Failed to create admin: ' . $e->getMessage());
            return 1;
        }
    }
}
