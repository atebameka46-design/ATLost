<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DocumentSearch extends Model
{
    protected $fillable = ['user_id', 'query', 'document_type'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
