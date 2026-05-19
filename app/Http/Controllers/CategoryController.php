<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCategoryRequest;
use App\Http\Requests\UpdateCategoryRequest;
use App\Models\Category;
use Phaseolies\Http\Request;
use Phaseolies\Http\Response;
use Phaseolies\Http\Response\JsonResponse;
use Phaseolies\Utilities\Attributes\Model as RouteModel;
use Phaseolies\Utilities\Attributes\Route;

class CategoryController extends Controller
{
    #[Route(uri: 'categories', name: 'categories.index')]
    public function index(): Response
    {
        return view('categories.index');
    }

    #[Route(uri: 'categories/data', name: 'categories.data')]
    public function data(Request $request): JsonResponse
    {
        $payload = $request->all() ?? [];
        $draw = max((int) ($payload['draw'] ?? 1), 1);
        $start = max((int) ($payload['start'] ?? 0), 0);
        $length = (int) ($payload['length'] ?? 6);
        $length = $length > 0 && $length <= 100 ? $length : 6;
        $search = trim((string) ($payload['search']['value'] ?? ''));

        $orderColumnMap = [
            1 => 'status',
            2 => 'name',
            3 => 'excerpt',
            4 => 'updated_at',
            5 => 'created_at',
        ];

        $orderColumnIndex = (int) ($payload['order'][0]['column'] ?? 5);
        $orderColumn = $orderColumnMap[$orderColumnIndex] ?? 'created_at';
        $orderDirection = strtolower((string) ($payload['order'][0]['dir'] ?? 'desc')) === 'asc'
            ? 'ASC'
            : 'DESC';

        $total = Category::query()->count();
        $query = Category::query();

        if ($search !== '') {
            $this->applySearch($query, $search);
        }

        $filtered = $query->count();
        $categories = $query
            ->orderBy($orderColumn, $orderDirection)
            ->limit($length)
            ->offset($start)
            ->get();

        $data = [];

        foreach ($categories as $category) {
            $isActive = (bool) $category->status;

            $data[] = [
                'id' => (int) $category->id,
                'status' => $isActive ? 1 : 0,
                'status_label' => $isActive ? 'Active' : 'Inactive',
                'status_class' => $isActive ? 'is-active' : 'is-inactive',
                'name' => (string) $category->name,
                'excerpt' => trim((string) ($category->excerpt ?? '')),
                'updated_at_label' => $this->formatDate($category->updated_at ?? null),
                'created_at_label' => $this->formatDate($category->created_at ?? null),
            ];
        }

        return response()->json([
            'draw' => $draw,
            'recordsTotal' => $total,
            'recordsFiltered' => $filtered,
            'data' => $data,
        ]);
    }

    #[Route(uri: 'categories', methods: ['POST'], name: 'categories.store')]
    public function store(StoreCategoryRequest $request): JsonResponse
    {
        $payload = $this->extractCategoryPayload($request->validate());

        if ($this->hasDuplicateName($payload['name'])) {
            return $this->validationError([
                'name' => [trans('validation.unique', [':attribute' => 'name'])],
            ]);
        }

        $category = Category::create($payload);

        return response()->json([
            'message' => 'Category created successfully.',
            'category' => [
                'id' => (int) $category->id,
                'name' => (string) $category->name,
            ],
        ], 201);
    }

    #[Route(uri: 'categories/{category}/edit', name: 'categories.edit')]
    public function edit(#[RouteModel(exception: true)] Category $category): JsonResponse
    {
        return response()->json([
            'category' => [
                'id' => (int) $category->id,
                'name' => (string) $category->name,
                'excerpt' => (string) ($category->excerpt ?? ''),
                'status' => (bool) $category->status,
            ],
        ]);
    }

    #[Route(uri: 'categories/{category}', methods: ['PUT'], name: 'categories.update')]
    public function update(
        UpdateCategoryRequest $request,
        #[RouteModel(exception: true)] Category $category
    ): JsonResponse {
        $payload = $this->extractCategoryPayload($request->validate());

        if ($this->hasDuplicateName($payload['name'], (int) $category->id)) {
            return $this->validationError([
                'name' => [trans('validation.unique', [':attribute' => 'name'])],
            ]);
        }

        $category->update($payload);

        return response()->json([
            'message' => 'Category updated successfully.',
        ]);
    }

    #[Route(uri: 'categories/{category}', methods: ['DELETE'], name: 'categories.destroy')]
    public function destroy(#[RouteModel(exception: true)] Category $category): JsonResponse
    {
        $category->delete();

        return response()->json([
            'message' => 'Category deleted successfully.',
        ]);
    }

    protected function applySearch($query, string $search): void
    {
        $normalized = strtolower($search);

        $query->where(function ($nestedQuery) use ($search, $normalized) {
            $nestedQuery
                ->orWhereLike('name', $search)
                ->orWhereLike('excerpt', $search);

            if (in_array($normalized, ['active', 'enabled', 'published'], true)) {
                $nestedQuery->orWhere('status', 1);
            }

            if (in_array($normalized, ['inactive', 'disabled', 'draft'], true)) {
                $nestedQuery->orWhere('status', 0);
            }
        });
    }

    protected function extractCategoryPayload(array $payload): array
    {
        return [
            'name' => trim((string) ($payload['name'] ?? '')),
            'excerpt' => trim((string) ($payload['excerpt'] ?? '')),
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

    protected function validationError(array $errors): JsonResponse
    {
        return response()->json([
            'message' => trans('validation.default'),
            'errors' => $errors,
        ], 422);
    }

    protected function formatDate(?string $value): string
    {
        if (!$value) {
            return 'N/A';
        }

        $timestamp = strtotime($value);

        if ($timestamp === false) {
            return 'N/A';
        }

        return date('Y/m/d', $timestamp);
    }
}
