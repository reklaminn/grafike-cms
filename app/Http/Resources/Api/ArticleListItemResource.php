<?php

namespace App\Http\Resources\Api;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ArticleListItemResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'slug' => $this->slug,
            'excerpt' => $this->excerpt,
            'featured_image' => $this->getFirstMediaUrl('cover'),
            'published_at' => $this->published_at?->toAtomString(),
            'page' => $this->whenLoaded('page', fn () => [
                'id' => $this->page?->id,
                'title' => $this->page?->title,
                'slug' => $this->page?->slug,
            ]),
        ];
    }
}
