<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\BudgetController;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\GalleryController;
use App\Http\Controllers\GuestController;
use App\Http\Controllers\MailTestController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\VendorController;
use App\Http\Controllers\VendorDashboardController;
use Illuminate\Support\Facades\Route;

Route::get('/', [DashboardController::class, 'landing'])->name('landing');

// Testing Mail Routes
Route::get('/mail-debug', [MailTestController::class, 'index'])->name('mail.debug.index');
Route::post('/mail-debug/send-sync', [MailTestController::class, 'sendSync'])->name('mail.debug.sync');
Route::post('/mail-debug/send-queue', [MailTestController::class, 'sendQueue'])->name('mail.debug.queue');
Route::get('/mail-debug/test-smtp', [MailTestController::class, 'testSmtpConnection'])->name('mail.debug.smtp');
Route::post('/mail-debug/command', [MailTestController::class, 'runCommand'])->name('mail.debug.command');

// Guest RSVP Signed Route
Route::get('/guests/rsvp/{event}/{guest}/{status}', function ($eventId, $guestId, $status) {
    if (! request()->hasValidSignature()) {
        abort(401, 'This link has expired or is invalid.');
    }

    $guest = \App\Models\Guest::findOrFail($guestId);
    $event = \App\Models\Event::findOrFail($eventId);

    // Map accepted/declined to yes/no for portal RSVP status
    $rsvpStatus = 'pending';
    if ($status === 'accepted' || $status === 'yes') {
        $rsvpStatus = 'yes';
    } elseif ($status === 'declined' || $status === 'no') {
        $rsvpStatus = 'no';
    }

    $guest->status = $status;
    $guest->rsvp_status = $rsvpStatus;
    $guest->save();

    app(\App\Services\MailService::class)->sendRsvpConfirmation($guest, $event, $status);

    return "Thank you for your response! You have {$status} the invitation for {$event->title}.";
})->name('guests.rsvp');

/* ================================================================
 *  AUTH — GUEST (unauthenticated) ROUTES
 * ================================================================ */
Route::middleware(['guest', 'no-cache'])->group(function () {
    Route::get('/register', [AuthController::class, 'register'])->name('register');
    Route::post('/register', [AuthController::class, 'store'])->name('register.store');
    Route::get('/login', [AuthController::class, 'login'])->name('login');
    Route::post('/login', [AuthController::class, 'authenticate'])->name('login.authenticate');
    Route::get('/reset-password', [AuthController::class, 'showResetForm'])->name('password.reset');
    Route::post('/reset-password', [AuthController::class, 'sendResetOtp'])->name('password.send-otp');
    Route::get('/reset-password/verify', [AuthController::class, 'showResetOtpForm'])->name('password.verify-otp');
    Route::post('/reset-password/verify', [AuthController::class, 'verifyResetOtp'])->name('password.verify-otp.submit');
    Route::post('/reset-password/resend', [AuthController::class, 'resendResetOtp'])->name('password.resend-otp');
    Route::get('/reset-password/new', [AuthController::class, 'showNewPasswordForm'])->name('password.new-password');
    Route::post('/reset-password/new', [AuthController::class, 'resetPassword'])->name('password.update');
});

/* ================================================================
 *  OTP VERIFICATION — requires auth but NOT verified
 * ================================================================ */
Route::middleware('auth')->group(function () {
    Route::get('/verify-otp', [AuthController::class, 'showOtpForm'])->name('verification.otp');
    Route::post('/verify-otp', [AuthController::class, 'verifyOtp'])->name('verification.verify-otp');
    Route::post('/resend-otp', [AuthController::class, 'resendOtp'])->name('verification.resend-otp');
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
});

// Public RSVP routes
Route::get('/rsvp/{token}', [GuestController::class, 'publicRsvp'])->name('rsvp.show');
Route::post('/rsvp/{token}', [GuestController::class, 'submitRsvp'])->name('rsvp.submit');
Route::get('/timeline/shared/{token}', [BookingController::class, 'sharedTimeline'])->name('timeline.shared');

