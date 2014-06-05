<?php

namespace JwPersistentUser;

use JwPersistentUser\Listener\WriteTokenToCookie,
    JwPersistentUser\Service\CookieAuthenticationService;

use Zend\ModuleManager\Feature,
    Zend\EventManager\EventManager,
    Zend\EventManager\EventInterface;

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

        $request = $e->getApplication()->getRequest();
        $response = $e->getApplication()->getResponse();

        // Write token to cookie after valid authentication
        $placeCookie = new WriteTokenToCookie;
        $placeCookie->setRequest($request);
        $placeCookie->setResponse($response);
        $placeCookie->setServiceLocator($e->getApplication()->getServiceManager());
        $em->getSharedManager()->attachAggregate($placeCookie);

        // Try to login from Cookie if applicable
        $service = new CookieAuthenticationService;
        $service->setServiceLocator($e->getApplication()->getServiceManager());
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
