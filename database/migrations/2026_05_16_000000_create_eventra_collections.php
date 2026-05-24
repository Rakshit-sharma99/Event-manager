<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $db = DB::connection('mongodb')->getDatabase();
        $existing = iterator_to_array($db->listCollectionNames());

        foreach ([
            'users', 'profiles', 'events', 'event_budgets', 'event_expenses',
            'vendors', 'vendor_ratings', 'favorites', 'guests', 'guest_responses',
            'bookings', 'booking_histories', 'tasks', 'galleries', 'notifications',
            'audit_logs', 'jobs', 'failed_jobs', 'job_batches',
        ] as $collection) {
            if (! in_array($collection, $existing, true)) {
                $db->createCollection($collection);
            }
        }

        $db->users->createIndex(['email' => 1], ['unique' => true]);
        $db->users->createIndex(['verification_token' => 1]);
        $db->profiles->createIndex(['user_id' => 1]);
        $db->events->createIndex(['user_id' => 1]);
        $db->events->createIndex(['event_date' => 1]);
        $db->vendors->createIndex(['category' => 1, 'location' => 1, 'rating' => -1]);
        $db->favorites->createIndex(['user_id' => 1, 'vendor_id' => 1], ['unique' => true]);
        $db->guests->createIndex(['event_id' => 1, 'email' => 1]);
        $db->guests->createIndex(['invite_token' => 1], ['unique' => true, 'sparse' => true]);
        $db->bookings->createIndex(['event_id' => 1, 'booking_date' => 1]);
        $db->tasks->createIndex(['event_id' => 1, 'status' => 1]);
        $db->galleries->createIndex(['event_id' => 1]);
    }

    public function down(): void
    {
        $db = DB::connection('mongodb')->getDatabase();

        foreach ([
            'audit_logs', 'notifications', 'galleries', 'tasks', 'booking_histories',
            'bookings', 'guest_responses', 'guests', 'favorites', 'vendor_ratings',
            'vendors', 'event_expenses', 'event_budgets', 'events', 'profiles', 'users',
            'jobs', 'failed_jobs', 'job_batches',
        ] as $collection) {
            try {
                $db->dropCollection($collection);
            } catch (Throwable) {
                // Collection may not exist in partially migrated local environments.
            }
        }
    }
};
