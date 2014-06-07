<?php

namespace JwPersistentUser\Service;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class RememberMeServiceFactory implements FactoryInterface
{
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $service = new RememberMeService;

        $service->setModuleOptions($serviceLocator->get('JwPersistentUser\ModuleOptions'));
        $service->setMapper($serviceLocator->get('JwPersistentUser\Mapper\SerieToken'));

        return $service;
    }
}