/* ================================================================
 *  PROTECTED ROUTES — requires auth
 * ================================================================ */
Route::middleware(['auth', 'jwt.session'])->group(function () {

    // Dashboard router (redirects by role)
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Profile (all roles)
    Route::get('/profile/edit', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::post('/profile/update', [ProfileController::class, 'update'])->name('profile.update');

    // Vendor directory (all roles)
    Route::get('/vendors', [VendorController::class, 'index'])->name('vendors.index');
    Route::get('/vendors/search', [VendorController::class, 'apiFilter'])->name('vendors.search');
    Route::get('/vendors/{id}', [VendorController::class, 'show'])->name('vendors.show');
    Route::post('/vendors/{id}/favorites', [VendorController::class, 'addToFavorites'])->name('vendors.favorite');
    Route::delete('/vendors/{id}/favorites', [VendorController::class, 'removeFromFavorites'])->name('vendors.unfavorite');
    Route::get('/favorites', [VendorController::class, 'favorites'])->name('vendors.favorites');
    Route::get('/api/vendors/filter', [VendorController::class, 'apiFilter'])->name('api.vendors.filter');

    // Messaging (all authenticated roles — planner & vendor)
    Route::get('/messages', [ChatController::class, 'index'])->name('chat.index');
    Route::get('/messages/conversations', [ChatController::class, 'conversationList'])->name('chat.conversations');
    Route::get('/messages/{bookingId}', [ChatController::class, 'messages'])->name('chat.messages');
    Route::post('/messages/{bookingId}', [ChatController::class, 'send'])->name('chat.send');

    /* ── Planner Dashboard ── */
    Route::middleware('role:planner')->group(function () {
        Route::get('/planner-dashboard', [DashboardController::class, 'planner'])->name('planner.dashboard');

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
        Route::match(['get', 'post'], '/events/{id}/guests/{guestId}/send-invite', [GuestController::class, 'sendInvite'])->name('guests.invite');
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

        // Planner-side chat on bookings
        Route::get('/events/{id}/bookings/{bookingId}/messages', [BookingController::class, 'chatMessages'])->name('bookings.messages');
        Route::post('/events/{id}/bookings/{bookingId}/messages', [BookingController::class, 'sendMessage'])->name('bookings.messages.send');

        Route::get('/events/{id}/tasks', [TaskController::class, 'index'])->name('tasks.index');
        Route::post('/events/{id}/tasks', [TaskController::class, 'store'])->name('tasks.store');
        Route::put('/events/{id}/tasks/{taskId}', [TaskController::class, 'update'])->name('tasks.update');
        Route::delete('/events/{id}/tasks/{taskId}', [TaskController::class, 'destroy'])->name('tasks.destroy');

        Route::get('/events/{id}/gallery', [GalleryController::class, 'index'])->name('gallery.index');
        Route::post('/events/{id}/gallery', [GalleryController::class, 'store'])->name('gallery.store');
        Route::delete('/events/{id}/gallery/{imageId}', [GalleryController::class, 'destroy'])->name('gallery.destroy');
    });

    /* ── Vendor Dashboard ── */
    Route::middleware('role:vendor')->group(function () {
        Route::get('/vendor-dashboard', [VendorDashboardController::class, 'index'])->name('vendor.dashboard');
        Route::post('/vendor-dashboard', [VendorDashboardController::class, 'updateProfile'])->name('vendor.dashboard.update');

        // Vendor booking request portal
        Route::post('/vendor-bookings/{bookingId}/respond', [VendorDashboardController::class, 'respondBooking'])->name('vendor.booking.respond');
        Route::get('/vendor-bookings/{bookingId}/messages', [VendorDashboardController::class, 'chatMessages'])->name('vendor.booking.messages');
        Route::post('/vendor-bookings/{bookingId}/messages', [VendorDashboardController::class, 'sendMessage'])->name('vendor.booking.messages.send');
    });

    /* ── Guest Dashboard ── */
    Route::middleware('role:guest')->group(function () {
        Route::get('/guest-dashboard', [DashboardController::class, 'guest'])->name('guest.dashboard');
    });
});
