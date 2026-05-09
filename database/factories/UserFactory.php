<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserFactory extends Factory
{
    protected static ?string $password;

   public function definition(): array
    {
        return [
            'username'   => 'Admin', 
            'password'   => Hash::make('supersecure'), 
            'role'       => 'admin',
            'identifier' => 'ADMIN-001',
            'remember_token' => Str::random(10),
        ];
    }

    // Removed the unverified() method because 'email_verified_at' does not exist in your migration
}
