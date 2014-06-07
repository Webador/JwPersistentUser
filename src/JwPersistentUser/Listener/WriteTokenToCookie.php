<?php

namespace JwPersistentUser\Listener;

use JwPersistentUser\Service\CookieService;
use JwPersistentUser\Service\RememberMeService;

use Zend\Http\Request;
use Zend\Http\Response;
use Zend\Stdlib\RequestInterface;
use Zend\Stdlib\ResponseInterface;
use Zend\ServiceManager\ServiceLocatorAwareTrait;
use Zend\EventManager\SharedEventManagerInterface;
use Zend\EventManager\SharedListenerAggregateInterface;

use ZfcUser\Authentication\Adapter\AdapterChainEvent;

class WriteTokenToCookie implements SharedListenerAggregateInterface
{
    use ServiceLocatorAwareTrait;

    /**
     * @var []
     */
    protected $sharedListeners = [];

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

    public function attachShared(SharedEventManagerInterface $events)
    {
        $this->sharedListeners[] = $events->attach(
            'ZfcUser\Authentication\Adapter\AdapterChain',
            'authenticate.success',
            [$this, 'authenticate']
        );

        $this->sharedListeners[] = $events->attach(
            'ZfcUser\Authentication\Adapter\AdapterChain',
            'logout',
            [$this, 'logout']
        );
    }

    /**
     * @param AdapterChainEvent $e
     */
    public function authenticate(AdapterChainEvent $e)
    {
        if (!$this->isValidRequestAndResponse()) {
            return;
        }

        $serieToken = $this->getRememberMeService()->createNew($e->getIdentity());

        $this->getCookieService()->writeSerie($this->getResponse(), $serieToken);
    }

    /**
     * @param AdapterChainEvent $e
     */
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
            $this->rememberMeService = $this->getServiceLocator()->get('JwPersistentUser\Service\RememberMe');
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
            $this->cookieService = $this->getServiceLocator()->get('JwPersistentUser\Service\Cookie');
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
