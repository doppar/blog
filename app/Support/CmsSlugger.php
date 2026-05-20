<?php

namespace App\Support;

final class CmsSlugger
{
    public static function slugify(string $value): string
    {
        $value = strtolower(trim($value));
        $value = preg_replace('/[^a-z0-9]+/', '-', $value) ?? '';
        $value = trim($value, '-');

        return $value !== '' ? $value : 'entry';
    }

    public static function unique(string $modelClass, string $value, ?int $ignoreId = null): string
    {
        $baseSlug = self::slugify($value);
        $slug = $baseSlug;
        $suffix = 2;

        while (self::exists($modelClass, $slug, $ignoreId)) {
            $slug = $baseSlug . '-' . $suffix;
            $suffix++;
        }

        return $slug;
    }

    protected static function exists(string $modelClass, string $slug, ?int $ignoreId = null): bool
    {
        $query = $modelClass::query()->where('slug', $slug);

        if ($ignoreId !== null) {
            $query->where('id', '!=', $ignoreId);
        }

        return $query->first() !== null;
    }
}
