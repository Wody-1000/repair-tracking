<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LowStockAlert extends Model
{
    protected $fillable = ['part_id', 'quantity_at_alert', 'resolved'];

    protected function casts(): array
    {
        return ['resolved' => 'boolean'];
    }

    public function part(): BelongsTo
    {
        return $this->belongsTo(Part::class);
    }
}
