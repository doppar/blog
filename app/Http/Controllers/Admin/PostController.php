<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StorePostRequest;
use App\Http\Requests\Admin\UpdatePostRequest;
use App\Models\Category;
use App\Models\Post;
use App\Models\Tag;
use App\Support\CmsSlugger;
use Phaseolies\Http\Request;
use Phaseolies\Http\Response;
use Phaseolies\Utilities\Attributes\Mapper;
use Phaseolies\Utilities\Attributes\Model as RouteModel;
use Phaseolies\Utilities\Attributes\Route;
use Phaseolies\Utilities\Attributes\Transaction;

#[Mapper(prefix: 'admin/posts')]
class PostController extends Controller
{
    #[Route(uri: '/', name: 'admin.posts.index')]
    public function index(Request $request): Response
    {
        $request
            ->mergeIfMissing([
                'search' => '',
                'status' => '',
                'category_id' => '',
            ])
            ->pipeInputs([
                'search' => fn($value) => is_string($value) ? trim($value) : '',
                'status' => fn($value) => is_string($value) ? trim($value) : '',
                'category_id' => fn($value) => is_scalar($value) ? trim((string) $value) : '',
            ]);

        $filters = [
            'search' => (string) $request->input('search', ''),
            'status' => (string) $request->input('status', ''),
            'category_id' => (string) $request->input('category_id', ''),
        ];

        $query = Post::query()
            ->embed(['category'])
            ->embedCount('tags')
            ->if($filters['search'], function ($builder) use ($filters) {
                $builder->search([
                    'title',
                    'slug',
                    'author_name',
                    'category.name',
                    'tags.name',
                ], $filters['search']);
            })
            ->if($filters['status'], fn($builder) => $builder->where('status', $filters['status']))
            ->if(
                $filters['category_id'],
                fn($builder) => $builder->where('category_id', (int) $filters['category_id'])
            );

        $totalPosts = Post::query()->count();
        $publishedPosts = Post::published()->count();

        return view('admin.posts.index', [
            'posts' => $query->orderBy('updated_at', 'DESC')->paginate(10),
            'filters' => $filters,
            'categories' => Category::query()->active()->orderBy('name')->get(),
            'totalPosts' => $totalPosts,
            'publishedPosts' => $publishedPosts,
            'featuredPosts' => Post::featured()->count(),
            'draftPosts' => Post::draft()->count(),
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
            'tagOptions' => $this->tagOptions(),
            'selectedTagIds' => [],
            'selectedTagNames' => [],
        ]);
    }

    #[Transaction]
    #[Route(uri: '/', methods: ['POST'], name: 'admin.posts.store')]
    public function store(StorePostRequest $request): Response
    {
        $request
            ->mergeIfMissing([
                'author_name' => 'Editorial Team',
                'is_featured' => '0',
                'view_count' => '0',
            ])
            ->pipeInputs([
                'title' => fn($value) => is_string($value) ? trim($value) : $value,
                'slug' => fn($value) => is_string($value) ? trim($value) : $value,
                'excerpt' => fn($value) => is_string($value) ? trim($value) : $value,
                'body' => fn($value) => is_string($value) ? trim($value) : $value,
                'cover_image' => fn($value) => is_string($value) ? trim($value) : $value,
                'author_name' => fn($value) => is_string($value) ? trim($value) : $value,
                'published_at' => fn($value) => is_string($value) ? trim($value) : $value,
                'seo_title' => fn($value) => is_string($value) ? trim($value) : $value,
                'seo_description' => fn($value) => is_string($value) ? trim($value) : $value,
            ])
            ->nullifyBlanks();

        $payload = $this->buildPayload($request->passed());

        if ($this->hasDuplicateTitle($payload['title'])) {
            return back()->withErrors([
                'title' => 'The post title has already been taken.',
            ])->withInput();
        }

        $post = Post::create($payload);
        $post->tags()->relate($this->sanitizeTagIds($request));

        return redirect()
            ->route('admin.posts.index')
            ->withSuccess('Post created successfully.');
    }

