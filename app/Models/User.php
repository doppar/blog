<?php

namespace App\Models;

use Phaseolies\Database\Entity\Attributes\Hook;
use Phaseolies\Database\Entity\Model;

class User extends Model
{
    public const ROLE_ADMIN = 'admin';
    public const ROLE_EDITOR = 'editor';
    public const ROLE_AUTHOR = 'author';

    protected $table = 'users';

    protected $creatable = [
        'name',
        'email',
        'role',
        'image',
        'status',
        'password',
        'remember_token',
        'two_factor_secret',
        'two_factor_recovery_codes',
    ];

    protected $unexposable = [
        'password',
        'remember_token',
        'two_factor_secret',
        'two_factor_recovery_codes',
    ];

    /**
     * Get the available role labels for forms and filters.
     *
     * @return array<string, string>
     */
    public static function roleOptions(): array
    {
        return [
            self::ROLE_ADMIN => 'Admin',
            self::ROLE_EDITOR => 'Editor',
            self::ROLE_AUTHOR => 'Author',
        ];
    }

    /**
     * Get the posts that belong to this user.
     *
     * @return mixed
     */
    public function posts()
    {
        return $this->linkMany(Post::class, 'user_id', 'id');
    }

    /**
     * Clear cached user counters after the record changes.
     *
     * @return void
     */
    #[Hook('after_created')]
    #[Hook('after_updated')]
    #[Hook('after_deleted')]
    protected function removeCache(): void
    {
        cache()->forget('user.total');
        cache()->forget('user.active');
        cache()->forget('user.admin');
        cache()->forget('user.editor');
        cache()->forget('user.author');
    }

    /**
     * Prepare the model state before a new user is created.
     *
     * @return void
     */
    #[Hook('before_created')]
    protected function prepareForCreate(): void
    {
        $this->normalizeIdentityFields();

        if (trim((string) $this->password) !== '') {
            $this->password = bcrypt(trim((string) $this->password));
        }
    }

    /**
     * Prepare the model state before an existing user is updated.
     *
     * @return void
     */
    #[Hook('before_updated')]
    protected function prepareForUpdate(): void
    {
        $this->normalizeIdentityFields();

        if ($this->isDirtyAttr('password') && trim((string) $this->password) !== '') {
            $this->password = bcrypt(trim((string) $this->password));
        }
    }

    /**
     * Normalize identity-related fields before persistence.
     *
     * @return void
     */
    protected function normalizeIdentityFields(): void
    {
        $this->name = trim((string) $this->name);
        $this->email = strtolower(trim((string) $this->email));

        $role = strtolower(trim((string) ($this->role ?? self::ROLE_EDITOR)));
        $this->role = array_key_exists($role, self::roleOptions()) ? $role : self::ROLE_EDITOR;

        $this->status = filter_var($this->status, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE) ?? (bool) $this->status;
    }
}
