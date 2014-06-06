<?php

namespace JwPersistentUser\Service;

use Zend\ServiceManager\FactoryInterface,
    Zend\ServiceManager\ServiceLocatorInterface;

class CookieServiceFactory implements FactoryInterface
{
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $service = new CookieService;

        $service->setModuleOptions($service->get('JwPersistentUser\ModuleOptions'));

        return $service;
    }
}
