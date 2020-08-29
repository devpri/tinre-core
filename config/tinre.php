<?php

return [
    'redirect_type' => 302,

    'dashboard_path' => '/dashboard',

    'date_format' => 'MM/DD/YYYY, h:mm:ss a',

    'redirect_user_to_dashboard' => true,

    'roles' => [
        'user',
        'editor',
        'administrator',
    ],

    'permissions' => [
        'url:view',
        'url:create',
        'url:update',
        'url:delete',
        'url:view:any',
        'url:update:any',
        'url:delete:any',
        'stats:view',
        'user:change_email',
        'user:view',
        'user:create',
        'user:update',
        'user:view:any',
        'user:update:any',
        'user:delete:any',
        'access_token:view',
        'access_token:create',
        'access_token:update',
        'access_token:delete',
        'access_token:view:any',
        'access_token:update:any',
        'access_token:delete:any',     
    ],

    'role_permissions' => [
        'user' => [
            'url:view',
            'url:create',
            'url:update',
            'url:delete',
            'stats:view',
            'user:view',
            'user:change_email',
            'user:update',
            'access_token:view',
            'access_token:create',
            'access_token:update',
            'access_token:delete',
        ],
        'editor' => [
            'url:view',
            'url:create',
            'url:update',
            'url:delete',
            'url:view:any',
            'url:update:any',
            'url:delete:any',
            'stats:view',
            'user:view',
            'user:change_email',
            'user:update',
            'access_token:view',
            'access_token:create',
            'access_token:update',
            'access_token:delete',
        ],
        'administrator' => ['*'],
    ],

    'api_permissions' => [
        'url:view',
        'url:create',
        'url:update',
        'url:delete',
        'url:view:any',
        'url:update:any',
        'url:delete:any',
        'stats:view',
    ],

    'default_role' => 'user',

    'default_path_length' => 6,

    'min_path_length' => 6,

    'max_path_length' => 255,

    'restricted_paths' => [
        'api',
        'web',
        'dashboard',
    ],

    'restricted_domains' => [],

    'guest_form' => true,

    'guest_form_custom_path' => true,

    'url_preview' => true,

    'url_preview_suffix' => '+',
];
