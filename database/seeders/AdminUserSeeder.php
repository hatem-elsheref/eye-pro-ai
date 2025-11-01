<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create Admin User
        User::create([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'password' => Hash::make('password'),
            'is_admin' => true,
            'is_approved' => true,
        ]);

        // Create Regular Approved User
        User::create([
            'name' => 'Test User',
            'email' => 'user@example.com',
            'password' => Hash::make('password'),
            'is_admin' => false,
            'is_approved' => true,
        ]);

        // Create Pending User (needs approval)
        User::create([
            'name' => 'Pending User',
            'email' => 'pending@example.com',
            'password' => Hash::make('password'),
            'is_admin' => false,
            'is_approved' => false,
        ]);

        $this->command->info('✅ Users created successfully!');
        $this->command->info('');
        $this->command->info('📧 Admin Login:');
        $this->command->info('   Email: admin@example.com');
        $this->command->info('   Password: password');
        $this->command->info('');
        $this->command->info('📧 Regular User Login:');
        $this->command->info('   Email: user@example.com');
        $this->command->info('   Password: password');
        $this->command->info('');
        $this->command->info('📧 Pending User (Not Approved):');
        $this->command->info('   Email: pending@example.com');
        $this->command->info('   Password: password');
    }
}
