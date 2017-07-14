<?php

namespace JwPersistentUser;

use JwPersistentUser\Listener\WriteTokenToCookie,
    JwPersistentUser\Service\CookieAuthenticationService;

use Zend\ModuleManager\Feature,
    Zend\EventManager\EventManager,
    Zend\EventManager\EventInterface;
use Zend\ServiceManager\ServiceManager;

class Module implements
    Feature\ConfigProviderInterface,
    Feature\BootstrapListenerInterface,
    Feature\AutoloaderProviderInterface
{
    public function getConfig()
    {
        return include __DIR__ . '/config/module.config.php';
    }

    public function onBootstrap(EventInterface $e)
    {
        /** @var EventManager $em */
        $em = $e->getApplication()->getEventManager();

        /** @var ServiceManager $sm */
        $sm = $e->getApplication()->getServiceManager();

        $request = $e->getApplication()->getRequest();
        $response = $e->getApplication()->getResponse();

        // Try to login from Cookie if applicable
        $service = new CookieAuthenticationService($sm);
        $service->loginFrom($request, $response);
    }

    public function getAutoloaderConfig()
    {
        return [
            'Zend\Loader\StandardAutoloader' => [
                'namespaces' => [
                    __NAMESPACE__ => __DIR__ . '/src/' . __NAMESPACE__,
                ],
            ],
        ];
    }
}
