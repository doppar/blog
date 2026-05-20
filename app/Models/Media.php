<?php

namespace App\Models;

use App\Support\AdminMediaLibraryCache;
use Phaseolies\Database\Entity\Attributes\Hook;
use Phaseolies\Database\Entity\Model;
use Phaseolies\Support\Facades\Storage;

class Media extends Model
{
    protected $table = 'media';

    protected $creatable = [
        'title',
        'original_name',
        'file_name',
        'disk',
        'directory',
        'path',
        'mime_type',
        'extension',
        'size_bytes',
        'width',
        'height',
        'alt_text',
        'caption',
        'uploaded_by',
    ];

    public static function publicUrl(?string $path): string
    {
        $baseUrl = rtrim((string) config('filesystem.disks.public.url'), '/');

        return $baseUrl . '/' . ltrim((string) $path, '/');
    }

    #[Hook('after_created')]
    #[Hook('after_deleted')]
    protected function forgetMediaCounts(): void
    {
        AdminMediaLibraryCache::forgetCounts();
    }

    #[Hook('after_deleted')]
    protected function deleteStoredAsset(): void
    {
        $disk = trim((string) ($this->disk ?? 'public'));
        $path = trim((string) ($this->path ?? ''));

        if ($disk === '' || $path === '') {
            return;
        }

        Storage::disk($disk)->delete($path);
    }
}
