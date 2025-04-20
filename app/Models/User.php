<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Filament\Panel;
use Laravel\Sanctum\HasApiTokens;
use Namu\WireChat\Traits\Chatable;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Storage;
use Filament\Models\Contracts\HasAvatar;
use Illuminate\Notifications\Notifiable;
use Filament\Models\Contracts\FilamentUser;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Stephenjude\FilamentTwoFactorAuthentication\TwoFactorAuthenticatable;

class User extends Authenticatable implements FilamentUser, HasAvatar
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory;
    use Notifiable;
    use SoftDeletes;
    use Chatable;
    use TwoFactorAuthenticatable;

    const ROLE_ADMIN = 'administrator';
    const ROLE_USER = 'user';

    const ROLE = [
        self::ROLE_ADMIN,
        self::ROLE_USER,
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'profile_photo_url',
        'custom_fields',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'two_factor_recovery_codes',
        'two_factor_secret',
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array<int, string>
     */
    protected $appends = [
        'profile_photo_url',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'custom_fields' => 'array'
        ];
    }

    protected static function booted()
    {
        if (auth()->user()) {
            $check_ip = Identifier::where("user_id", auth()->user()->id)->exists();

            if ($check_ip) {
                Identifier::where("user_id", auth()->user()->id)
                    ->update([
                        "ip_address" => Request::ip()
                    ]);
            } else {
                Identifier::firstOrCreate([
                    "user_id" => auth()->user()->id,
                    "ip_address" => Request::ip(),
                    "mac_address" => implode(':', str_split(substr(md5(mt_rand()), 0, 12), 2))
                ]);
            }
        }
    }

    public function isAdmin(): bool
    {
        return $this->role === self::ROLE_ADMIN;
    }

    public function isUser(): bool
    {
        return $this->role === self::ROLE_USER;
    }

    public function designations(): BelongsTo
    {
        return $this->belongsTo(Designation::class, 'designation_id');
    }

    public function divisions(): BelongsTo
    {
        return $this->belongsTo(Division::class, 'division_id');
    }

    public function entries(): HasMany
    {
        return $this->hasMany(Entry::class);
    }

    public function identifiers(): HasMany
    {
        return $this->hasMany(Identifier::class);
    }

    public function canCreateChats(): bool
    {
        return true;
    }

    public function getFilamentAvatarUrl(): ?string
    {
        $avatarColumn = config('filament-edit-profile.avatar_column', 'avatar_url');
        return $this->$avatarColumn ? $this->profile_photo_url : null;
    }

    public function getProfilePhotoUrlAttribute()
    {
        return $this->attributes['profile_photo_url'] ? Storage::url($this->attributes['profile_photo_url']) : null;
    }

    public function getCoverUrlAttribute(): ?string
    {
        return $this->profile_photo_url ?? null;
    }

    public function searchChatables(string $query): ?Collection
    {
        $searchableFields = ['name'];
        return User::where(function ($queryBuilder) use ($searchableFields, $query) {
            foreach ($searchableFields as $field) {
                $queryBuilder->orWhere($field, 'LIKE', '%' . $query . '%');
            }
        })
            ->limit(20)
            ->get();
    }

    public function canAccessPanel(Panel $panel): bool
    {
        return match ($panel->getId()) {
            'dashboard' => $this->isUser() || $this->isAdmin(),
            'admin' => $this->isAdmin(),
            default => false,
        };
    }
}
