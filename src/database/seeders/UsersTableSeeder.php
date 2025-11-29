<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use Carbon\Carbon;

class UsersTableSeeder extends Seeder
{
    public function run()
    {
        User::create([
            'name' => 'テスト太郎',
            'email' => 'kiwi@example.com',
            'password' => Hash::make('password'),
            'profile_image' => 'kiwi.png',
            'postal_code' => '100-0005',
            'address' => '東京都千代田区丸の内3-1-1',
            'building_name' => '丸の内国際ビルディング101',
            'email_verified_at' => Carbon::now(),
        ]);

        User::create([
            'name' => 'テスト次郎',
            'email' => 'orange@example.com',
            'password' => Hash::make('password'),
            'profile_image' => 'orange.png',
            'postal_code' => '100-0005',
            'address' => '東京都千代田区丸の内3-1-1',
            'building_name' => '丸の内国際ビルディング201',
            'email_verified_at' => Carbon::now(),
        ]);

        User::create([
            'name' => 'テスト花子',
            'email' => 'watermelon@example.com',
            'password' => Hash::make('password'),
            'email_verified_at' => Carbon::now(),
        ]);
    }
}


