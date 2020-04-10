<?php

use JwPersistentUser\Model\ModuleOptions;
use JwPersistentUser\Model\ModuleOptionsFactory;
use JwPersistentUser\Service\CookieService;
use JwPersistentUser\Service\CookieServiceFactory;
use JwPersistentUser\Service\RememberMeService;
use JwPersistentUser\Service\RememberMeServiceFactory;
use JwPersistentUser\Service\UserAlwaysValid;
use Zend\ServiceManager\Factory\InvokableFactory;

return [
    'service_manager' => [
        'factories' => [
            ModuleOptions::class => ModuleOptionsFactory::class,
            RememberMeService::class => RememberMeServiceFactory::class,
            CookieService::class => CookieServiceFactory::class,
            UserAlwaysValid::class => InvokableFactory::class,
        ],
        'aliases' => [
            'JwPersistentUser\UserValidity' => UserAlwaysValid::class,
            'JwPersistentUser\Service\RememberMe' => RememberMeService::class,
            'JwPersistentUser\ModuleOptions' => ModuleOptions::class,
            'JwPersistentUser\Service\Cookie' => CookieService::class,
        ],
    ],
];
