<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreCategoryRequest;
use App\Http\Requests\Admin\UpdateCategoryRequest;
use App\Models\Category;
use App\Support\CmsSlugger;
use Phaseolies\Http\Request;
use Phaseolies\Http\Response;
use Phaseolies\Utilities\Attributes\Mapper;
use Phaseolies\Utilities\Attributes\Model as RouteModel;
use Phaseolies\Utilities\Attributes\Route;

#[Mapper(prefix: 'admin/categories')]
class CategoryController extends Controller
{
    #[Route(uri: '/', name: 'admin.categories.index')]
    public function index(Request $request): Response
    {
        $request
            ->mergeIfMissing([
                'search' => '',
                'status' => '',
            ])
            ->pipeInputs([
                'search' => fn($value) => is_string($value) ? trim($value) : '',
                'status' => fn($value) => is_scalar($value) ? trim((string) $value) : '',
            ]);

        $filters = [
            'search' => (string) $request->input('search', ''),
            'status' => (string) $request->input('status', ''),
        ];

        $query = Category::query()
            ->embedCount('posts')
            ->if(
                $filters['search'],
                fn($builder) => $builder->search(['name', 'slug', 'description'], $filters['search'])
            )
            ->if(
                $filters['status'],
                fn($builder) => $builder->where('status', $filters['status'] === '1')
            );

        return view('admin.categories.index', [
            'categories' => $query->orderBy('updated_at', 'DESC')->paginate(10),
            'filters' => $filters,
        ]);
    }

    #[Route(uri: '/create', name: 'admin.categories.create')]
    public function create(): Response
    {
        return view('admin.categories.form', [
            'category' => null,
            'formMode' => 'create',
            'formInput' => session('input') ?? [],
        ]);
    }

    #[Route(uri: '/', methods: ['POST'], name: 'admin.categories.store')]
    public function store(StoreCategoryRequest $request): Response
    {
        $request
            ->pipeInputs([
                'name' => fn($value) => is_string($value) ? trim($value) : $value,
                'slug' => fn($value) => is_string($value) ? trim($value) : $value,
                'description' => fn($value) => is_string($value) ? trim($value) : $value,
                'accent_color' => fn($value) => is_string($value) ? trim($value) : $value,
            ])
            ->nullifyBlanks();

        $request->validate();

        $payload = $this->buildPayload($request->passed());

        if ($this->hasDuplicateName($payload['name'])) {
            return back()->withErrors([
                'name' => 'The category name has already been taken.',
            ])->withInput();
        }

        Category::create($payload);

        return redirect()
            ->route('admin.categories.index')
            ->withSuccess('Category created successfully.');
    }

    #[Route(uri: '/{category}/edit', name: 'admin.categories.edit')]
    public function edit(#[RouteModel(exception: true)] Category $category): Response
    {
        return view('admin.categories.form', [
            'category' => $category,
            'formMode' => 'edit',
            'formInput' => session('input') ?? [],
        ]);
    }

    #[Route(uri: '/{category}', methods: ['PUT'], name: 'admin.categories.update')]
    public function update(
        UpdateCategoryRequest $request,
        #[RouteModel(exception: true)] Category $category
    ): Response {
        $request
            ->pipeInputs([
                'name' => fn($value) => is_string($value) ? trim($value) : $value,
                'slug' => fn($value) => is_string($value) ? trim($value) : $value,
                'description' => fn($value) => is_string($value) ? trim($value) : $value,
                'accent_color' => fn($value) => is_string($value) ? trim($value) : $value,
            ])
            ->nullifyBlanks();

        $request->validate();

        $payload = $this->buildPayload($request->passed(), (int) $category->id);

        if ($this->hasDuplicateName($payload['name'], (int) $category->id)) {
            return back()->withErrors([
                'name' => 'The category name has already been taken.',
            ])->withInput();
        }

        $category->update($payload);

        return redirect()
            ->route('admin.categories.index')
            ->withSuccess('Category updated successfully.');
    }

    #[Route(uri: '/{category}', methods: ['DELETE'], name: 'admin.categories.destroy')]
    public function destroy(#[RouteModel(exception: true)] Category $category): Response
    {
        if ($category->posts()->exists()) {
            return redirect()
                ->route('admin.categories.index')
                ->withError('Move or remove the posts in this category before deleting it.');
        }

        $category->delete();

        return redirect()
            ->route('admin.categories.index')
            ->withSuccess('Category deleted successfully.');
    }

    protected function buildPayload(array $payload, ?int $ignoreId = null): array
    {
        $name = trim((string) ($payload['name'] ?? ''));
        $slugInput = trim((string) ($payload['slug'] ?? ''));
        $description = trim((string) ($payload['description'] ?? ''));

        return [
            'name' => $name,
            'slug' => CmsSlugger::unique(Category::class, $slugInput !== '' ? $slugInput : $name, $ignoreId),
            'description' => $description,
            'accent_color' => trim((string) ($payload['accent_color'] ?? '#6f7bf7')),
            'status' => (string) ($payload['status'] ?? '0') === '1',
        ];
    }

    protected function hasDuplicateName(string $name, ?int $ignoreId = null): bool
    {
        $query = Category::query()->where('name', $name);

        if ($ignoreId !== null) {
            $query->where('id', '!=', $ignoreId);
        }

        return $query->first() !== null;
    }
}
