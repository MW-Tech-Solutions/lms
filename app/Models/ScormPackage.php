<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ScormPackage extends Model
{
    protected $fillable = [
        'tenant_id',
        'title',
        'version',
        'entry_url',
        'storage_path',
    ];

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function tracks(): HasMany
    {
        return $this->hasMany(ScormTrack::class);
    }
}