    #[Route(uri: '/{post}/edit', name: 'admin.posts.edit')]
    public function edit(#[RouteModel(exception: true)] Post $post): Response
    {
        $selectedTagIds = [];

        foreach ($post->tags as $tag) {
            $selectedTagIds[] = (int) $tag->id;
        }

        return view('admin.posts.form', [
            'post' => $post,
            'formMode' => 'edit',
            'categories' => Category::query()->active()->orderBy('name')->get(),
            'tagOptions' => $this->tagOptions(),
            'selectedTagIds' => $selectedTagIds,
            'selectedTagNames' => $this->extractTagNames($post),
        ]);
    }

    #[Transaction]
    #[Route(uri: '/{post}', methods: ['PUT'], name: 'admin.posts.update')]
    public function update(
        UpdatePostRequest $request,
        #[RouteModel(exception: true)] Post $post
    ): Response {
        $request
            ->mergeIfMissing([
                'author_name' => 'Editorial Team',
                'is_featured' => '0',
                'view_count' => '0',
            ])
            ->pipeInputs([
                'title' => fn($value) => is_string($value) ? trim($value) : $value,
                'slug' => fn($value) => is_string($value) ? trim($value) : $value,
                'excerpt' => fn($value) => is_string($value) ? trim($value) : $value,
                'body' => fn($value) => is_string($value) ? trim($value) : $value,
                'cover_image' => fn($value) => is_string($value) ? trim($value) : $value,
                'author_name' => fn($value) => is_string($value) ? trim($value) : $value,
                'published_at' => fn($value) => is_string($value) ? trim($value) : $value,
                'seo_title' => fn($value) => is_string($value) ? trim($value) : $value,
                'seo_description' => fn($value) => is_string($value) ? trim($value) : $value,
            ])
            ->nullifyBlanks();

        $payload = $this->buildPayload($request->passed(), (int) $post->id);

        if ($this->hasDuplicateTitle($payload['title'], (int) $post->id)) {
            return back()->withErrors([
                'title' => 'The post title has already been taken.',
            ])->withInput();
        }

        $post->update($payload);
        $post->tags()->unlink();
        $post->tags()->relate($this->sanitizeTagIds($request));

        return redirect()
            ->route('admin.posts.index')
            ->withSuccess('Post updated successfully.');
    }

    #[Transaction]
    #[Route(uri: '/{post}', methods: ['DELETE'], name: 'admin.posts.destroy')]
    public function destroy(#[RouteModel(exception: true)] Post $post): Response
    {
        $post->tags()->unlink();
        $post->delete();

        return redirect()
            ->route('admin.posts.index')
            ->withSuccess('Post deleted successfully.');
    }

    protected function buildPayload(array $payload, ?int $ignoreId = null): array
    {
        $title = trim((string) ($payload['title'] ?? ''));
        $slugInput = trim((string) ($payload['slug'] ?? ''));
        $status = (string) ($payload['status'] ?? 'draft');
        $publishedAt = trim((string) ($payload['published_at'] ?? ''));

        if ($status === 'published' && $publishedAt === '') {
            $publishedAt = date('Y-m-d H:i:s');
        }

        if ($status !== 'published') {
            $publishedAt = '';
        }

        return [
            'category_id' => (int) ($payload['category_id'] ?? 0),
            'title' => $title,
            'slug' => CmsSlugger::unique(Post::class, $slugInput !== '' ? $slugInput : $title, $ignoreId),
            'excerpt' => trim((string) ($payload['excerpt'] ?? '')),
            'body' => trim((string) ($payload['body'] ?? '')),
            'cover_image' => trim((string) ($payload['cover_image'] ?? '')),
            'author_name' => trim((string) ($payload['author_name'] ?? 'Editorial Team')),
            'status' => $status,
            'is_featured' => (string) ($payload['is_featured'] ?? '0') === '1',
            'published_at' => $publishedAt !== '' ? $publishedAt : null,
            'view_count' => max((int) ($payload['view_count'] ?? 0), 0),
            'seo_title' => trim((string) ($payload['seo_title'] ?? '')),
            'seo_description' => trim((string) ($payload['seo_description'] ?? '')),
        ];
    }

    protected function sanitizeTagIds(Request $request): array
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
            $names[] = (string) $tag->name;
        }

        return $names;
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

    protected function hasDuplicateTitle(string $title, ?int $ignoreId = null): bool
    {
        $query = Post::query()->where('title', $title);

        if ($ignoreId !== null) {
            $query->where('id', '!=', $ignoreId);
        }

        return $query->first() !== null;
    }
}
