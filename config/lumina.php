<?php

return [
    'project' => [
        'status' => [
            'invoiced'      => 'Invoiced',
            'paid'          => 'Paid',
            'overdue'       => 'Overdue',
            'start'         => 'Start',
            'finished'      => 'Finished',
        ]
    ],
    'accounts' => [
        'defaults' => [
            'password' => '$Lumina123'
        ]
    ],
    'variables' => [
        'default' => [
            'safe'      => 2,
            'span'      => 1,
            'fee_theta' => 50
        ]
    ]
];
