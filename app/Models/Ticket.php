<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\DB;

class Ticket extends Model
{
    protected $fillable = [
        'ticket_number', 'customer_id', 'device_type', 'brand', 'model', 'serial_number',
        'problem_description', 'status_id', 'created_by', 'assigned_to', 'labor_cost',
    ];

    protected function casts(): array
    {
        return ['labor_cost' => 'decimal:2'];
    }

    /** Next ticket number from the PostgreSQL sequence, e.g. RT-2026-0007 */
    public static function nextNumber(): string
    {
        $n = DB::selectOne("SELECT nextval('ticket_number_seq') AS n")->n;

        return sprintf('RT-%s-%04d', now()->format('Y'), $n);
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function status(): BelongsTo
    {
        return $this->belongsTo(RepairStatus::class, 'status_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function technician(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function parts(): HasMany
    {
        return $this->hasMany(TicketPart::class);
    }

    public function histories(): HasMany
    {
        return $this->hasMany(TicketStatusHistory::class)->orderBy('created_at')->orderBy('id');
    }

    /** Admin can work on any ticket. A technician only on unassigned tickets or their own (same rule as the database policy). */
    public function canBeWorkedOnBy(User $user): bool
    {
        if ($user->isAdmin()) {
            return true;
        }

        return $user->role === User::ROLE_TECHNICIAN
            && ($this->assigned_to === null || (int) $this->assigned_to === (int) $user->id);
    }

    public function isReleased(): bool
    {
        return $this->status?->name === RepairStatus::RELEASED;
    }

    public function partsCost(): float
    {
        return (float) $this->parts->sum(fn (TicketPart $p) => $p->quantity * $p->unit_price);
    }

    public function totalCost(): float
    {
        return (float) $this->labor_cost + $this->partsCost();
    }

    public function scopeSearch(Builder $query, ?string $term): Builder
    {
        if (! $term) {
            return $query;
        }

        return $query->where(function (Builder $q) use ($term) {
            $q->where('ticket_number', 'ilike', "%{$term}%")
                ->orWhere('brand', 'ilike', "%{$term}%")
                ->orWhere('model', 'ilike', "%{$term}%")
                ->orWhereHas('customer', fn (Builder $c) => $c->where('name', 'ilike', "%{$term}%")
                    ->orWhere('phone', 'ilike', "%{$term}%"));
        });
    }
}
