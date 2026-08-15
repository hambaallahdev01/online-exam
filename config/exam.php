<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Student Exam Performance
    |--------------------------------------------------------------------------
    |
    | Question payloads are safe to cache because they never contain answer
    | keys. Drafts use the database by default so shared hosting does not need
    | Redis. Set EXAM_DRAFT_STORE=redis on hosts that provide Redis.
    |
    */

    'payload_cache' => [
        'enabled' => env('EXAM_PAYLOAD_CACHE_ENABLED', true),
        'store' => env('EXAM_PAYLOAD_CACHE_STORE', env('CACHE_STORE', 'file')),
        'ttl_seconds' => (int) env('EXAM_PAYLOAD_CACHE_TTL', 3600),
    ],

    'drafts' => [
        'store' => env('EXAM_DRAFT_STORE', 'database'),
        'ttl_seconds' => (int) env('EXAM_DRAFT_TTL', 86400),
        'database_checkpoint_seconds' => (int) env('EXAM_DRAFT_CHECKPOINT_SECONDS', 30),
    ],

];
