<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    protected $message;

    protected $token;

    protected $tokenType;

    public function __construct($resource, $token = null, $tokenType = null, $message = 'Success')
    {
        parent::__construct($resource);
        $this->message = $message;
        $this->token = $token;
        $this->tokenType = $tokenType;
    }

    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'message' => $this->message,
            'header' => [
                'accessToken' => $this->token,
                'tokenType' => $this->tokenType,
            ],
            'attributes' => [
                'id' => $this->id,
                'name' => $this->name,
                'email' => $this->email,
                'phone' => $this->phone,
                'created_at' => $this->created_at,
                'updated_at' => $this->updated_at,
            ],
        ];
    }
}
