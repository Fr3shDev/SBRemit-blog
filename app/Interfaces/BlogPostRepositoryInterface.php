<?php

namespace App\Interfaces;

interface BlogPostRepositoryInterface
{
    public function getBlogPosts();

    public function getPublishedBlogPosts();

    public function getDraftBlogPosts();

    public function getBlogPost($id);

    public function createBlogPost(array $details);

    public function updateBlogPost(array $details, $id);

    public function blogPostToBeDeleted($id);
}
