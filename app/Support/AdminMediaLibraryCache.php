<?php

namespace App\Support;

use App\Models\Media;
use Phaseolies\Support\Facades\Cache;

class AdminMediaLibraryCache
{
    public const COUNTS_KEY = 'admin.media.counts.v2';

    public static function counts(): array
    {
        $counts = Cache::stash(self::COUNTS_KEY, ttl: 600, callback: static function (): array {
            $monthStart = date('Y-m-01 00:00:00');
            $storageBytes = (int) round(Media::query()->sum('size_bytes'));

            return [
                'total_items' => Media::query()->count(),
                'uploaded_this_month' => Media::query()
                    ->where('created_at', '>=', $monthStart)
                    ->count(),
                'storage_bytes' => $storageBytes,
                'storage_label' => self::formatBytes($storageBytes),
            ];
        });

        $storageBytes = (int) ($counts['storage_bytes'] ?? 0);

        return [
            'total_items' => (int) ($counts['total_items'] ?? 0),
            'uploaded_this_month' => (int) ($counts['uploaded_this_month'] ?? 0),
            'storage_bytes' => $storageBytes,
            'storage_label' => (string) ($counts['storage_label'] ?? self::formatBytes($storageBytes)),
        ];
    }

    public static function forgetCounts(): void
    {
        Cache::delete(self::COUNTS_KEY);
    }

    protected static function formatBytes(int $bytes): string
    {
        if ($bytes < 1024) {
            return $bytes . ' B';
        }

        $units = ['KB', 'MB', 'GB', 'TB'];
        $value = $bytes / 1024;

        foreach ($units as $unit) {
            if ($value < 1024 || $unit === 'TB') {
                return round($value, $value >= 100 ? 0 : 1) . ' ' . $unit;
            }

            $value /= 1024;
        }

        return round($value, 1) . ' TB';
    }
}
