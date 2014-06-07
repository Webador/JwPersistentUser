<?php

namespace JwPersistentUser\Service;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class CookieServiceFactory implements FactoryInterface
{
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $service = new CookieService;

        $service->setModuleOptions($serviceLocator->get('JwPersistentUser\ModuleOptions'));

        return $service;
    }
}
