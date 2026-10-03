<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        $password = config('app.admin.password') ?: Str::password(24);
        $admin = User::firstOrCreate(
            ['email' => config('app.admin.email')],
            [
                'name' => config('app.admin.name'),
                'password' => $password,
                'is_admin' => true,
                'email_verified_at' => now(),
            ],
        );

        if (! $admin->wasRecentlyCreated) {
            $this->command->warn("User {$admin->email} sudah ada. Tidak ada perubahan yang dilakukan.");

            return;
        }

        $this->command->info("Admin berhasil dibuat: {$admin->email}");
        if (! config('app.admin.password')) {
            $this->command->line("Password sementara (simpan sekarang): {$password}");
        } else {
            $this->command->line('Password admin diambil dari ADMIN_PASSWORD.');
        }
    }
}
