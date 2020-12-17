<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;
use Illuminate\Support\Facades\DB;

class AllExists implements Rule
{
    private string $table_name;
    private ?string $column;

    public function __construct(string $table_name, ?string $column)
    {
        $this->table_name = $table_name;
        $this->column = $column;
    }

    public function passes($attribute, $value)
    {
        $column = $this->column ?: $value;

        $count = DB::table($this->table_name)
            ->whereIn($column, $value)
            ->count();

        return $count === count($value);
    }

    public function message()
    {
        return 'Some element not exists';
    }
}