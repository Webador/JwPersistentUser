<?php

namespace JwPersistentUser\Model;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Interop\Container\ContainerInterface;

class ModuleOptionsFactory implements FactoryInterface
{
    const KEY = 'jwpersistentuser';

    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $options = new ModuleOptions;

        $config = $container->get('Config');
        if (isset($config[self::KEY]) && is_array($config[self::KEY])) {
            $options->setFromArray($config[self::KEY]);
        }

        if (!$options->getSerieTokenEntityClass()) {
            $options->setSerieTokenEntityClass('JwPersistentUser\Model\SerieToken');
        }

        return $options;
    }

    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        return $this($serviceLocator->getServiceLocator(), 'JwPersistentUser\Service\Cookie');
    }
}
