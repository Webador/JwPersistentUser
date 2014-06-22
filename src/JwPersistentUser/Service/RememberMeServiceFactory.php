<?php

namespace JwPersistentUser\Service;

use Zend\Http\PhpEnvironment\RemoteAddress;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class RememberMeServiceFactory implements FactoryInterface
{
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $service = new RememberMeService;

        $service->setIpService(new RemoteAddress);
        $service->setModuleOptions($serviceLocator->get('JwPersistentUser\ModuleOptions'));
        $service->setMapper($serviceLocator->get('JwPersistentUser\Mapper\SerieToken'));

        return $service;
    }
}
