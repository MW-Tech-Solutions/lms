<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WikiPage extends Model
{
    protected $fillable = [
        'wiki_id',
        'title',
        'content_markdown',
        'last_edited_by_user_id',
    ];

    public function wiki(): BelongsTo
    {
        return $this->belongsTo(CourseWiki::class, 'wiki_id');
    }

    public function lastEditor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'last_edited_by_user_id');
    }
}
