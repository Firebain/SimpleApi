<?php

namespace App\Repositories;

use App\Models\User;

class UserRepository
{
    public function getByEmail(string $email): ?User
    {
        return User::whereEmail($email)->first();
    }
}