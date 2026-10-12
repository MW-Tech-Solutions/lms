<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ScormTrack extends Model
{
    protected $fillable = [
        'user_id',
        'scorm_package_id',
        'cmi_key',
        'cmi_value',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function scormPackage(): BelongsTo
    {
        return $this->belongsTo(ScormPackage::class);
    }
}
