<?php

namespace App\Support;

use App\Models\User;
use Illuminate\Database\ConnectionInterface;

/**
 * Tells PostgreSQL who is calling, so the row-level security policies (database/sql/rls.sql)
 * match the Laravel login: app.role = admin | technician | front_desk | public (guest).
 */
class DbContext
{
    public static function apply(ConnectionInterface $connection, ?User $user): void
    {
        $connection->select(
            "SELECT set_config('app.role', ?, false), set_config('app.user_id', ?, false)",
            [$user?->role ?? 'public', $user ? (string) $user->id : '']
        );
    }

    public static function clear(ConnectionInterface $connection): void
    {
        $connection->select("SELECT set_config('app.role', '', false), set_config('app.user_id', '', false)");
    }
}
