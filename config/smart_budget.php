<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Available Services
    |--------------------------------------------------------------------------
    | Master list of selectable services with display metadata.
    */
    'services' => [
        'venue'             => ['label' => 'Venue',             'icon' => '🏛️'],
        'catering'          => ['label' => 'Catering',          'icon' => '🍽️'],
        'decoration'        => ['label' => 'Decoration',        'icon' => '🎨'],
        'photography'       => ['label' => 'Photography',       'icon' => '📸'],
        'dj_music'          => ['label' => 'DJ / Music',        'icon' => '🎵'],
        'makeup_artist'     => ['label' => 'Makeup Artist',     'icon' => '💄'],
        'invitation_design' => ['label' => 'Invitation Design', 'icon' => '💌'],
        'transportation'    => ['label' => 'Transportation',    'icon' => '🚗'],
        'lighting'          => ['label' => 'Lighting',          'icon' => '💡'],
        'accommodation'     => ['label' => 'Accommodation',     'icon' => '🏨'],
        'security'          => ['label' => 'Security',          'icon' => '🛡️'],
        'return_gifts'      => ['label' => 'Return Gifts',      'icon' => '🎁'],
    ],

    /*
    |--------------------------------------------------------------------------
    | Base Allocation Templates (percentages per event type)
    |--------------------------------------------------------------------------
    | Each key maps to the event category stored in Event->category.
    | Percentages are for the "Balanced" luxury level and will be scaled.
    | Only services listed here get a non-zero base; others default to 0.
    */
    'templates' => [

        'Wedding' => [
            'venue'             => 30,
            'catering'          => 25,
            'decoration'        => 15,
            'photography'       => 10,
            'dj_music'          =>  5,
            'makeup_artist'     =>  4,
            'invitation_design' =>  2,
            'lighting'          =>  3,
            'return_gifts'      =>  3,
            'transportation'    =>  2,
            'accommodation'     =>  0,
            'security'          =>  1,
        ],

        'Birthday' => [
            'venue'             => 25,
            'catering'          => 30,
            'decoration'        => 20,
            'photography'       =>  8,
            'dj_music'          => 10,
            'return_gifts'      =>  5,
            'lighting'          =>  2,
            'makeup_artist'     =>  0,
            'invitation_design' =>  0,
            'transportation'    =>  0,
            'accommodation'     =>  0,
            'security'          =>  0,
        ],

        'Corporate' => [
            'venue'             => 35,
            'catering'          => 25,
            'decoration'        =>  5,
            'photography'       =>  8,
            'dj_music'          =>  2,
            'lighting'          =>  5,
            'transportation'    => 10,
            'accommodation'     =>  5,
            'security'          =>  3,
            'invitation_design' =>  2,
            'makeup_artist'     =>  0,
            'return_gifts'      =>  0,
        ],

        'Reception' => [
            'venue'             => 30,
            'catering'          => 28,
            'decoration'        => 18,
            'photography'       => 10,
            'dj_music'          =>  5,
            'lighting'          =>  4,
            'makeup_artist'     =>  3,
            'invitation_design' =>  2,
            'return_gifts'      =>  0,
            'transportation'    =>  0,
            'accommodation'     =>  0,
            'security'          =>  0,
        ],

        'Engagement' => [
            'venue'             => 28,
            'catering'          => 25,
            'decoration'        => 18,
            'photography'       => 12,
            'dj_music'          =>  5,
            'makeup_artist'     =>  5,
            'lighting'          =>  3,
            'invitation_design' =>  2,
            'return_gifts'      =>  2,
            'transportation'    =>  0,
            'accommodation'     =>  0,
            'security'          =>  0,
        ],

        'Concert' => [
            'venue'             => 35,
            'dj_music'          => 25,
            'lighting'          => 15,
            'security'          => 10,
            'transportation'    =>  5,
            'decoration'        =>  5,
            'photography'       =>  3,
            'catering'          =>  2,
            'makeup_artist'     =>  0,
            'invitation_design' =>  0,
            'accommodation'     =>  0,
            'return_gifts'      =>  0,
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Luxury Level Multipliers
    |--------------------------------------------------------------------------
    | Multipliers applied to "premium" categories.
    | Categories NOT in 'premium_categories' keep a 1.0x multiplier.
    | After multiplication, totals are re-normalized to 100%.
    */
    'luxury_levels' => [
        'budget'   => ['multiplier' => 0.7,  'label' => 'Budget',   'vendor_tier' => 'budget_friendly'],
        'balanced' => ['multiplier' => 1.0,  'label' => 'Balanced', 'vendor_tier' => 'all'],
        'premium'  => ['multiplier' => 1.3,  'label' => 'Premium',  'vendor_tier' => 'premium'],
        'luxury'   => ['multiplier' => 1.6,  'label' => 'Luxury',   'vendor_tier' => 'premium_first'],
    ],

    // Categories that receive luxury multiplier boosts
    'premium_categories' => [
        'photography', 'decoration', 'lighting', 'makeup_artist', 'venue',
    ],

    /*
    |--------------------------------------------------------------------------
    | Priority Weights
    |--------------------------------------------------------------------------
    | How priority labels shift the allocation weight before re-normalization.
    */
    'priority_weights' => [
        'high'   => 1.35,  // +35%
        'medium' => 1.00,
        'low'    => 0.75,  // -25%
    ],

    /*
    |--------------------------------------------------------------------------
    | Guest Count Scaling
    |--------------------------------------------------------------------------
    | When guest count exceeds thresholds, boost certain categories.
    */
    'guest_scaling' => [
        ['threshold' => 300, 'boost' => ['catering' => 1.15, 'venue' => 1.10, 'security' => 1.20, 'transportation' => 1.15]],
        ['threshold' => 500, 'boost' => ['catering' => 1.25, 'venue' => 1.15, 'security' => 1.35, 'accommodation' => 1.20]],
        ['threshold' => 1000, 'boost' => ['catering' => 1.35, 'venue' => 1.20, 'security' => 1.50, 'accommodation' => 1.30]],
    ],

    /*
    |--------------------------------------------------------------------------
    | Per-Guest Cost Benchmarks (INR)
    |--------------------------------------------------------------------------
    | Used for intelligent warnings like "catering budget may be low for 500 guests".
    */
    'per_guest_benchmarks' => [
        'catering' => [
            'budget'   => 400,
            'balanced' => 700,
            'premium'  => 1200,
            'luxury'   => 2000,
        ],
        'return_gifts' => [
            'budget'   => 50,
            'balanced' => 150,
            'premium'  => 350,
            'luxury'   => 600,
        ],
        'accommodation' => [
            'budget'   => 800,
            'balanced' => 1500,
            'premium'  => 3000,
            'luxury'   => 5000,
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Vendor Scoring Weights
    |--------------------------------------------------------------------------
    */
    'vendor_scoring' => [
        'price_fit'    => 35,
        'rating'       => 20,
        'availability' => 25,
        'location'     => 10,
        'reviews'      => 10,
    ],

    /*
    |--------------------------------------------------------------------------
    | Recommendation Cache TTL (minutes)
    |--------------------------------------------------------------------------
    */
    'recommendation_cache_ttl' => 30,

    /*
    |--------------------------------------------------------------------------
    | Service → Vendor Category Mapping
    |--------------------------------------------------------------------------
    | Maps smart-budget service keys to vendor.category values in the DB.
    */
    'service_vendor_category_map' => [
        'venue'             => ['venue', 'banquet_hall', 'resort'],
        'catering'          => ['catering', 'caterer', 'food'],
        'decoration'        => ['decoration', 'decorator', 'decor', 'florist'],
        'photography'       => ['photography', 'photographer', 'videography'],
        'dj_music'          => ['dj', 'music', 'entertainment', 'band'],
        'makeup_artist'     => ['makeup', 'makeup_artist', 'beauty', 'mehndi'],
        'invitation_design' => ['invitation', 'printing', 'stationery'],
        'transportation'    => ['transportation', 'travel', 'car_rental'],
        'lighting'          => ['lighting', 'lights', 'sound'],
        'accommodation'     => ['accommodation', 'hotel', 'lodging'],
        'security'          => ['security', 'bouncer'],
        'return_gifts'      => ['gifts', 'return_gifts', 'favors'],
    ],
];
