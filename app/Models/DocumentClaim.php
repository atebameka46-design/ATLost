<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DocumentClaim extends Model
{
    protected $fillable = ['document_report_id', 'claimant_id', 'status', 'appointment_at', 'payment_status'];

    protected $casts = ['appointment_at' => 'datetime'];

    public function report(): BelongsTo
    {
        return $this->belongsTo(DocumentReport::class, 'document_report_id');
    }

    public function claimant(): BelongsTo
    {
        return $this->belongsTo(User::class, 'claimant_id');
    }

    public function statusLabel(): string
    {
        return ['pending' => 'En attente', 'accepted' => 'Acceptée', 'cancelled' => 'Annulée', 'completed' => 'Terminée'][$this->status] ?? $this->status;
    }
}
