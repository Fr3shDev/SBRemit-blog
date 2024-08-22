<?php

namespace App\Repositories;

use App\Interfaces\CategoryRepositoryInterface;
use App\Models\Category;

class CategoryRepository implements CategoryRepositoryInterface
{
    public function getCategories(?array $filters = null)
    {
        return Category::all();
    }

    public function getCategory($categoryId)
    {
        return Category::findOrFail($categoryId);
    }

    public function createCategory(array $categoryDetails)
    {
        return Category::create($categoryDetails);
    }

    public function updateCategory(array $categoryDetails, $categoryId)
    {
        $category = Category::findOrFail($categoryId);
        $category->update($categoryDetails);

        return $category;
    }

    public function categoryToBeDeleted($categoryId)
    {
        return Category::with('blogPosts')->findOrFail($categoryId);
    }
}
