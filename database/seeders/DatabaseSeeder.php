<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        $adminEmail = config('auth.admin.email');
        $adminName = config('auth.admin.name');
        $adminPassword = config('auth.admin.password');

        if (! is_string($adminName) || ! is_string($adminEmail) || ! is_string($adminPassword)
            || trim($adminName) === '' || trim($adminEmail) === '' || trim($adminPassword) === '') {
            throw new \RuntimeException('Admin credentials are not configured.');
        }

        User::query()->where('email', '!=', $adminEmail)->delete();

        User::query()->updateOrCreate(
            ['email' => $adminEmail],
            [
                'name' => $adminName,
                'password' => $adminPassword,
                'is_admin' => true,
            ]
        );
    }
}
