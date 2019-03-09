<?php

return [
    'token' => [
        'parameter' => 'token',
        'separator' => ':',

        // Disable middleware on following routes
        'routes' => [
            'password/reset*'
        ]
    ]
];