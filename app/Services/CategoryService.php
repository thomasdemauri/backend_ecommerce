<?php

namespace App\Services;

use App\Models\Category;
use App\Models\User;
use App\Models\Store;
use Illuminate\Support\Str;

class CategoryService
{

    public function getCategoryWithAttributes(string $id)
    {
        return Category::with('attributes')
                            ->findOrFail($id);
    }

}