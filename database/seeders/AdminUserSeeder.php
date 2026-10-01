<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    /**
     * Buat 1 user Admin utama untuk Tech Blog.
     */
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'admin@myblog.com'],
            [
                'name'              => 'Admin Blog',
                'email'             => 'admin@myblog.com',
                'password'          => Hash::make('Admin@12345'),
                'is_admin'          => true,
                'email_verified_at' => now(),
            ]
        );

        $this->command->info('✅ Admin user berhasil dibuat: admin@myblog.com / Admin@12345');
    }
}
