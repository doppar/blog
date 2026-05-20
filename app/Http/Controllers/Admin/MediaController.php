<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Media;
use App\Support\AdminMediaLibraryCache;
use Phaseolies\Http\Request;
use Phaseolies\Http\Response;
use Phaseolies\Support\File;
use Phaseolies\Utilities\Attributes\Mapper;
use Phaseolies\Utilities\Attributes\Middleware;
use Phaseolies\Utilities\Attributes\Model as RouteModel;
use Phaseolies\Utilities\Attributes\Route;
use Phaseolies\Utilities\Attributes\Transaction;

#[Mapper(prefix: 'admin/media')]
#[Middleware(['auth'])]
class MediaController extends Controller
{
    #[Route(uri: '/', name: 'admin.media.index')]
    public function index(Request $request): Response
    {
        $request
            ->mergeIfMissing([
                'search' => '',
                'month' => '',
                'focus' => '',
            ])
            ->pipeInputs([
                'search' => fn($value) => is_string($value) ? trim($value) : '',
                'month' => fn($value) => is_string($value) ? trim($value) : '',
                'focus' => fn($value) => is_string($value) ? trim($value) : '',
            ]);

        $filters = [
            'search' => (string) $request->input('search', ''),
            'month' => (string) $request->input('month', ''),
            'focus' => (string) $request->input('focus', ''),
        ];

        $query = Media::query()
            ->if(
                $filters['search'],
                fn($builder) => $builder->search(
                    ['title', 'original_name', 'file_name', 'mime_type', 'uploaded_by'],
                    $filters['search']
                )
            )
            ->if($filters['month'], function ($builder) use ($filters) {
                [$start, $end] = $this->monthRange($filters['month']);

                if ($start && $end) {
                    $builder
                        ->where('created_at', '>=', $start)
                        ->where('created_at', '<', $end);
                }
            });

        $library = $query->orderBy('created_at', 'DESC')->paginate(18);
        $library['data'] = array_map(fn($item) => $this->transformMediaRecord($item), $library['data']);

        return view('admin.media.index', [
            'media' => $library,
            'filters' => $filters,
            'monthOptions' => $this->monthOptions(),
            'counts' => AdminMediaLibraryCache::counts(),
        ]);
    }

    #[Transaction]
    #[Route(uri: '/upload', methods: ['POST'], name: 'admin.media.upload')]
    public function upload(Request $request): Response
    {
        $files = $request->file('files');

        if ($files instanceof File) {
            $files = [$files];
        }

        if (!is_array($files) || $files === []) {
            return response()->json([
                'message' => 'Please select at least one image to upload.',
                'errors' => [
                    'files' => ['Please select at least one image to upload.'],
                ],
            ], 422);
        }

        $uploadedItems = [];

        foreach ($files as $file) {
            if (!$file instanceof File) {
                continue;
            }

            $validationError = $this->validateUpload($file);

            if ($validationError !== null) {
                return response()->json([
                    'message' => $validationError,
                    'errors' => [
                        'files' => [$validationError],
                    ],
                ], 422);
            }

            $uploadedItems[] = $this->storeUploadedFile($file);
        }

        return response()->json([
            'message' => count($uploadedItems) === 1
                ? 'Media file uploaded successfully.'
                : count($uploadedItems) . ' media files uploaded successfully.',
            'items' => array_map(fn($media) => $this->transformMediaRecord($media), $uploadedItems),
            'counts' => AdminMediaLibraryCache::counts(),
        ]);
    }

