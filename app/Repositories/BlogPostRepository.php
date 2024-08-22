<?php

namespace App\Repositories;

use App\Interfaces\BlogPostRepositoryInterface;
use App\Models\BlogPost;

class BlogPostRepository implements BlogPostRepositoryInterface
{
    public function getBlogPosts()
    {
        $blogPosts = BlogPost::all();

        return $blogPosts;
    }

    public function getPublishedBlogPosts()
    {
        $blogPosts = BlogPost::where('status', 'published')->get();

        return $blogPosts;
    }

    public function getDraftBlogPosts()
    {
        $blogPosts = BlogPost::where('status', 'draft')->get();

        return $blogPosts;
    }

    public function getBlogPost($id)
    {
        $blogPost = BlogPost::findOrFail($id);

        return $blogPost;
    }

    public function createBlogPost(array $blogPostDetails)
    {
        return BlogPost::create($blogPostDetails);
    }

    public function updateBlogPost(array $blogPostDetails, $blogPostId)
    {
        $blogPost = BlogPost::findOrFail($blogPostId);
        $blogPost->update($blogPostDetails);

        return $blogPost;
    }

    public function blogPostToBeDeleted($blogPostId)
    {
        $blogPost = BlogPost::findOrFail($blogPostId);

        return $blogPost;
    }
}
