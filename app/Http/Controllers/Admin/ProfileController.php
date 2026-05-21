<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ConfirmTwoFactorSetupRequest;
use App\Http\Requests\Admin\UpdateProfileInformationRequest;
use App\Http\Requests\Admin\UpdateProfilePasswordRequest;
use App\Models\Media;
use App\Models\User;
use OTPHP\TOTP;
use ParagonIE\ConstantTime\Base32;
use Phaseolies\Http\Response;
use Phaseolies\Support\Facades\Auth;
use Phaseolies\Support\Facades\Crypt;
use Phaseolies\Support\Facades\Hash;
use Phaseolies\Support\Facades\Storage;
use Phaseolies\Support\File;
use Phaseolies\Utilities\Attributes\Mapper;
use Phaseolies\Utilities\Attributes\Middleware;
use Phaseolies\Utilities\Attributes\Route;
use Symfony\Component\Clock\NativeClock;

#[Mapper(prefix: 'admin/profile')]
#[Middleware(['auth'])]
class ProfileController extends Controller
{
    private const PENDING_SETUP_SESSION_KEY = 'admin.profile.two_factor_setup';
    private const RECOVERY_CODES_SESSION_KEY = 'admin.profile.two_factor_recovery_codes';

    #[Route(uri: '/', name: 'admin.profile.index')]
    public function index(): Response
    {
        /** @var User $user */
        $user = Auth::user();

        return view('admin.profile', [
            'user' => $user,
            'twoFactorEnabled' => Auth::hasTwoFactorEnabled($user),
            'pendingTwoFactorSetup' => session(self::PENDING_SETUP_SESSION_KEY),
            'recoveryCodes' => session(self::RECOVERY_CODES_SESSION_KEY) ?? [],
        ]);
    }

    #[Route(uri: '/information', methods: ['POST'], name: 'admin.profile.information')]
    public function updateInformation(UpdateProfileInformationRequest $request): Response
    {
        /** @var User $user */
        $user = Auth::user();
        $payload = $request->passed();
        $email = strtolower(trim((string) ($payload['email'] ?? '')));

        $uploadedImage = $this->storeProfileImage($request->file('image_file'));
        $previousImage = (string) ($user->image ?? '');

        if ($uploadedImage !== null) {
            $payload['image'] = $uploadedImage;
        }

        $user->update($payload);

        if ($uploadedImage !== null) {
            $this->deleteStoredProfileImage($previousImage);
        }

        return redirect()->route('admin.profile.index')->withSuccess('Your profile information has been updated.');
    }

    #[Route(uri: '/password', methods: ['POST'], name: 'admin.profile.password')]
    public function updatePassword(UpdateProfilePasswordRequest $request): Response
    {
        /** @var User $user */
        $user = Auth::user();
        $currentPassword = (string) $request->input('current_password');
        $newPassword = trim((string) $request->input('password'));

        if (!Hash::check($currentPassword, (string) $user->password)) {
            return back()->withErrors([
                'current_password' => 'The current password does not match our records.',
            ])->withInput();
        }

        if (Hash::check($newPassword, (string) $user->password)) {
            return back()->withErrors([
                'password' => 'The new password must be different from the current password.',
            ])->withInput();
        }

        $user->update([
            'password' => $newPassword,
        ]);

        return redirect()->route('admin.profile.index')->withSuccess('Your password has been updated.');
    }

    #[Route(uri: '/two-factor/start', methods: ['POST'], name: 'admin.profile.two-factor.start')]
    public function startTwoFactorSetup(): Response
    {
        /** @var User $user */
        $user = Auth::user();

        if (Auth::hasTwoFactorEnabled($user)) {
            return redirect()->route('admin.profile.index')->withError('Two-factor authentication is already enabled.');
        }

        session()->put(self::PENDING_SETUP_SESSION_KEY, $this->makePendingTwoFactorSetup($user));

        return redirect()->route('admin.profile.index');
    }

