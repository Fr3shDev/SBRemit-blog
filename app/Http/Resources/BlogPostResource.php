<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BlogPostResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'attributes' => [
                'user_id' => $this->user_id,
                'category_id' => $this->category_id,
                'title' => $this->title,
                'content' => $this->content,
                'status' => $this->status,
            ],
        ];
    }
}
