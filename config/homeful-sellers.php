<?php

return [
    'end-points' => [
        'customer' => env('CUSTOMER_END_POINT','http://homeful-contacts.test/api/references/:contact_reference_code'),
        'projects' => env('PROJECTS_END_POINT','https://properties.homeful.ph/fetch-projects'),
    ],
];
