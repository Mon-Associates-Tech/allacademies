<?php
// app/MockExam/Models/MockExamIdentityField.php
namespace App\MockExam\Models;

use Illuminate\Database\Eloquent\Model;

class MockExamIdentityField extends Model
{
    protected $fillable = ['key', 'label', 'type', 'pretext', 'font_size', 'sort_order', 'same_row', 'is_required'];

    protected function casts(): array
    {
        return ['sort_order' => 'integer', 'font_size' => 'integer', 'same_row' => 'boolean', 'is_required' => 'boolean'];
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order');
    }
}
