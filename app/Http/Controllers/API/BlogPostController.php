<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\API\StoreBlogPostRequest;
use App\Http\Requests\API\UpdateBlogPostRequest;
use App\Http\Resources\BlogPostResource;
use App\Interfaces\BlogPostRepositoryInterface;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class BlogPostController extends Controller
{
    public function __construct(private BlogPostRepositoryInterface $blogPostRepository) {}

    public function index()
    {
        try {
            $blogPosts = $this->blogPostRepository->getBlogPosts();
            if ($blogPosts->isEmpty()) {
                return response()->json(['status' => false, 'message' => 'No blog posts available yet']);
            }

            return BlogPostResource::collection($blogPosts);
        } catch (Exception $e) {
            return response()->json(['status' => false, 'message' => 'Something went wrong, please try again later'], 500);
        }
    }

    public function publishedBlogPosts()
    {
        try {
            $blogPosts = $this->blogPostRepository->getPublishedBlogPosts();
            if ($blogPosts->isEmpty()) {
                return response()->json(['status' => false, 'message' => 'No published blog posts available yet']);
            }

            return BlogPostResource::collection($blogPosts);
        } catch (Exception $e) {
            return response()->json(['status' => false, 'message' => 'Something went wrong, please try again later'], 500);
        }
    }

    public function draftBlogPosts()
    {
        try {
            $blogPosts = $this->blogPostRepository->getDraftBlogPosts();
            if ($blogPosts->isEmpty()) {
                return response()->json(['status' => false, 'message' => 'No draft blog posts available yet']);
            }

            return BlogPostResource::collection($blogPosts);
        } catch (Exception $e) {
            return response()->json(['status' => false, 'message' => 'Something went wrong, please try again later'], 500);
        }
    }

    public function store(StoreBlogPostRequest $request)
    {
        $validated = $request->validated();
        $blogPostDetails = [
            'user_id' => $request->user()->id,
            'category_id' => $validated['category_id'],
            'title' => $validated['title'],
            'content' => $validated['content'],
            'status' => $validated['status'],
        ];

        DB::beginTransaction();
        try {
            $blogPost = $this->blogPostRepository->createBlogPost($blogPostDetails);
            DB::commit();

            return new BlogPostResource($blogPost);
        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Blog post creation failed: '.$e->getMessage());

            return response()->json([
                'status' => false,
                'message' => 'Blog post creation failed, please try again later.',
            ], 500);
        }
    }

    public function show($id)
    {
        try {
            $blogPost = $this->blogPostRepository->getBlogPost($id);
        } catch (ModelNotFoundException $e) {
            return response()->json(['status' => false, 'message' => 'Blog post not found'], 404);
        } catch (Exception $e) {
            return response()->json(['status' => false, 'message' => 'Something went wrong, please try again later'], 500);
        }

        return new BlogPostResource($blogPost);
    }

    public function update(UpdateBlogPostRequest $request, $id)
    {
        $validated = $request->validated();
        $blogPostDetails = [
            'user_id' => $request->user()->id,
            'category_id' => $validated['category_id'],
            'title' => $validated['title'],
            'content' => $validated['content'],
            'status' => $validated['status'],
        ];
        DB::beginTransaction();
        try {
            $blogPost = $this->blogPostRepository->updateBlogPost($blogPostDetails, $id);
            DB::commit();

            return new BlogPostResource($blogPost);
        } catch (ModelNotFoundException $e) {
            DB::rollBack();
            Log::warning('Blog post not found: '.$e->getMessage());

            return response()->json(['status' => false, 'message' => 'Blog post not found'], 404);
        } catch (Exception $e) {
            DB::rollBack();

            Log::error('Blog post update failed: '.$e->getMessage());

            return response()->json([
                'status' => false,
                'message' => 'Blog post update failed, please try again later.',
            ], 500);
        }
    }

    public function delete($id)
    {
        try {
            $blogPost = $this->blogPostRepository->blogPostToBeDeleted($id);
            $blogPost->delete();

            return response()->json(['status' => true, 'message' => 'Blog post deleted successfully']);
        } catch (ModelNotFoundException $e) {
            return response()->json(['status' => false, 'message' => 'Blog post not found'], 404);
        } catch (Exception $e) {
            return response()->json(['status' => false, 'message' => 'Something went wrong, please try again later'], 500);
        }

    }
}
