<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreTagRequest;
use App\Http\Requests\Admin\UpdateTagRequest;
use App\Models\Tag;
use App\Support\CmsSlugger;
use Phaseolies\Http\Request;
use Phaseolies\Http\Response;
use Phaseolies\Utilities\Attributes\Mapper;
use Phaseolies\Utilities\Attributes\Model as RouteModel;
use Phaseolies\Utilities\Attributes\Route;

#[Mapper(prefix: 'admin/tags')]
class TagController extends Controller
{
    #[Route(uri: '/', name: 'admin.tags.index')]
    public function index(Request $request): Response
    {
        $request
            ->mergeIfMissing(['search' => ''])
            ->pipeInputs([
                'search' => fn($value) => is_string($value) ? trim($value) : '',
            ]);

        $search = (string) $request->input('search', '');

        $query = Tag::query()
            ->embedCount('posts')
            ->if($search, fn($builder) => $builder->search(['name', 'slug', 'description'], $search));

        return view('admin.tags.index', [
            'tags' => $query->orderBy('updated_at', 'DESC')->paginate(12),
            'search' => $search,
        ]);
    }

    #[Route(uri: '/create', name: 'admin.tags.create')]
    public function create(): Response
    {
        return view('admin.tags.form', [
            'tag' => null,
            'formMode' => 'create',
        ]);
    }

    #[Route(uri: '/', methods: ['POST'], name: 'admin.tags.store')]
    public function store(StoreTagRequest $request): Response
    {
        $request
            ->pipeInputs([
                'name' => fn($value) => is_string($value) ? trim($value) : $value,
                'slug' => fn($value) => is_string($value) ? trim($value) : $value,
                'description' => fn($value) => is_string($value) ? trim($value) : $value,
                'color' => fn($value) => is_string($value) ? trim($value) : $value,
            ])
            ->nullifyBlanks();

        $request->validate();

        $payload = $this->buildPayload($request->passed());

        if ($this->hasDuplicateName($payload['name'])) {
            return back()->withErrors([
                'name' => 'The tag name has already been taken.',
            ])->withInput();
        }

        Tag::create($payload);

        return redirect()
            ->route('admin.tags.index')
            ->withSuccess('Tag created successfully.');
    }

    #[Route(uri: '/{tag}/edit', name: 'admin.tags.edit')]
    public function edit(#[RouteModel(exception: true)] Tag $tag): Response
    {
        return view('admin.tags.form', [
            'tag' => $tag,
            'formMode' => 'edit',
        ]);
    }

    #[Route(uri: '/{tag}', methods: ['PUT'], name: 'admin.tags.update')]
    public function update(
        UpdateTagRequest $request,
        #[RouteModel(exception: true)] Tag $tag
    ): Response {
        $request
            ->pipeInputs([
                'name' => fn($value) => is_string($value) ? trim($value) : $value,
                'slug' => fn($value) => is_string($value) ? trim($value) : $value,
                'description' => fn($value) => is_string($value) ? trim($value) : $value,
                'color' => fn($value) => is_string($value) ? trim($value) : $value,
            ])
            ->nullifyBlanks();

        $request->validate();

        $payload = $this->buildPayload($request->passed(), (int) $tag->id);

        if ($this->hasDuplicateName($payload['name'], (int) $tag->id)) {
            return back()->withErrors([
                'name' => 'The tag name has already been taken.',
            ])->withInput();
        }

        $tag->update($payload);

        return redirect()
            ->route('admin.tags.index')
            ->withSuccess('Tag updated successfully.');
    }

    #[Route(uri: '/{tag}', methods: ['DELETE'], name: 'admin.tags.destroy')]
    public function destroy(#[RouteModel(exception: true)] Tag $tag): Response
    {
        $tag->posts()->unlink();
        $tag->delete();

        return redirect()
            ->route('admin.tags.index')
            ->withSuccess('Tag deleted successfully.');
    }

    protected function buildPayload(array $payload, ?int $ignoreId = null): array
    {
        $name = trim((string) ($payload['name'] ?? ''));
        $slugInput = trim((string) ($payload['slug'] ?? ''));

        return [
            'name' => $name,
            'slug' => CmsSlugger::unique(Tag::class, $slugInput !== '' ? $slugInput : $name, $ignoreId),
            'description' => trim((string) ($payload['description'] ?? '')),
            'color' => trim((string) ($payload['color'] ?? '#8fa2ff')),
        ];
    }

    protected function hasDuplicateName(string $name, ?int $ignoreId = null): bool
    {
        $query = Tag::query()->where('name', $name);

        if ($ignoreId !== null) {
            $query->where('id', '!=', $ignoreId);
        }

        return $query->first() !== null;
    }
}
