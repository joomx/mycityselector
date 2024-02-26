<?php

return [
    'database' => [
        'any' => [
            [
                'city' => [10, 45, 34],
                'original' => 'mail@mail.com',
                'new_value' => 'ignat@website.com'
            ]
        ],
//        '#__users.email' => [
//            'city' => [10, 45, 34],
//            'original' => 'mail@mail.com',
//            'new_value' => 'ignat@sebsite.com'
//        ]
    ],
    'configuration' => [
        'mailfrom' => [
            [
                'city' => [10, 45, 34],
                'new_value' => 'ignat@sebsite.com'
            ],
            [
                'province' => [ 45, 23, 5 ],
                'new_value' => 'vlad@sebsite.com'
            ],
            [
                'country' => [ 1 ],
                'new_value' => 'dima@sebsite.com'
            ],
        ]
    ],
];