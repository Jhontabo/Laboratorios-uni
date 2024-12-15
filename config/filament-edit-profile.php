<?php

return [
    'avatar_url' => 'avatar_url',
    'disk' => env('FILESYSTEM_DISK', 'public'),
    'visibility' => 'public', // or replace by filesystem disk visibility with fallback value
];
