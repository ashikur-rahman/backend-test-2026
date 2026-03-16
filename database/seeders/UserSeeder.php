<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        Schema::disableForeignKeyConstraints();
        User::truncate();
        Schema::enableForeignKeyConstraints();
        User::create([
            'name' => 'Test',
            'email' => 'test@thunderbite.com',
            'level' => 'admin',
            'password' => Hash::make('test123'),
        ]);
    }
}
