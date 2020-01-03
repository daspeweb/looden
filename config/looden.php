<?php

return [
    'get_trace_error_on_api_calls' => 'true',

    'auth' => [
        'api' => false,
        'web' => false,
        'middlewares' =>
            [
                'api' => [],//additional middlaware names here
                'web' => []//
            ]
    ]
];