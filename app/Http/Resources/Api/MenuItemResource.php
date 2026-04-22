<?php

namespace App\Http\Resources\Api;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MenuItemResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'url' => $this->url ?: ($this->page ? '/'.$this->page->slug : '#'),
            'target' => $this->target,
            'children' => MenuItemResource::collection($this->whenLoaded('children')),
        ];
    }
}
