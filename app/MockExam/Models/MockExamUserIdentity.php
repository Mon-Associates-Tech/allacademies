<?php
// app/MockExam/Models/MockExamUserIdentity.php
namespace App\MockExam\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MockExamUserIdentity extends Model
{
    protected $fillable = ['user_id', 'values'];

    protected function casts(): array
    {
        return ['values' => 'array'];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function valueFor(string $key): ?string
    {
        return $this->values[$key] ?? null;
    }
}
