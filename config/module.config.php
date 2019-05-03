<?php

return [
    'service_manager' => [
        'factories' => [
            'JwPersistentUser\ModuleOptions' => 'JwPersistentUser\Model\ModuleOptionsFactory',
            'JwPersistentUser\Service\RememberMe' => 'JwPersistentUser\Service\RememberMeServiceFactory',
            'JwPersistentUser\Service\Cookie' => 'JwPersistentUser\Service\CookieServiceFactory',
            'JwPersistentUser\Service\UserAlwaysValid' => \Zend\ServiceManager\Factory\InvokableFactory::class,
        ],
        'alias' => [
            'JwPersistentUser\UserValidity' => 'JwPersistentUser\Service\UserAlwaysValid',
        ],
    ],
];
