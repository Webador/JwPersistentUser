<?php

namespace JwPersistentUser\Listener;

use Interop\Container\ContainerInterface;
use JwPersistentUser\Service\CookieService;
use JwPersistentUser\Service\RememberMeService;
use Zend\EventManager\SharedEventManagerInterface;
use Zend\Http\Request;
use Zend\Http\Response;
use Zend\Stdlib\RequestInterface;
use Zend\Stdlib\ResponseInterface;
use ZfcUser\Authentication\Adapter\AdapterChainEvent;

class WriteTokenToCookie
{
    protected $sharedListeners = [];

    /**
     * @var ContainerInterface
     */
    protected $serviceLocator;

    public function __construct(ContainerInterface $serviceLocator)
    {
        $this->serviceLocator = $serviceLocator;
    }

    public function attachShared(SharedEventManagerInterface $events)
    {
        $events->attach(
            'ZfcUser\Authentication\Adapter\AdapterChain',
            'authenticate.success',
            [$this, 'authenticate']
        );

        $events->attach(
            'ZfcUser\Authentication\Adapter\AdapterChain',
            'logout',
            [$this, 'logout']
        );
    }

    public function detachShared(SharedEventManagerInterface $events)
    {
        foreach ($this->sharedListeners as $key => $handle) {
            if ($events->detach($handle[0], $handle[1])) {
                unset($this->sharedListeners[$key]);
            }
        }
    }

    public function authenticate(AdapterChainEvent $e)
    {
        if (!$this->isValidRequestAndResponse()) {
            return;
        }

        $serieToken = $this->getRememberMeService()->createNew($e->getIdentity());

        $this->getCookieService()->writeSerie($this->getResponse(), $serieToken);
    }

    public function logout(AdapterChainEvent $e)
    {
        if (!$this->isValidRequestAndResponse()) {
            return;
        }

        $serieToken = $this->getCookieService()->read($this->getRequest(), $this->getResponse());
        if ($serieToken) {
            $this->getRememberMeService()->removeSerie($serieToken->getUserId(), $serieToken->getSerie());
        }

        $this->getCookieService()->writeNull($this->getResponse());
    }

    /**
     * @return bool
     */
    protected function isValidRequestAndResponse()
    {
        return $this->getRequest() instanceof Request
            && $this->getResponse() instanceof Response;
    }

    /**
     * @return RememberMeService
     */
    public function getRememberMeService()
    {
        return $this->serviceLocator->get('JwPersistentUser\Service\RememberMe');
    }

    /**
     * @return ResponseInterface
     */
    public function getResponse()
    {
        return $this->serviceLocator->get('Response');
    }

    /**
     * @return RequestInterface
     */
    public function getRequest()
    {
        return $this->serviceLocator->get('Request');
    }

    /**
     * @return CookieService
     */
    public function getCookieService()
    {
        return $this->serviceLocator->get('JwPersistentUser\Service\Cookie');
    }
}
