<?php

namespace App\Repositories;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

abstract class BaseRepository
{
    abstract protected function getInstance();

    public function all(): Collection
    {
        return $this->getInstance()::all();
    }

    public function create(array $fillable): Model
    {
        return $this->getInstance()::create($fillable);
    }

    public function update(Model $model, array $fillable)
    {
        $model->fill($fillable);
        $model->save();

        return $model;
    }

    public function delete(Model $model)
    {
        $model->delete();
    }
}