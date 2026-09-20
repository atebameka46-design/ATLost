<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DocumentClaim extends Model
{
    protected $fillable = [
        'document_report_id',
        'claimant_id',
        'status',
        'appointment_at',
        'payment_status',
        'payment_method',
        'payment_reference',
        'payment_proof_path',
        'paid_amount',
        'service_fee_amount',
        'payment_submitted_at',
        'payment_verified_at',
        'payment_verified_by',
        'completed_at',
        'completed_by',
        'payout_status',
        'payout_amount',
        'paid_out_at',
    ];

    protected $casts = [
        'appointment_at' => 'datetime',
        'paid_amount' => 'decimal:2',
        'service_fee_amount' => 'decimal:2',
        'payment_submitted_at' => 'datetime',
        'payment_verified_at' => 'datetime',
        'completed_at' => 'datetime',
        'payout_amount' => 'decimal:2',
        'paid_out_at' => 'datetime',
    ];

    public function report(): BelongsTo
    {
        return $this->belongsTo(DocumentReport::class, 'document_report_id');
    }

    public function claimant(): BelongsTo
    {
        return $this->belongsTo(User::class, 'claimant_id');
    }

    public function paymentVerifier(): BelongsTo
    {
        return $this->belongsTo(User::class, 'payment_verified_by');
    }

    public function completer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'completed_by');
    }

    public function netPayoutAmount(): float
    {
        return max(0, (float) $this->paid_amount - (float) $this->service_fee_amount);
    }

    public function statusLabel(): string
    {
        return ['pending' => 'En attente', 'accepted' => 'Acceptée', 'cancelled' => 'Annulée', 'completed' => 'Terminée'][$this->status] ?? $this->status;
    }
}