    #[Transaction]
    #[Route(uri: '/{media}', methods: ['DELETE'], name: 'admin.media.destroy')]
    public function destroy(#[RouteModel(exception: true)] Media $media): Response
    {
        $mediaId = (int) $media->id;
        $media->delete();

        return response()->json([
            'message' => 'Media file deleted successfully.',
            'deleted_id' => $mediaId,
            'counts' => AdminMediaLibraryCache::counts(),
        ]);
    }

    protected function validateUpload(File $file): ?string
    {
        if (!$file->isValid()) {
            return 'One of the selected files could not be uploaded.';
        }

        if (!$file->isImage()) {
            return 'Only image uploads are allowed in the media library.';
        }

        $extension = $file->getClientOriginalExtension();
        $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif', 'webp', 'avif', 'svg'];

        if (!in_array($extension, $allowedExtensions, true)) {
            return 'Unsupported image type. Please upload JPG, PNG, GIF, WEBP, AVIF, or SVG.';
        }

        if ($file->getClientOriginalSize() > 8 * 1024 * 1024) {
            return 'The selected image exceeds the 8 MB upload limit.';
        }

        return null;
    }

    protected function storeUploadedFile(File $file): Media
    {
        $directory = 'media/' . date('Y/m');
        $extension = $file->getClientOriginalExtension();
        $fileName = date('His') . '_' . bin2hex(random_bytes(6)) . ($extension !== '' ? '.' . $extension : '');
        $imageSize = @getimagesize($file->getClientOriginalPath()) ?: null;
        $storedPath = $file->storeAs($directory, $fileName, 'public');

        if ($storedPath === false) {
            throw new \RuntimeException('The selected file could not be stored.');
        }

        $title = $this->buildMediaTitle($file->getClientOriginalName());

        return Media::create([
            'title' => $title,
            'original_name' => $file->getClientOriginalName(),
            'file_name' => $fileName,
            'disk' => 'public',
            'directory' => $directory,
            'path' => $storedPath,
            'mime_type' => $file->getClientOriginalType(),
            'extension' => $extension,
            'size_bytes' => $file->getClientOriginalSize(),
            'width' => $imageSize[0] ?? null,
            'height' => $imageSize[1] ?? null,
            'alt_text' => $title,
            'caption' => null,
            'uploaded_by' => 'Editorial Admin',
        ]);
    }

    protected function buildMediaTitle(string $originalName): string
    {
        $base = pathinfo($originalName, PATHINFO_FILENAME);
        $base = preg_replace('/[-_]+/', ' ', $base ?? '');
        $base = preg_replace('/\s+/', ' ', (string) $base);
        $base = trim((string) $base);

        return $base !== '' ? ucwords($base) : 'Untitled Image';
    }

    protected function transformMediaRecord(Media $media): array
    {
        $createdAt = (string) ($media->created_at ?? '');

        return [
            'id' => (int) $media->id,
            'title' => (string) $media->title,
            'original_name' => (string) $media->original_name,
            'file_name' => (string) $media->file_name,
            'disk' => (string) $media->disk,
            'path' => (string) $media->path,
            'url' => Media::publicUrl((string) $media->path),
            'thumbnail_url' => Media::publicUrl((string) $media->path),
            'destroy_url' => route('admin.media.destroy', ['media' => $media->id]),
            'mime_type' => (string) $media->mime_type,
            'extension' => strtoupper((string) $media->extension),
            'size_bytes' => (int) $media->size_bytes,
            'size_label' => $this->formatBytes((int) $media->size_bytes),
            'width' => $media->width ? (int) $media->width : null,
            'height' => $media->height ? (int) $media->height : null,
            'dimensions_label' => $media->width && $media->height
                ? $media->width . ' × ' . $media->height
                : 'Flexible',
            'alt_text' => (string) ($media->alt_text ?? ''),
            'caption' => (string) ($media->caption ?? ''),
            'uploaded_by' => (string) $media->uploaded_by,
            'created_at' => $createdAt,
            'created_label' => $createdAt !== '' ? date('M d, Y · h:i A', strtotime($createdAt)) : 'Recently added',
            'created_relative' => $createdAt !== '' ? $this->relativeDate($createdAt) : 'Just now',
        ];
    }

    protected function monthOptions(): array
    {
        $options = [];

        foreach (Media::query()->orderBy('created_at', 'DESC')->get() as $item) {
            $createdAt = trim((string) ($item->created_at ?? ''));

            if ($createdAt === '') {
                continue;
            }

            $value = date('Y-m', strtotime($createdAt));

            if (!isset($options[$value])) {
                $options[$value] = date('F Y', strtotime($createdAt));
            }
        }

        return $options;
    }

    protected function monthRange(string $month): array
    {
        if (!preg_match('/^\d{4}-\d{2}$/', $month)) {
            return [null, null];
        }

        $start = date('Y-m-01 00:00:00', strtotime($month . '-01'));
        $end = date('Y-m-01 00:00:00', strtotime($start . ' +1 month'));

        return [$start, $end];
    }

    protected function formatBytes(int $bytes): string
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

    protected function relativeDate(string $dateTime): string
    {
        $timestamp = strtotime($dateTime);

        if ($timestamp === false) {
            return 'Recently added';
        }

        $diff = time() - $timestamp;

        if ($diff < 60) {
            return 'Just now';
        }

        if ($diff < 3600) {
            return floor($diff / 60) . ' min ago';
        }

        if ($diff < 86400) {
            return floor($diff / 3600) . ' hr ago';
        }

        if ($diff < 604800) {
            return floor($diff / 86400) . ' days ago';
        }

        return date('M d, Y', $timestamp);
    }
}
