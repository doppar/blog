<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreCategoryRequest;
use App\Http\Requests\Admin\UpdateCategoryRequest;
use App\Models\Category;
use Phaseolies\Http\Request;
use Phaseolies\Http\Response;
use Phaseolies\Utilities\Attributes\Mapper;
use Phaseolies\Utilities\Attributes\Middleware;
use Phaseolies\Utilities\Attributes\Model as RouteModel;
use Phaseolies\Utilities\Attributes\Route;

#[Middleware(['auth', 'admin'])]
#[Mapper(prefix: 'admin/categories')]
class CategoryController extends Controller
{
    #[Route(uri: '/', name: 'admin.categories.index')]
    public function index(Request $request, Category $category): Response
    {
        $categories = $category->embedCount('posts')
            ->if($request->q, fn($q) => $q->search(['name', 'slug'], $request->q))
            ->if($request->status, fn($q) => $q->whereStatus($request->status === 'active'))
            ->orderBy('id', 'DESC')
            ->paginate(10);

        return view('admin.categories.index', compact('categories'));
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
        Category::create($request->passed());

        return redirect()->route('admin.categories.index')->withSuccess('Category created successfully.');
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
    public function update(UpdateCategoryRequest $request, #[RouteModel(exception: true)] Category $category): Response
    {
        $category->update($request->passed());

        return redirect()->route('admin.categories.index')->withSuccess('Category updated successfully.');
    }

    #[Route(uri: '/{category}', methods: ['DELETE'], name: 'admin.categories.destroy')]
    public function destroy(#[RouteModel(exception: true)] Category $category): Response
    {
        if ($category->posts()->exists()) {
            return back()->withError('Move or remove the posts in this category before deleting it.');
        }

        $category->delete();

        return redirect()->route('admin.categories.index')->withSuccess('Category deleted successfully.');
    }
}
