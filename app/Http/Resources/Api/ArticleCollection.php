<?php

namespace App\Http\Resources\Api;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

/**
 * Standard Laravel collection format:
 *   { "data": [ {...}, ... ], "links": {...}, "meta": { "total": N, ... } }
 *
 * Next.js tüketicisi:  response.data  → items   |  response.meta.total → sayfalama
 */
class ArticleCollection extends ResourceCollection
{
    public $collects = ArticleListItemResource::class;
}
