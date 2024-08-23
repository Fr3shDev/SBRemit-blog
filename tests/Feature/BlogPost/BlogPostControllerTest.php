<?php

use App\Models\BlogPost;
use App\Models\Category;
use App\Models\User;

beforeEach(function () {
    Category::factory()->count(3)->create();
    $this->actingAs(User::factory()->create());
});

test('get all blog posts successfully', function () {
    // Create some blog posts
    $blogPosts = BlogPost::factory()->count(3)->create();

    // Make the request
    $response = $this->get('/api/blog-posts');

    // Assert the response status and structure
    $response->assertStatus(200);
    $response->assertJsonCount(3, 'data');
});

test('get all published blog posts successfully', function () {
    // Create some blog posts
    $publishedPosts = BlogPost::factory()->count(2)->create(['status' => 'published']);
    $draftPosts = BlogPost::factory()->count(1)->create(['status' => 'draft']);

    // Make the request
    $response = $this->get('/api/blog-posts/published');

    // Assert the response status
    $response->assertStatus(200);
});

test('get all draft blog posts successfully', function () {
    // Create some blog posts
    $draftPosts = BlogPost::factory()->count(2)->create(['status' => 'draft']);
    $publishedPosts = BlogPost::factory()->count(1)->create(['status' => 'published']);

    // Make the request
    $response = $this->get('/api/blog-posts/draft');

    // Assert the response status
    $response->assertStatus(200);
});

test('create blog post successfully', function () {
    $category = Category::factory()->create();

    $data = [
        'category_id' => $category->id,
        'title' => 'New Blog Post',
        'content' => 'This is the content of the new blog post.',
        'status' => 'published',
    ];

    $response = $this->post('/api/blog-posts', $data);

    $response->assertStatus(201);
    $this->assertDatabaseHas('blog_posts', ['title' => 'New Blog Post']);
});

test('get a specific blog post successfully', function () {
    $blogPost = BlogPost::factory()->create();

    $response = $this->get("/api/blog-posts/{$blogPost->id}");

    $response->assertStatus(200);
});

test('update blog post successfully', function () {
    $blogPost = BlogPost::factory()->create();

    $data = [
        'category_id' => Category::factory()->create()->id,
        'title' => 'Updated Blog Post Title',
        'content' => 'Updated content of the blog post.',
        'status' => 'draft',
    ];

    $response = $this->patch("/api/blog-posts/update/{$blogPost->id}", $data);

    $response->assertStatus(200);
    $this->assertDatabaseHas('blog_posts', ['id' => $blogPost->id, 'title' => 'Updated Blog Post Title']);
});

test('delete blog post successfully', function () {
    $blogPost = BlogPost::factory()->create();

    $response = $this->delete("/api/blog-posts/delete/{$blogPost->id}");

    $response->assertStatus(200);
    $this->assertDatabaseMissing('blog_posts', ['id' => $blogPost->id]);
});

test('get a blog post fails if not found', function () {
    $response = $this->get('/api/blog-posts/9999'); // Assuming 9999 is a non-existent ID

    $response->assertStatus(404)
        ->assertJson([
            'status' => false,
            'message' => 'Blog post not found',
        ]);
});
