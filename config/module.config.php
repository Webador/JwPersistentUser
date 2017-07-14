<?php

return [
    'service_manager' => [
        'factories' => [
            'JwPersistentUser\ModuleOptions' => 'JwPersistentUser\Model\ModuleOptionsFactory',
            'JwPersistentUser\Service\RememberMe' => 'JwPersistentUser\Service\RememberMeServiceFactory',
            'JwPersistentUser\Service\Cookie' => 'JwPersistentUser\Service\CookieServiceFactory',
        ],
        'delegators' => [
            'ZfcUser\Authentication\Adapter\AdapterChain' => [
                'JwPersistentUser\Listener\WriteTokenToCookie',
            ],
        ],
    ],
];
