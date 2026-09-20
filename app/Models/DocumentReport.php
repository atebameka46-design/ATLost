<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class DocumentReport extends Model
{
    protected $fillable = ['user_id', 'document_type', 'owner_name', 'location', 'phone', 'reward_amount', 'description', 'photo_path', 'status'];

    protected function casts(): array
    {
        return ['reward_amount' => 'decimal:2'];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function claims(): HasMany
    {
        return $this->hasMany(DocumentClaim::class);
    }

    public function statusLabel(): string
    {
        return ['pending' => 'En attente', 'approved' => 'Publié', 'resolved' => 'Restitué', 'rejected' => 'Refusé'][$this->status] ?? $this->status;
    }
}
