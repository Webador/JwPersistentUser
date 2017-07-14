<?php

namespace JwPersistentUser\Service;

use Zend\Http\PhpEnvironment\RemoteAddress;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Interop\Container\ContainerInterface;

class RememberMeServiceFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $service = new RememberMeService;

        $service->setIpService(new RemoteAddress);
        $service->setRequest($container->get('Request'));
        $service->setModuleOptions($container->get('JwPersistentUser\ModuleOptions'));
        $service->setMapper($container->get('JwPersistentUser\Mapper\SerieToken'));

        return $service;
    }

    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        return $this($serviceLocator->getServiceLocator(), 'JwPersistentUser\Service\RememberMe');
    }
}
