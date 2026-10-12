<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AntiCheatLog extends Model
{
    public const EVENT_TAB_SWITCH = 'TAB_SWITCH';
    public const EVENT_COPY_ATTEMPT = 'COPY_ATTEMPT';
    public const EVENT_DEVICE_DISCONNECTED = 'DEVICE_DISCONNECTED';
    public const EVENT_WEBCAM_FLAG = 'WEBCAM_FLAG';

    public $timestamps = false;

    protected $fillable = [
        'attempt_id',
        'event_type',
        'snapshot_url',
        'recorded_at',
    ];

    protected $casts = [
        'recorded_at' => 'datetime',
    ];

    public function attempt(): BelongsTo
    {
        return $this->belongsTo(QuizAttempt::class, 'attempt_id');
    }
}
