<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Media;
use App\Http\Requests\Admin\StorePostRequest;
use App\Http\Requests\Admin\UpdatePostRequest;
use App\Models\Category;
use App\Models\Post;
use App\Models\Tag;
use App\Support\CmsSlugger;
use Phaseolies\Http\Request;
use Phaseolies\Http\Response;
use Phaseolies\Utilities\Attributes\Mapper;
use Phaseolies\Utilities\Attributes\Middleware;
use Phaseolies\Utilities\Attributes\Model as RouteModel;
use Phaseolies\Utilities\Attributes\Route;
use Phaseolies\Utilities\Attributes\Transaction;

#[Mapper(prefix: 'admin/posts')]
#[Middleware(['auth', 'admin'])]
class PostController extends Controller
{
    #[Route(uri: '/', name: 'admin.posts.index')]
    public function index(Request $request): Response
    {
        $posts = Post::embed(['category:name', 'user:name', 'tags:name'])
            ->embedCount('tags')
            ->if($request->search, function ($q) use ($request) {
                $q->search(['title', 'user.name', 'category.name',   'tags.name',], $request->search);
            })
            ->if($request->status, fn($q) => $q->whereStatus($request->status))
            ->if($request->category_id,     fn($q) => $q->where('category_id', (int) $request->category_id))
            ->orderBy('id', 'DESC')
            ->paginate(10);

        $totalPosts = cache()->stashForever('post.total', fn() => Post::count());
        $publishedPosts = cache()->stashForever('post.published', fn() => Post::published()->count());
        $featuredPosts = cache()->stashForever('post.featured', fn() => Post::featured()->count());
        $draftPosts = cache()->stashForever('post.draft', fn() => Post::draft()->count());

        return view('admin.posts.index', [
            'posts' => $posts,
            'categories' => Category::query()->active()->orderBy('name')->get(),
            'totalPosts' => $totalPosts,
            'publishedPosts' => $publishedPosts,
            'featuredPosts' => $featuredPosts,
            'draftPosts' => $draftPosts,
            'publishedRatio' => $totalPosts > 0 ? (int) round(($publishedPosts / $totalPosts) * 100) : 0,
        ]);
    }

    #[Route(uri: '/create', name: 'admin.posts.create')]
    public function create(): Response
    {
        return view('admin.posts.form', [
            'post' => null,
            'formMode' => 'create',
            'categories' => Category::query()->active()->orderBy('name')->get(),
            'authorOptions' => $this->authorOptions(),
            'tagOptions' => $this->tagOptions(),
            'selectedTagIds' => [],
            'selectedTagNames' => [],
            'tagFieldValue' => '',
            'coverMediaItems' => $this->coverMediaItems(),
            'formInput' => session('input') ?? [],
        ]);
    }

    #[Transaction]
    #[Route(uri: '/', methods: ['POST'], name: 'admin.posts.store')]
    public function store(StorePostRequest $request): Response
    {
        $post = Post::create($request->passed());
        $post->tags()->relate($this->sanitizeTagIds($request));

        return redirect()->route('admin.posts.index')->withSuccess('Post created successfully.');
    }

    #[Route(uri: '/{post}/edit', name: 'admin.posts.edit')]
    public function edit(#[RouteModel(exception: true)] Post $post)
    {
        return view('admin.posts.form', [
            'post' => $post,
            'formMode' => 'edit',
            'categories' => Category::active()->orderBy('name')->get(),
            'authorOptions' => $this->authorOptions((string) ($post->author_name ?? '')),
            'tagOptions' => $this->tagOptions(),
            'selectedTagIds' => [],
            'tagFieldValue' => implode(', ', $this->extractTagNames($post)),
            'coverMediaItems' => $this->coverMediaItems(),
            'formInput' => session('input') ?? [],
        ]);
    }

    #[Transaction]
    #[Route(uri: '/{post}', methods: ['PUT'], name: 'admin.posts.update')]
    public function update(UpdatePostRequest $request, #[RouteModel(exception: true)] Post $post): Response
    {
        $post->update($request->passed());
        $post->tags()->relate($this->sanitizeTagIds($request));

        return redirect()->route('admin.posts.index')->withSuccess('Post updated successfully.');
    }

    #[Transaction]
    #[Route(uri: '/{post}', methods: ['DELETE'], name: 'admin.posts.destroy')]
    public function destroy(#[RouteModel(exception: true)] Post $post): Response
    {
        $post->tags()->unlink();
        $post->delete();

        return redirect()->route('admin.posts.index')->withSuccess('Post deleted successfully.');
    }

    protected function authorOptions(?string $currentValue = null): array
    {
        $options = [
            'Editorial Team',
            'Staff Writer',
            'Managing Editor',
            'Guest Contributor',
        ];

        $currentValue = trim((string) $currentValue);

        if ($currentValue !== '' && !in_array($currentValue, $options, true)) {
            array_unshift($options, $currentValue);
        }

        return $options;
    }

    protected function sanitizeTagIds($request): array
    {
        $tagIds = [];

        foreach ($this->normalizeTagNames($request->asArray('tag_names')) as $name) {
            $slug = CmsSlugger::slugify($name);
            $tag = Tag::firstOrCreate(
                ['slug' => $slug],
                [
                    'name' => $name,
                    'description' => '',
                    'color' => $this->pickTagColor($slug),
                ]
            );

            $tagIds[] = (int) $tag->id;
        }

        return array_values(array_unique($tagIds));
    }

    protected function coverMediaItems(): array
    {
        $items = [];

        foreach (Media::query()->orderBy('created_at', 'DESC')->limit(8)->get() as $media) {
            $items[] = [
                'id' => (int) $media->id,
                'title' => (string) $media->title,
                'url' => Media::publicUrl((string) $media->path),
                'thumbnail_url' => Media::publicUrl((string) $media->path),
                'original_name' => (string) $media->original_name,
                'size_label' => $this->formatMediaBytes((int) $media->size_bytes),
                'dimensions_label' => $media->width && $media->height
                    ? $media->width . ' × ' . $media->height
                    : 'Flexible',
                'alt_text' => (string) ($media->alt_text ?? $media->title ?? ''),
            ];
        }

        return $items;
    }

    protected function formatMediaBytes(int $bytes): string
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

    protected function normalizeTagNames(array $tagNames): array
    {
        $normalized = [];

        foreach ($tagNames as $tagName) {
            $name = trim((string) $tagName);

            if ($name === '') {
                continue;
            }

            $normalized[CmsSlugger::slugify($name)] = ucwords($name);
        }

        return array_values($normalized);
    }

    protected function extractTagNames(Post $post): array
    {
        $names = [];

        foreach ($post->tags as $tag) {
            $name = $this->extractTagValue($tag, 'name');

            if ($name !== null && trim((string) $name) !== '') {
                $names[] = (string) $name;
            }
        }

        return $names;
    }

    protected function extractTagValue(mixed $tag, string $key): mixed
    {
        if (is_array($tag)) {
            return $tag[$key] ?? null;
        }

        if (is_object($tag)) {
            return $tag->{$key} ?? null;
        }

        return null;
    }

    protected function tagOptions(): array
    {
        $options = [];

        foreach (Tag::query()->orderBy('name')->get() as $tag) {
            $options[] = (string) $tag->name;
        }

        return $options;
    }

    protected function pickTagColor(string $seed): string
    {
        $palette = [
            '#16a9eb',
            '#7c5cff',
            '#ff6f91',
            '#ff9f43',
            '#22c55e',
            '#14b8a6',
            '#6366f1',
        ];

        return $palette[abs(crc32($seed)) % count($palette)];
    }
}
