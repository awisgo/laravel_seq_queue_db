<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Queue Connections
    |--------------------------------------------------------------------------
    |
    | Here you may configure the connection information for each server that
    | is used by your application. A default configuration has been added
    | for each back-end shipped with Laravel. You are free to add more.
    |
    | Adds to laravel config/queue.php Drivers: "seq_database"
    |
    */

    'seq_database' => [
        'driver'      => 'database',
        'table'       => 'jobs',
        'queue'       => 'default',
        'retry_after' => 90,
        'after_commit' => false,
    ],

];
