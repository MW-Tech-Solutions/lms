<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AttendanceRecord extends Model
{
    public const STATUS_PRESENT = 'PRESENT';
    public const STATUS_ABSENT = 'ABSENT';
    public const STATUS_LATE = 'LATE';
    public const STATUS_EXCUSED = 'EXCUSED';

    public const METHOD_QR_SCAN = 'QR_SCAN';
    public const METHOD_MANUAL_OVERRIDE = 'MANUAL_OVERRIDE';
    public const METHOD_ZOOM_WEBHOOK = 'ZOOM_WEBHOOK';

    protected $fillable = [
        'session_id',
        'user_id',
        'status',
        'checkin_method',
        'verified_ip',
    ];

    public function session(): BelongsTo
    {
        return $this->belongsTo(AttendanceSession::class, 'session_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
