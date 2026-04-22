<?php

namespace App\Http\Controllers\Api\Concerns;

use App\Models\Language;

trait ResolvesApiLanguage
{
    protected function resolveLanguage(?string $code = null): ?Language
    {
        $requestedCode = $code ?: request()->query('lang') ?: app()->getLocale();

        return Language::query()
            ->where('code', $requestedCode)
            ->orWhere('locale', $requestedCode)
            ->first();
    }
}
