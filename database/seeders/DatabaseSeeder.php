<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Factories\Sequence;

use App\Models\User;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        User::factory()
            ->count(3)
            ->state(new Sequence(
                ["email" => "test1@gmail.com"],
                ["email" => "test2@gmail.com"],
                ["email" => "test3@gmail.com"]
            ))
            ->hasCategories(5)
            ->create();
    }
}