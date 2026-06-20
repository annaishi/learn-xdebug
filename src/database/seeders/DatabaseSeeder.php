<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // デモ用ログインアカウント
        //   email:    demo@example.com
        //   password: password
        User::updateOrCreate(
            ['email' => 'demo@example.com'],
            [
                'name'     => 'デモ ユーザー',
                'password' => 'password', // User モデルの cast でハッシュ化される
            ],
        );
    }
}
