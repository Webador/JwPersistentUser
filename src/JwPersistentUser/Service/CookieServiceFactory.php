<?php

namespace JwPersistentUser\Service;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Interop\Container\ContainerInterface;

class CookieServiceFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $service = new CookieService;

        $service->setModuleOptions($container->get('JwPersistentUser\ModuleOptions'));

        return $service;
    }

    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        return $this($serviceLocator->getServiceLocator(), 'JwPersistentUser\Service\Cookie');
    }
}
