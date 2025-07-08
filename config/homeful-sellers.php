<?php

return [
    'end-points' => [
        'customer' => env('CUSTOMER_END_POINT','https://contacts-staging.homeful.ph/api/references/:contact_reference_code'),
        'projects' => env('PROJECTS_END_POINT','https://properties-staging.homeful.ph/fetch-projects'),
        'contact' => env('CONTACT_END_POINT','https://contacts-staging.homeful.ph/'),
        'contract' => env('CONTRACT_END_POINT','https://contracts-staging.homeful.ph/')
    ],
    'keys'=>[
        'contact_key' =>env('CONTACT_API_KEY')
    ],
    'engagespark' => [
        'org_id' => env('ENGAGESPARK_ORGANIZATION_ID', ''),
        'sender_id' => env('ENGAGESPARK_SENDER_ID', ''),
        'api_key' => env('ENGAGESPARK_API_KEY', ''),
    ],
    'api-urls' => [
        'property' => env('PROJECTS_END_POINT','https://properties.homeful.ph'),
        'match' => env('PROJECTS_END_POINT','https://match.homeful.ph')
    ],
    'api_token' => 'Bearer '.env('API_TOKEN','UcQzIoYSM7OU2Ltrk73fmguiK0k39JW09hmzVuvDBysDAaDklsVmqkBDdYWHP9jD')
    
];
