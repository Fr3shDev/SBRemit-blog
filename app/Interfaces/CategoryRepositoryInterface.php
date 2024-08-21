<?php

namespace App\Interfaces;

interface CategoryRepositoryInterface
{
    public function getCategories(?array $filters = null);

    public function getCategory($id);

    public function createCategory(array $details);

    public function updateCategory(array $details, $id);

    public function deleteCategory($id);
}
