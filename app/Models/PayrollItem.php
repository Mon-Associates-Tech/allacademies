<?php

namespace App\Models;

use App\Support\PayrollAmount;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PayrollItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'payroll_run_id',
        'teacher_id',
        'employee_id',
        'employee_name',
        'department',
        'base_salary',
        'allowances',
        'deductions',
        'notes',
        'confirmed_by',
        'confirmed_at',
    ];

    protected $casts = [
        'base_salary' => 'decimal:2',
        'allowances' => 'decimal:2',
        'deductions' => 'decimal:2',
        'confirmed_at' => 'datetime',
    ];

    public function payrollRun(): BelongsTo
    {
        return $this->belongsTo(PayrollRun::class);
    }

    public function teacher(): BelongsTo
    {
        return $this->belongsTo(Teacher::class);
    }

    public function confirmer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'confirmed_by');
    }

    public function netPayInCents(): int
    {
        return PayrollAmount::netPayInCents($this->base_salary, $this->allowances, $this->deductions);
    }

    public function getNetPayAttribute(): string
    {
        return PayrollAmount::formatCents($this->netPayInCents());
    }
}
