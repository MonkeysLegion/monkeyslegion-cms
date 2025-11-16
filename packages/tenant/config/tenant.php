<?php

return [
    'tenant' => [
        'key_prefix' => $_ENV['TENANT_KEY_PREFIX'] ?? 'tenant_',
        'ttl' => $_ENV['TENANT_TTL'] ?? 30, // in seconds
    ],
];
