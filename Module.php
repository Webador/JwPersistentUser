<?php

namespace JwPersistentUser;

use JwPersistentUser\Listener\WriteTokenToCookie;
use JwPersistentUser\Service\CookieAuthenticationService;
use Zend\EventManager\EventInterface;
use Zend\EventManager\EventManager;
use Zend\ModuleManager\Feature;
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
        $service->setEventManager(new EventManager($em->getSharedManager()));
        $service->loginFrom($request, $response);

        (new WriteTokenToCookie($sm))->attachShared($em->getSharedManager());
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
