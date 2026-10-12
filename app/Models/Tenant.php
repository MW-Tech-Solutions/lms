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
        'subdomain',
        'custom_domain',
        'branding_json',
        'is_active',
    ];

    protected $casts = [
        'branding_json' => 'array',
        'is_active' => 'boolean',
    ];

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
