<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        User::create([
            'name' => 'Admin',
            'email' => 'ai.s.photo.official@gmail.com',
            'password' => Hash::make('blue-0617'),
            'is_admin' => 1,  // あなたのモデルによってはこのフィールドが異なるかもしれません。
        ]);

        User::create([
            'name' => 'あおい',
            'email' => 'aoi.no.hair@gmail.com',
            'password' => Hash::make('blue0329'),
            'is_admin' => 0,  // あなたのモデルによってはこのフィールドが異なるかもしれません。
        ]);

        User::create([
            'name' => 'しがさん',
            'email' => 'd.shiga@quidquid.net',
            'password' => Hash::make('quid1023'),
            'is_admin' => 0,  // あなたのモデルによってはこのフィールドが異なるかもしれません。
        ]);

    }
}
