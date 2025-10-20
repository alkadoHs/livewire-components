<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        // This formats each user in the 'data' array
        return [
            'id' => $this->id,
            'name' => $this->name,
        ];
    }
}