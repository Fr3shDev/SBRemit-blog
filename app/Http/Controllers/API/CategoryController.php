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

/**
 * @tags Blog Post Categories
 */
class CategoryController extends Controller
{
    public function __construct(private CategoryRepositoryInterface $categoryRepository) {}

    /**
     * Get All Categories
     *
     * Getting all blog post categories
     *
     * @authenticated
     *
     * @security BearerAuth
     */
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

    /**
     * Create Category
     *
     * @authenticated
     */
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
        } catch (Exception $e) {
            DB::rollBack();

            Log::error('Category creation failed: '.$e->getMessage());

            return response()->json([
                'status' => false,
                'message' => 'Category creation failed, please try again later.',
            ], 500);
        }
    }

    /**
     * Get a Category
     *
     * @authenticated
     */
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

    /**
     * Update a Category
     *
     * @authenticated
     */
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

    /**
     * Delete a Category
     *
     * @authenticated
     */
    public function delete($id)
    {
        try {
            $category = $this->categoryRepository->categoryToBeDeleted($id);
            if ($category->blogPosts->isNotEmpty()) {
                return response()->json(['status' => false, 'message' => 'Cannot delete a category with blog posts']);
            }
            $category->delete();

            return response()->json(['status' => true, 'message' => 'Category deleted successfully']);
        } catch (ModelNotFoundException $e) {
            return response()->json(['status' => false, 'message' => 'Category not found'], 404);
        } catch (Exception $e) {
            return response()->json(['status' => false, 'message' => 'Something went wrong, please try again later'], 500);

        }
    }
}
