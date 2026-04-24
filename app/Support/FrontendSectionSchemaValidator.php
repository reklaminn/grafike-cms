<?php

namespace App\Support;

use Illuminate\Support\Collection;

final class FrontendSectionSchemaValidator
{
    /**
     * Validate all block content fields against their section template schema.
     *
     * @param  array      $sectionsJson  The decoded sections_json structure
     * @param  Collection $templatesById SectionTemplate models keyed by ID
     * @return array<string, list<string>>  dot-path → list of error messages
     */
    public static function validate(array $sectionsJson, Collection $templatesById): array
    {
        $errors = [];
        $blocks = FrontendSections::flattenBlocks($sectionsJson);

        foreach ($blocks as $index => $block) {
            $templateId = $block['section_template_id'] ?? null;
            $template   = $templateId ? $templatesById->get($templateId) : null;
            $schema     = $template?->schema_json ?? [];

            if (empty($schema) || ! is_array($schema)) {
                continue;
            }

            $content = $block['content'] ?? [];
            $path    = "sections.block_{$index}";

            foreach ($schema as $fieldKey => $fieldRules) {
                if (! is_array($fieldRules)) {
                    continue;
                }

                $value      = $content[$fieldKey] ?? null;
                $fieldPath  = "{$path}.{$fieldKey}";
                $label      = $fieldRules['label'] ?? $fieldKey;

                if (! empty($fieldRules['required']) && self::isEmpty($value)) {
                    $errors[$fieldPath][] = "{$label} alanı zorunludur.";
                    continue;
                }

                if (self::isEmpty($value)) {
                    continue;
                }

                $type = $fieldRules['type'] ?? 'string';

                if (in_array($type, ['string', 'text', 'textarea']) && isset($fieldRules['max'])) {
                    if (mb_strlen((string) $value) > (int) $fieldRules['max']) {
                        $errors[$fieldPath][] = "{$label} en fazla {$fieldRules['max']} karakter olabilir.";
                    }
                }

                if (in_array($type, ['string', 'text', 'textarea']) && isset($fieldRules['min'])) {
                    if (mb_strlen((string) $value) < (int) $fieldRules['min']) {
                        $errors[$fieldPath][] = "{$label} en az {$fieldRules['min']} karakter olmalıdır.";
                    }
                }

                if ($type === 'number') {
                    if (! is_numeric($value)) {
                        $errors[$fieldPath][] = "{$label} sayısal bir değer olmalıdır.";
                    } elseif (isset($fieldRules['min']) && (float) $value < (float) $fieldRules['min']) {
                        $errors[$fieldPath][] = "{$label} en az {$fieldRules['min']} olmalıdır.";
                    } elseif (isset($fieldRules['max']) && (float) $value > (float) $fieldRules['max']) {
                        $errors[$fieldPath][] = "{$label} en fazla {$fieldRules['max']} olabilir.";
                    }
                }

                if ($type === 'url' && ! empty($value) && ! filter_var($value, FILTER_VALIDATE_URL)) {
                    $errors[$fieldPath][] = "{$label} geçerli bir URL olmalıdır.";
                }

                if ($type === 'email' && ! empty($value) && ! filter_var($value, FILTER_VALIDATE_EMAIL)) {
                    $errors[$fieldPath][] = "{$label} geçerli bir e-posta adresi olmalıdır.";
                }

                if ($type === 'enum' && isset($fieldRules['options']) && is_array($fieldRules['options'])) {
                    if (! in_array($value, $fieldRules['options'], true)) {
                        $allowed = implode(', ', $fieldRules['options']);
                        $errors[$fieldPath][] = "{$label} şu değerlerden biri olmalıdır: {$allowed}.";
                    }
                }
            }
        }

        return $errors;
    }

    private static function isEmpty(mixed $value): bool
    {
        return $value === null || $value === '' || (is_array($value) && empty($value));
    }
}
