<?php

namespace JwPersistentUser\Listener;

use Interop\Container\ContainerInterface;
use Interop\Container\Exception\ContainerException;
use JwPersistentUser\Service\CookieService;
use JwPersistentUser\Service\RememberMeService;

use Zend\EventManager\Event;
use Zend\Http\Request;
use Zend\Http\Response;
use Zend\ServiceManager\Exception\ServiceNotCreatedException;
use Zend\ServiceManager\Exception\ServiceNotFoundException;
use Zend\ServiceManager\Factory\DelegatorFactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\Stdlib\RequestInterface;
use Zend\Stdlib\ResponseInterface;
use Zend\EventManager\SharedEventManagerInterface;

use ZfcUser\Authentication\Adapter\AdapterChain;
use ZfcUser\Authentication\Adapter\AdapterChainEvent;

class WriteTokenToCookie implements DelegatorFactoryInterface
{
    /**
     * @var []
     */
    protected $sharedListeners = [];

    /**
     * @var ContainerInterface
     */
    protected $serviceLocator;

    /**
     * @var RequestInterface
     */
    protected $request;

    /**
     * @var ResponseInterface
     */
    protected $response;

    /**
     * @var RememberMeService
     */
    protected $rememberMeService;

    /**
     * @var CookieService
     */
    protected $cookieService;

    public function __invoke(ContainerInterface $container, $name, callable $callback, array $options = null)
    {
        $this->serviceLocator = $container;

        /** @var AdapterChain $original */
        $original = call_user_func($callback);

        $original->getEventManager()->attach(
            'authenticate.success',
            [$this, 'authenticate']
        );

        $original->getEventManager()->attach(
            'logout',
            [$this, 'logout']
        );

        return $original;
    }

    public function authenticate(Event $e)
    {
        $e = $e->getTarget();

        if (!$this->isValidRequestAndResponse()) {
            return;
        }

        $serieToken = $this->getRememberMeService()->createNew($e->getIdentity());

        $this->getCookieService()->writeSerie($this->getResponse(), $serieToken);
    }

    public function logout(Event $e)
    {
        $e = $e->getTarget();

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

    public function detachShared(SharedEventManagerInterface $events)
    {
        foreach ($this->sharedListeners as $index => $listener) {
            if ($events->detach($this->eventIdentifier, $listener)) {
                unset($this->sharedListeners[$index]);
            }
        }
    }

    /**
     * @return RememberMeService
     */
    public function getRememberMeService()
    {
        if ($this->rememberMeService === null) {
            $this->rememberMeService = $this->serviceLocator->get('JwPersistentUser\Service\RememberMe');
        }
        return $this->rememberMeService;
    }

    /**
     * @param RememberMeService $rememberMeService
     * @return $this
     */
    public function setRememberMeService($rememberMeService)
    {
        $this->rememberMeService = $rememberMeService;
        return $this;
    }

    /**
     * @return ResponseInterface
     */
    public function getResponse()
    {
        if (!$this->response) {
            $this->response = $this->serviceLocator->get('Response');
        }

        return $this->response;
    }

    /**
     * @param ResponseInterface $response
     * @return $this
     */
    public function setResponse($response)
    {
        $this->response = $response;
        return $this;
    }

    /**
     * @return RequestInterface
     */
    public function getRequest()
    {
        if (!$this->request) {
            $this->request = $this->serviceLocator->get('Request');
        }

        return $this->request;
    }

    /**
     * @param RequestInterface $request
     * @return $this
     */
    public function setRequest($request)
    {
        $this->request = $request;
        return $this;
    }

    /**
     * @return CookieService
     */
    public function getCookieService()
    {
        if ($this->cookieService === null) {
            $this->cookieService = $this->serviceLocator->get('JwPersistentUser\Service\Cookie');
        }
        return $this->cookieService;
    }

    /**
     * @param CookieService $cookieService
     * @return $this
     */
    public function setCookieService($cookieService)
    {
        $this->cookieService = $cookieService;
        return $this;
    }
}
