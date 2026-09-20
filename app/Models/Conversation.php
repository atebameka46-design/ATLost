<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Conversation extends Model
{
    protected $fillable = ['user_one_id', 'user_two_id', 'document_report_id', 'last_message_at'];

    protected function casts(): array
    {
        return ['last_message_at' => 'datetime'];
    }

    public function userOne(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_one_id');
    }

    public function userTwo(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_two_id');
    }

    public function documentReport(): BelongsTo
    {
        return $this->belongsTo(DocumentReport::class);
    }

    public function messages(): HasMany
    {
        return $this->hasMany(Message::class);
    }

    public function getOtherUserAttribute(): ?User
    {
        return $this->userOne;
    }

    public function getOtherUserFor(User $user): ?User
    {
        if ($this->user_one_id === $user->id) {
            return $this->userTwo;
        }

        if ($this->user_two_id === $user->id) {
            return $this->userOne;
        }

        return null;
    }

    public function scopeForUser($query, User $user): void
    {
        $query->where(function ($q) use ($user) {
            $q->where('user_one_id', $user->id)->orWhere('user_two_id', $user->id);
        });
    }

    public function unreadCountFor(User $user): int
    {
        return $this->messages()->where('sender_id', '!=', $user->id)->whereNull('read_at')->count();
    }
}
