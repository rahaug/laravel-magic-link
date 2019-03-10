<?php

return [
    'token-parameter' => 'token',
    'token-separator' => ':',

    // Disable middleware on following routes
    'token-exclude-routes' => [
    'password/reset*'
]
];