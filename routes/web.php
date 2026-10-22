<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DatabaseAdminController;
use App\Http\Controllers\PartController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\StatusController;
use App\Http\Controllers\TicketController;
use App\Http\Controllers\TicketPartController;
use App\Http\Controllers\TicketStatusController;
use App\Http\Controllers\UserController;
use App\Http\Middleware\EnsureRole;
use App\Http\Middleware\SetDatabaseContext;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Roles
|--------------------------------------------------------------------------
| admin       everything (inventory, reports, users)
| technician  works on tickets: status, labor, parts used
| front_desk  receives devices: creates and edits tickets
| public      customers check status by ticket number (no login)
*/

// Every route runs inside SetDatabaseContext: it tells PostgreSQL the caller's role and id for row-level security.
Route::middleware(SetDatabaseContext::class)->group(function () {

    // Public
    Route::get('/', [StatusController::class, 'index'])->middleware('throttle:30,1')->name('status');

    // Guests only
    Route::middleware('guest')->group(function () {
        Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
        Route::post('/login', [AuthController::class, 'login'])->middleware('throttle:10,1');
        Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
        Route::post('/register', [AuthController::class, 'register'])->middleware('throttle:10,1');
    });

    // Any logged-in user
    Route::middleware('auth')->group(function () {
        Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

        // Intake: admin + front desk. Defined BEFORE tickets/{ticket} so "create" is not read as an id.
        Route::middleware(EnsureRole::class.':admin,front_desk')->group(function () {
            Route::get('/tickets/create', [TicketController::class, 'create'])->name('tickets.create');
            Route::post('/tickets', [TicketController::class, 'store'])->name('tickets.store');
            Route::get('/tickets/{ticket}/edit', [TicketController::class, 'edit'])->whereNumber('ticket')->name('tickets.edit');
            Route::put('/tickets/{ticket}', [TicketController::class, 'update'])->whereNumber('ticket')->name('tickets.update');
        });

        // Repair work: admin + technician
        Route::middleware(EnsureRole::class.':admin,technician')->group(function () {
            Route::put('/tickets/{ticket}/progress', [TicketStatusController::class, 'update'])->whereNumber('ticket')->name('tickets.progress');
            Route::post('/tickets/{ticket}/parts', [TicketPartController::class, 'store'])->whereNumber('ticket')->name('tickets.parts.store');
            Route::delete('/tickets/{ticket}/parts/{ticketPart}', [TicketPartController::class, 'destroy'])->whereNumber('ticket')->whereNumber('ticketPart')->name('tickets.parts.destroy');
        });

        // Everyone logged in can look
        Route::get('/tickets', [TicketController::class, 'index'])->name('tickets.index');
        Route::get('/tickets/{ticket}', [TicketController::class, 'show'])->whereNumber('ticket')->name('tickets.show');
        Route::get('/parts', [PartController::class, 'index'])->name('parts.index');

        // Admin only
        Route::middleware(EnsureRole::class.':admin')->group(function () {
            Route::delete('/tickets/{ticket}', [TicketController::class, 'destroy'])->whereNumber('ticket')->name('tickets.destroy');

            Route::get('/parts/create', [PartController::class, 'create'])->name('parts.create');
            Route::post('/parts', [PartController::class, 'store'])->name('parts.store');
            Route::get('/parts/{part}/edit', [PartController::class, 'edit'])->whereNumber('part')->name('parts.edit');
            Route::put('/parts/{part}', [PartController::class, 'update'])->whereNumber('part')->name('parts.update');
            Route::delete('/parts/{part}', [PartController::class, 'destroy'])->whereNumber('part')->name('parts.destroy');

            Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
            Route::get('/admin/database', [DatabaseAdminController::class, 'index'])->name('admin.database');

            Route::get('/users', [UserController::class, 'index'])->name('users.index');
            Route::get('/users/{user}/edit', [UserController::class, 'edit'])->whereNumber('user')->name('users.edit');
            Route::put('/users/{user}', [UserController::class, 'update'])->whereNumber('user')->name('users.update');
            Route::delete('/users/{user}', [UserController::class, 'destroy'])->whereNumber('user')->name('users.destroy');
        });
    });

});
