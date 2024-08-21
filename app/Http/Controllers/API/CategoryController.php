<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\API\StoreCategoryRequest;
use App\Http\Requests\API\UpdateCategoryRequest;
use App\Http\Resources\CategoryResource;
use App\Interfaces\CategoryRepositoryInterface;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CategoryController extends Controller
{
    public function __construct(private CategoryRepositoryInterface $categoryRepository) {}

    public function index()
    {
        try {
            $categories = $this->categoryRepository->getCategories();
            if ($categories->isEmpty()) {
                return response()->json(['status' => false, 'message' => 'No categories available yet']);
            }
        } catch (Exception $e) {
            return response()->json(['status' => false, 'message' => 'Something went wrong, please try again later'], 500);
        }

        return CategoryResource::collection($categories);
    }

    public function store(StoreCategoryRequest $request)
    {
        $validated = $request->validated();
        $categoryDetails = [
            'name' => $validated['name'],
        ];

        DB::beginTransaction();
        try {
            $category = $this->categoryRepository->createCategory($categoryDetails);
            DB::commit();

            return new CategoryResource($category);
        } catch (\Exception $e) {
            DB::rollBack();

            // Log the exception for debugging purposes
            Log::error('Category creation failed: '.$e->getMessage());

            // Return a generic error message to the user with a 500 status code
            return response()->json([
                'status' => false,
                'message' => 'Category creation failed, please try again later.',
            ], 500);
        }
    }

    public function show($id)
    {
        try {
            $category = $this->categoryRepository->getCategory($id);
        } catch (ModelNotFoundException $e) {
            return response()->json(['status' => false, 'message' => 'Category not found'], 404);
        } catch (Exception $e) {
            Log::error('Could not get category: '.$e->getMessage());

            return response()->json(['status' => false, 'message' => 'Could not get category, please try again later'], 500);
        }

        return new CategoryResource($category);
    }

    public function update(UpdateCategoryRequest $request, $id)
    {
        $validated = $request->validated();
        $categoryDetails = [
            'name' => $validated['name'],
        ];
        DB::beginTransaction();
        try {
            $category = $this->categoryRepository->updateCategory($categoryDetails, $id);
            DB::commit();

            return new CategoryResource($category);
        } catch (ModelNotFoundException $e) {
            DB::rollBack();

            // Log the specific error for debugging
            Log::warning('Category not found: '.$e->getMessage(), ['categoryId' => $id]);

            // Return a 404 response with a message
            return response()->json([
                'status' => false,
                'message' => 'Category not found.',
            ], 404);
        } catch (Exception $e) {
            DB::rollBack();

            Log::error('Category update failed: '.$e->getMessage());

            return response()->json(['status' => false, 'message' => 'Category update failed, please try again later'], 500);
        }
    }

    public function delete($id)
    {
        $category = $this->categoryRepository->deleteCategory($id);
        if ($category->blogPosts->isNotEmpty()) {
            return response()->json(['status' => false, 'message' => 'Cannot delete a category with blog posts']);
        }
        $category->delete();

        return response()->json(['status' => true, 'message' => 'Category deleted successfully']);
    }
}
