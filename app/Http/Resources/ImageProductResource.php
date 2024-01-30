<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ImageProductResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
//        return parent::toArray($request);
        return [
            'id' => $this->id,
            "variant_id" => $this->variant_id,
            "image_gallery_id" => $this->image_gallery_id,
            "created_at" => $this->created_at,
            "updated_at" => $this->updated_at,
            'imageGallery' => new ImageGalleryResource($this->whenLoaded('imageGallery')),
        ];
    }
}
