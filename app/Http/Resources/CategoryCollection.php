<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\ResourceCollection;

class CategoryCollection extends ResourceCollection
{
    public function toArray($request)
    {
        return $this->collection->map(fn ($category) => [
            "id" => $category->id,
            "title" => $category->title
        ]);
    }
}