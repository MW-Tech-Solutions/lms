<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Tenant extends Model
{
    use HasUuids;

    protected $fillable = [
        'id',
        'name',
        'slug',
        'subdomain',
        'school_code',
        'custom_domain',
        'branding_json',
        'admin_user_id',
        'is_active',
        'subscription_status',
    ];

    protected $casts = [
        'branding_json' => 'array',
        'is_active' => 'boolean',
    ];

    public function admin(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class, 'admin_user_id');
    }

    public function departments(): HasMany
    {
        return $this->hasMany(Department::class);
    }

    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    public function siteSettings(): HasMany
    {
        return $this->hasMany(SiteSetting::class);
    }

    public function courses(): HasMany
    {
        return $this->hasMany(Course::class);
    }

    public function cmsPages(): HasMany
    {
        return $this->hasMany(CmsPage::class);
    }
}
