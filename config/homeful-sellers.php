<?php

return [
    'end-points' => [
        'customer' => env('CUSTOMER_END_POINT','https://contacts-staging.homeful.ph/api/references/:contact_reference_code'),
        'projects' => env('PROJECTS_END_POINT','https://properties-staging.homeful.ph/fetch-projects'),
        'contact' => env('CONTACT_END_POINT','https://contacts-staging.homeful.ph/'),
    ],
    'keys'=>[
        'contact_key' =>env('CONTACT_API_KEY')
    ]
];
