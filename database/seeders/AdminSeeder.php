<?php

namespace Database\Seeders;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
                        
         User::updateOrCreate(
            ['email' => 'ronaldjjuuko7@gmail.com'],
            [
                'name' => 'JRonnie',
                'password' => Hash::make('88928892'),
                'email_verified_at' => now(),
                'is_suspended' => false
            ]
        );

    }
}
