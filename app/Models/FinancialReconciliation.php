<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FinancialReconciliation extends Model
{
    protected $fillable = [
        'school_id',
        'reconciled_by',
        'source_type',
        'source_id',
        'bank_reference',
        'statement_date',
        'statement_amount',
        'statement_currency',
        'note',
        'reconciled_at',
    ];

    protected $casts = [
        'statement_date' => 'date',
        'statement_amount' => 'decimal:2',
        'reconciled_at' => 'datetime',
    ];

    public function school(): BelongsTo
    {
        return $this->belongsTo(School::class);
    }

    public function reconciler(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reconciled_by');
    }
}
