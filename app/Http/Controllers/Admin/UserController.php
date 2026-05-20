<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreUserRequest;
use App\Http\Requests\Admin\UpdateUserRequest;
use App\Models\Media;
use App\Models\User;
use Phaseolies\Http\Request;
use Phaseolies\Http\Response;
use Phaseolies\Support\Facades\Storage;
use Phaseolies\Support\File;
use Phaseolies\Utilities\Attributes\Mapper;
use Phaseolies\Utilities\Attributes\Model as RouteModel;
use Phaseolies\Utilities\Attributes\Route;

#[Mapper(prefix: 'admin/users')]
class UserController extends Controller
{
    #[Route(uri: '/', name: 'admin.users.index')]
    public function index(Request $request): Response
    {
        $users = User::query()
            ->if(
                $search = trim((string) $request->input('search', '')),
                fn($builder) => $builder->search(['name', 'email', 'role'], $search)
            )
            ->if(
                $role = strtolower(trim((string) $request->input('role', ''))),
                fn($builder) => $builder->where('role', $role)
            )
            ->orderBy('updated_at', 'DESC')
            ->paginate(10);

        return view('admin.users.index', [
            'users' => $users,
            'roleOptions' => User::roleOptions(),
            'totalUsers' => User::count(),
            'activeUsers' => User::where('status', true)->count(),
            'adminUsers' => User::where('role', User::ROLE_ADMIN)->count(),
            'editorUsers' => User::where('role', User::ROLE_EDITOR)->count(),
            'authorUsers' => User::where('role', User::ROLE_AUTHOR)->count(),
        ]);
    }

    #[Route(uri: '/create', name: 'admin.users.create')]
    public function create(): Response
    {
        return view('admin.users.form', [
            'user' => null,
            'formMode' => 'create',
            'formInput' => session('input') ?? [],
            'roleOptions' => User::roleOptions(),
        ]);
    }

    #[Route(uri: '/', methods: ['POST'], name: 'admin.users.store')]
    public function store(StoreUserRequest $request): Response
    {
        $payload = $request->passed();
        $payload['image'] = $this->storeProfileImage($request->file('image_file'));

        User::create($payload);

        return redirect()->route('admin.users.index')->withSuccess('User created successfully.');
    }

    #[Route(uri: '/{user}/edit', name: 'admin.users.edit')]
    public function edit(#[RouteModel(exception: true)] User $user): Response
    {
        return view('admin.users.form', [
            'user' => $user,
            'formMode' => 'edit',
            'formInput' => session('input') ?? [],
            'roleOptions' => User::roleOptions(),
        ]);
    }

    #[Route(uri: '/{user}', methods: ['POST'], name: 'admin.users.update')]
    public function update(UpdateUserRequest $request, #[RouteModel(exception: true)] User $user): Response
    {
        $payload = $request->passed();

        if (!isset($payload['password']) || trim((string) $payload['password']) === '') {
            unset($payload['password']);
        }

        $uploadedImage = $this->storeProfileImage($request->file('image_file'));
        $previousImage = (string) ($user->image ?? '');

        if ($uploadedImage !== null) {
            $payload['image'] = $uploadedImage;
        }

        $user->update($payload);

        if ($uploadedImage !== null) {
            $this->deleteStoredProfileImage($previousImage);
        }

        return redirect()->route('admin.users.index')->withSuccess('User updated successfully.');
    }

    #[Route(uri: '/{user}', methods: ['DELETE'], name: 'admin.users.destroy')]
    public function destroy(#[RouteModel(exception: true)] User $user): Response
    {
        $this->deleteStoredProfileImage((string) ($user->image ?? ''));
        $user->delete();

        return redirect()->route('admin.users.index')->withSuccess('User deleted successfully.');
    }

    protected function storeProfileImage(mixed $file): ?string
    {
        if (!$file instanceof File || !$file->isValid()) {
            return null;
        }

        $directory = 'profile/' . date('Y/m');
        $extension = strtolower(trim((string) $file->getClientOriginalExtension()));

        if ($extension === '') {
            $mimeType = trim((string) $file->getClientOriginalType());
            $mimeParts = explode('/', $mimeType);
            $extension = strtolower(trim((string) ($mimeParts[1] ?? '')));
        }

        $fileName = date('His') . '_' . bin2hex(random_bytes(6));

        if ($extension !== '') {
            $fileName .= '.' . $extension;
        }

        $stored = Storage::disk('public')->store($directory, $file, $fileName);

        if ($stored === false) {
            throw new \RuntimeException('The selected profile image could not be stored.');
        }

        $relativePath = $directory . '/' . $fileName;

        return Media::publicUrl($relativePath);
    }

    protected function deleteStoredProfileImage(string $url): void
    {
        $relativePath = $this->resolveStoredProfileImagePath($url);

        if ($relativePath === null) {
            return;
        }

        Storage::disk('public')->delete($relativePath);
    }

    protected function resolveStoredProfileImagePath(string $value): ?string
    {
        $value = trim($value);

        if ($value === '') {
            return null;
        }

        if (str_starts_with($value, 'profile/')) {
            return $value;
        }

        $baseUrl = rtrim((string) config('filesystem.disks.public.url'), '/');

        if (!str_starts_with($value, $baseUrl . '/')) {
            return null;
        }

        $relativePath = ltrim(substr($value, strlen($baseUrl)), '/');

        if (!str_starts_with($relativePath, 'profile/')) {
            return null;
        }

        return $relativePath;
    }
}
