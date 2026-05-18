<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\BudgetController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\GalleryController;
use App\Http\Controllers\GuestController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\VendorController;
use Illuminate\Support\Facades\Route;

Route::get('/', [DashboardController::class, 'landing'])->name('landing');

Route::middleware('guest')->group(function () {
    Route::get('/register', [AuthController::class, 'register'])->name('register');
    Route::post('/register', [AuthController::class, 'store'])->name('register.store');
    Route::get('/login', [AuthController::class, 'login'])->name('login');
    Route::post('/login', [AuthController::class, 'authenticate'])->name('login.authenticate');
    Route::get('/reset-password', [AuthController::class, 'showResetForm'])->name('password.reset');
    Route::post('/reset-password', [AuthController::class, 'resetPassword'])->name('password.update');
});

Route::get('/verify-email', [AuthController::class, 'verifyNotice'])->middleware('auth')->name('verification.notice');
Route::get('/verify-email/{token}', [AuthController::class, 'verifyEmail'])->name('verification.verify');
Route::get('/rsvp/{token}', [GuestController::class, 'publicRsvp'])->name('rsvp.show');
Route::post('/rsvp/{token}', [GuestController::class, 'submitRsvp'])->name('rsvp.submit');
Route::get('/timeline/shared/{token}', [BookingController::class, 'sharedTimeline'])->name('timeline.shared');

Route::middleware(['auth', 'jwt.session'])->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/profile/edit', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::post('/profile/update', [ProfileController::class, 'update'])->name('profile.update');

    Route::get('/vendors', [VendorController::class, 'index'])->name('vendors.index');
    Route::get('/vendors/search', [VendorController::class, 'apiFilter'])->name('vendors.search');
    Route::get('/vendors/{id}', [VendorController::class, 'show'])->name('vendors.show');
    Route::post('/vendors/{id}/favorites', [VendorController::class, 'addToFavorites'])->name('vendors.favorite');
    Route::delete('/vendors/{id}/favorites', [VendorController::class, 'removeFromFavorites'])->name('vendors.unfavorite');
    Route::get('/favorites', [VendorController::class, 'favorites'])->name('vendors.favorites');
    Route::get('/api/vendors/filter', [VendorController::class, 'apiFilter'])->name('api.vendors.filter');

    Route::middleware('role:planner')->group(function () {
        Route::get('/events', [EventController::class, 'index'])->name('events.index');
        Route::get('/events/create', [EventController::class, 'create'])->name('events.create');
        Route::post('/events', [EventController::class, 'store'])->name('events.store');
        Route::get('/events/{id}', [EventController::class, 'show'])->name('events.show');
        Route::get('/events/{id}/edit', [EventController::class, 'edit'])->name('events.edit');
        Route::put('/events/{id}', [EventController::class, 'update'])->name('events.update');
        Route::delete('/events/{id}', [EventController::class, 'destroy'])->name('events.destroy');
        Route::get('/api/events/{id}/stats', [EventController::class, 'statsJson'])->name('api.events.stats');

        Route::get('/budget/{eventId}', [BudgetController::class, 'index'])->name('budget.index');
        Route::post('/budget/{eventId}/expense', [BudgetController::class, 'addExpense'])->name('budget.expense.store');
        Route::delete('/budget/expense/{id}', [BudgetController::class, 'deleteExpense'])->name('budget.expense.destroy');
        Route::get('/api/budget/{eventId}/chart', [BudgetController::class, 'chart'])->name('api.budget.chart');
        Route::get('/api/budget/{eventId}/alerts', [BudgetController::class, 'alerts'])->name('api.budget.alerts');

        Route::get('/events/{id}/guests', [GuestController::class, 'index'])->name('guests.index');
        Route::get('/events/{id}/guests/create', [GuestController::class, 'create'])->name('guests.create');
        Route::post('/events/{id}/guests', [GuestController::class, 'store'])->name('guests.store');
        Route::match(['get', 'post'], '/events/{id}/guests/bulk-import', [GuestController::class, 'bulkImport'])->name('guests.bulk');
        Route::get('/events/{id}/guests/{guestId}/edit', [GuestController::class, 'edit'])->name('guests.edit');
        Route::put('/events/{id}/guests/{guestId}', [GuestController::class, 'update'])->name('guests.update');
        Route::delete('/events/{id}/guests/{guestId}', [GuestController::class, 'destroy'])->name('guests.destroy');
        Route::post('/events/{id}/guests/{guestId}/send-invite', [GuestController::class, 'sendInvite'])->name('guests.invite');
        Route::get('/events/{id}/guests/export', [GuestController::class, 'export'])->name('guests.export');
        Route::get('/api/events/{id}/rsvp-stats', [GuestController::class, 'statsJson'])->name('api.guests.stats');

        Route::get('/events/{id}/bookings', [BookingController::class, 'index'])->name('bookings.index');
        Route::get('/events/{id}/bookings/create', [BookingController::class, 'create'])->name('bookings.create');
        Route::post('/events/{id}/bookings', [BookingController::class, 'store'])->name('bookings.store');
        Route::put('/events/{id}/bookings/{bookingId}', [BookingController::class, 'update'])->name('bookings.update');
        Route::delete('/events/{id}/bookings/{bookingId}', [BookingController::class, 'cancel'])->name('bookings.cancel');
        Route::post('/events/{id}/bookings/{bookingId}/confirm', [BookingController::class, 'confirm'])->name('bookings.confirm');
        Route::post('/events/{id}/bookings/share', [BookingController::class, 'shareTimeline'])->name('bookings.share');
        Route::get('/events/{id}/bookings/timeline', [BookingController::class, 'timeline'])->name('bookings.timeline');
        Route::get('/api/events/{id}/bookings/timeline', [BookingController::class, 'timelineJson'])->name('api.bookings.timeline');
        Route::get('/api/events/{id}/bookings/conflicts', [BookingController::class, 'conflicts'])->name('api.bookings.conflicts');

        Route::get('/events/{id}/tasks', [TaskController::class, 'index'])->name('tasks.index');
        Route::post('/events/{id}/tasks', [TaskController::class, 'store'])->name('tasks.store');
        Route::put('/events/{id}/tasks/{taskId}', [TaskController::class, 'update'])->name('tasks.update');
        Route::delete('/events/{id}/tasks/{taskId}', [TaskController::class, 'destroy'])->name('tasks.destroy');

        Route::get('/events/{id}/gallery', [GalleryController::class, 'index'])->name('gallery.index');
        Route::post('/events/{id}/gallery', [GalleryController::class, 'store'])->name('gallery.store');
        Route::delete('/events/{id}/gallery/{imageId}', [GalleryController::class, 'destroy'])->name('gallery.destroy');
    });
});
