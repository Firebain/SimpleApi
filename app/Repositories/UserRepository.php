<?php

namespace App\Repositories;

use App\Models\User;

class UserRepository extends BaseRepository
{
    protected function getInstance()
    {
        return User::class;
    }

    public function getByEmail(string $email): ?User
    {
        return User::whereEmail($email)->first();
    }
}