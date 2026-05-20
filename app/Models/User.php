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

    public static function roleOptions(): array
    {
        return [
            self::ROLE_ADMIN => 'Admin',
            self::ROLE_EDITOR => 'Editor',
            self::ROLE_AUTHOR => 'Author',
        ];
    }

    #[Hook('before_created')]
    protected function prepareForCreate(): void
    {
        $this->normalizeIdentityFields();

        if (trim((string) $this->password) !== '') {
            $this->password = bcrypt(trim((string) $this->password));
        }
    }

    #[Hook('before_updated')]
    protected function prepareForUpdate(): void
    {
        $this->normalizeIdentityFields();

        if ($this->isDirtyAttr('password') && trim((string) $this->password) !== '') {
            $this->password = bcrypt(trim((string) $this->password));
        }
    }

    protected function normalizeIdentityFields(): void
    {
        $this->name = trim((string) $this->name);
        $this->email = strtolower(trim((string) $this->email));

        $role = strtolower(trim((string) ($this->role ?? self::ROLE_EDITOR)));
        $this->role = array_key_exists($role, self::roleOptions()) ? $role : self::ROLE_EDITOR;

        $this->status = filter_var($this->status, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE) ?? (bool) $this->status;
    }
}
