<?php

namespace App\Http\Resources\Api;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SiteResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'site' => [
                'name' => $this['name'],
                'domain' => $this['domain'],
                'theme' => $this['theme'],
                'tokens' => $this['tokens'],
                'header_variant' => $this['header_variant'],
                'footer_variant' => $this['footer_variant'],
                'locale' => $this['locale'],
                'available_locales' => $this['available_locales'],
            ],
        ];
    }
}
