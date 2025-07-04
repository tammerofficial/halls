<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class HallResource extends JsonResource
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
            'name' => $this->name,
            'description' => $this->description,
            'capacity' => $this->capacity,
            'price_per_hour' => $this->price_per_hour,
            'location' => $this->location,
            'status' => $this->status, // Assuming you have a 'status' column
            'is_available' => $this->is_available,
            'image' => $this->image_url, // Assuming you have an accessor for a default image
            'amenities' => $this->amenities, // Make sure this is cast to an array in your Hall model
            'created_at' => $this->created_at->toDateTimeString(),
            'updated_at' => $this->updated_at->toDateTimeString(),
        ];
    }
}
