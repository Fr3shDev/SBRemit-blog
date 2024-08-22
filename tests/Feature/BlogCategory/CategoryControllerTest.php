<?php

use App\Models\Category;
use App\Models\User;

beforeEach(function () {
    $this->actingAs(User::factory()->create());

});

test('get all categories successfully', function () {
    // Create some categories
    $categories = Category::factory()->count(3)->create();

    // Make the request
    $response = $this->get('/api/categories');

    // Assert the response status and structure
    $response->assertStatus(200);
});

test('create category successfully', function () {
    $data = [
        'name' => 'New Category',
    ];

    $response = $this->post('/api/categories/store', $data);

    $response->assertStatus(201);

    $this->assertDatabaseHas('categories', ['name' => 'New Category']);
});

test('get a specific category successfully', function () {
    $category = Category::factory()->create();

    $response = $this->get("/api/categories/{$category->id}");

    $response->assertStatus(200);
});

test('update category successfully', function () {
    $category = Category::factory()->create();

    $data = [
        'name' => 'Updated Category Name',
    ];

    $response = $this->put("/api/categories/update/{$category->id}", $data);

    $response->assertStatus(200);

    // Assert the category was updated in the database
    $this->assertDatabaseHas('categories', ['name' => 'Updated Category Name']);
});


test('delete category successfully', function () {
    $category = Category::factory()->create();

    $response = $this->delete("/api/categories/delete/{$category->id}");

    $response->assertStatus(200);

    // Assert the category was deleted from the database
    $this->assertDatabaseMissing('categories', ['id' => $category->id]);
});

test('get a category fails if not found', function () {
    $response = $this->get('/api/categories/9999'); // Assuming 9999 is a non-existent ID

    $response->assertStatus(404)
        ->assertJson([
            'status' => false,
            'message' => 'Category not found',
        ]);
});
