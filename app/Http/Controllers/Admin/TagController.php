<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreTagRequest;
use App\Http\Requests\Admin\UpdateTagRequest;
use App\Models\Tag;
use Phaseolies\Http\Request;
use Phaseolies\Http\Response;
use Phaseolies\Utilities\Attributes\Mapper;
use Phaseolies\Utilities\Attributes\Middleware;
use Phaseolies\Utilities\Attributes\Model as RouteModel;
use Phaseolies\Utilities\Attributes\Route;

#[Middleware(['auth', 'admin'])]
#[Mapper(prefix: 'admin/tags')]
class TagController extends Controller
{
    #[Route(uri: '/', name: 'admin.tags.index')]
    public function index(Request $request, Tag $tag): Response
    {
        $search = (string) trim($request->search);

        $tags = $tag->embedCount('posts')
            ->if($search, fn($q) => $q->search(['name', 'slug', 'description'], $search))
            ->orderBy('updated_at', 'DESC')
            ->paginate(12);

        return view('admin.tags.index', compact('tags', 'search'));
    }

    #[Route(uri: '/create', name: 'admin.tags.create')]
    public function create(): Response
    {
        return view('admin.tags.form', [
            'tag' => null,
            'formMode' => 'create',
            'formInput' => session('input') ?? [],
        ]);
    }

    #[Route(uri: '/', methods: ['POST'], name: 'admin.tags.store')]
    public function store(StoreTagRequest $request): Response
    {
        Tag::create($request->passed());

        return redirect()->route('admin.tags.index')->withSuccess('Tag created successfully.');
    }

    #[Route(uri: '/{tag}/edit', name: 'admin.tags.edit')]
    public function edit(#[RouteModel(exception: true)] Tag $tag): Response
    {
        return view('admin.tags.form', [
            'tag' => $tag,
            'formMode' => 'edit',
            'formInput' => session('input') ?? [],
        ]);
    }

    #[Route(uri: '/{tag}', methods: ['PUT'], name: 'admin.tags.update')]
    public function update(UpdateTagRequest $request, #[RouteModel(exception: true)] Tag $tag): Response
    {
        $tag->update($request->passed());

        return redirect()->route('admin.tags.index')->withSuccess('Tag updated successfully.');
    }

    #[Route(uri: '/{tag}', methods: ['DELETE'], name: 'admin.tags.destroy')]
    public function destroy(#[RouteModel(exception: true)] Tag $tag): Response
    {
        $tag->posts()->unlink();
        $tag->delete();

        return redirect()->route('admin.tags.index')->withSuccess('Tag deleted successfully.');
    }
}
