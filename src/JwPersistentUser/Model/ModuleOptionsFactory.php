<?php

namespace JwPersistentUser\Model;

use Zend\ServiceManager\FactoryInterface,
    Zend\ServiceManager\ServiceLocatorInterface;

class ModuleOptionsFactory implements FactoryInterface
{
    const KEY = 'jwpersistentuser';

    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $options = new ModuleOptions;

        $config = $serviceLocator->get('Config');
        if (isset($config[self::KEY]) && is_array($config[self::KEY])) {
            $options->setFromArray($config[self::KEY]);
        }

        return $options;
    }
}
