<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CmsSection extends Model
{
    public const TYPE_HERO = 'HERO';
    public const TYPE_CATEGORIES = 'CATEGORIES';
    public const TYPE_CATALOG = 'CATALOG';
    public const TYPE_MENTORS = 'MENTORS';
    public const TYPE_EVENTS = 'EVENTS';
    public const TYPE_FAQ = 'FAQ';
    public const TYPE_METRICS = 'METRICS';
    public const TYPE_PRICING = 'PRICING';
    public const TYPE_DYNAMIC_FORM = 'DYNAMIC_FORM';
    public const TYPE_FOOTER = 'FOOTER';
    public const TYPE_CUSTOM_HTML = 'CUSTOM_HTML';

    protected $fillable = [
        'page_id',
        'section_type',
        'order_index',
        'content_json',
        'styles_json',
    ];

    protected $casts = [
        'content_json' => 'array',
        'styles_json' => 'array',
    ];

    public function page(): BelongsTo
    {
        return $this->belongsTo(CmsPage::class, 'page_id');
    }
}