    #[Route(uri: '/two-factor/confirm', methods: ['POST'], name: 'admin.profile.two-factor.confirm')]
    public function confirmTwoFactorSetup(ConfirmTwoFactorSetupRequest $request): Response
    {
        /** @var User $user */
        $user = Auth::user();
        $setup = session(self::PENDING_SETUP_SESSION_KEY);

        if (!is_array($setup) || empty($setup['secret'])) {
            return redirect()->route('admin.profile.index')->withError('Start two-factor setup again to continue.');
        }

        if (Auth::hasTwoFactorEnabled($user)) {
            session()->forget(self::PENDING_SETUP_SESSION_KEY);

            return redirect()->route('admin.profile.index')->withError('Two-factor authentication is already enabled.');
        }

        $code = trim((string) $request->input('two_factor_code'));

        if (!$this->verifyPendingTwoFactorCode($setup, $code, (int) $user->id)) {
            return back()->withErrors([
                'two_factor_code' => 'The authentication code is invalid.',
            ])->withInput();
        }

        $user->update([
            'two_factor_secret' => (string) $setup['secret_encrypted'],
            'two_factor_recovery_codes' => (string) $setup['recovery_codes_encrypted'],
        ]);

        session()->forget(self::PENDING_SETUP_SESSION_KEY);
        session()->flash(self::RECOVERY_CODES_SESSION_KEY, $setup['recovery_codes'] ?? []);

        return redirect()->route('admin.profile.index')->withSuccess('Two-factor authentication has been enabled.');
    }

    #[Route(uri: '/two-factor/cancel', methods: ['POST'], name: 'admin.profile.two-factor.cancel')]
    public function cancelTwoFactorSetup(): Response
    {
        session()->forget(self::PENDING_SETUP_SESSION_KEY);

        return redirect()->route('admin.profile.index')->withSuccess('Two-factor setup has been cancelled.');
    }

    #[Route(uri: '/two-factor/disable', methods: ['POST'], name: 'admin.profile.two-factor.disable')]
    public function disableTwoFactor(): Response
    {
        /** @var User $user */
        $user = Auth::user();

        if (!Auth::hasTwoFactorEnabled($user)) {
            return redirect()->route('admin.profile.index')->withError('Two-factor authentication is not enabled yet.');
        }

        Auth::disableTwoFactorAuth();
        session()->forget(self::PENDING_SETUP_SESSION_KEY);
        session()->forget(self::RECOVERY_CODES_SESSION_KEY);

        return redirect()->route('admin.profile.index')->withSuccess('Two-factor authentication has been disabled.');
    }

    #[Route(uri: '/two-factor/recovery-codes', methods: ['POST'], name: 'admin.profile.two-factor.recovery-codes')]
    public function regenerateRecoveryCodes(): Response
    {
        /** @var User $user */
        $user = Auth::user();

        if (!Auth::hasTwoFactorEnabled($user)) {
            return redirect()->route('admin.profile.index')->withError('Enable two-factor authentication first.');
        }

        session()->flash(self::RECOVERY_CODES_SESSION_KEY, Auth::generateNewRecoveryCodes());

        return redirect()->route('admin.profile.index')->withSuccess('New recovery codes have been generated.');
    }

    protected function makePendingTwoFactorSetup(User $user): array
    {
        $secret = Base32::encodeUpper(random_bytes(20));
        $recoveryCodes = $this->generateRecoveryCodes();
        $totp = $this->makeTotp($secret, (int) $user->id);

        return [
            'secret' => $secret,
            'secret_encrypted' => Crypt::encrypt($secret),
            'recovery_codes' => $recoveryCodes,
            'recovery_codes_encrypted' => Crypt::encrypt(json_encode($recoveryCodes)),
            'qr_code_svg' => Auth::generateTwoFactorQrCode($totp->getProvisioningUri()),
        ];
    }

    protected function generateRecoveryCodes(): array
    {
        $codes = [];

        for ($i = 0; $i < 8; $i++) {
            $codes[] = strtoupper(bin2hex(random_bytes(5)));
        }

        return $codes;
    }

    protected function makeTotp(string $secret, int $userId): TOTP
    {
        $totp = TOTP::create(
            $secret,
            30,
            'sha1',
            6,
            $userId,
            new NativeClock()
        );

        $host = parse_url((string) config('app.url'), PHP_URL_HOST);
        $issuer = preg_replace('/[^a-zA-Z0-9.\\-_]/', '', (string) $host);

        $totp->setLabel(strtolower(trim((string) config('app.name'))));
        $totp->setIssuer($issuer);

        return $totp;
    }

    protected function verifyPendingTwoFactorCode(array $setup, string $code, int $userId): bool
    {
        try {
            return $this->makeTotp((string) $setup['secret'], $userId)->verify($code, null, 1);
        } catch (\Throwable) {
            return false;
        }
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

        return Media::publicUrl($directory . '/' . $fileName);
    }

    protected function deleteStoredProfileImage(string $url): void
    {
        $relativePath = $this->resolveStoredProfileImagePath($url);

        if ($relativePath !== null) {
            Storage::disk('public')->delete($relativePath);
        }
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

        return str_starts_with($relativePath, 'profile/') ? $relativePath : null;
    }
}
