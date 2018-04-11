<?php

namespace JwPersistentUser\Service;

use JwPersistentUser\Model\ModuleOptions;
use JwPersistentUser\Model\SerieTokenInterface;
use JwPersistentUser\Mapper\SerieTokenMapperInterface;

use Zend\Math\Rand;
use Zend\Http\Request;
use Zend\Http\PhpEnvironment\RemoteAddress;

class RememberMeService
{
    /**
     * @var Request
     */
    protected $request;

    /**
     * @var RemoteAddress
     */
    protected $ipService;

    /**
     * @var SerieTokenMapperInterface
     */
    protected $mapper;

    /**
     * @var ModuleOptions
     */
    protected $moduleOptions;

    /**
     * @param int $userId
     * @return SerieTokenInterface
     */
    public function createNew($userId)
    {
        $serieTokenClass = $this->getModuleOptions()->getSerieTokenEntityClass();
        $serieToken = new $serieTokenClass;

        /** @var $serieToken SerieTokenInterface */
        $serieToken->setUserId($userId);
        $serieToken->setSerie($this->generateRandom());
        $serieToken->setToken($this->generateRandom());
        $serieToken->setExpiresAt($this->getNewExpireDate());
        $serieToken->setIpAddress($this->getIpService()->getIpAddress());

        $userAgentHeader = $this->getRequest()->getHeader('UserAgent');
        if ($userAgentHeader !== false) {
            $serieToken->setUserAgent($userAgentHeader->getFieldValue());
        } else {
            $serieToken->setUserAgent('');
        }

        $this->getMapper()->persist($serieToken);

        return $serieToken;
    }

    /**
     * @param SerieTokenInterface $serieToken
     * @return SerieTokenInterface|null
     */
    public function getNextInSerie(SerieTokenInterface $serieToken)
    {
        $matchingSerieToken = $this->getMapper()->find($serieToken->getUserId(), $serieToken->getSerie());
        if (!$matchingSerieToken) {
            return null;
        } elseif (!$matchingSerieToken->getExpiresAt() || $matchingSerieToken->getExpiresAt() < new \DateTime) {
            return null;
        } elseif ($matchingSerieToken->getToken() !== $serieToken->getToken()) {
            $this->removeSerie($serieToken->getUserId(), $serieToken->getSerie());
            return null;
        }

        if ($this->moduleOptions->isTokenRefreshedAfterLogin()) {
            // Generate new token in the serie
            $matchingSerieToken->setToken($this->generateRandom());
        }

        $matchingSerieToken->setExpiresAt($this->getNewExpireDate());

        $this->getMapper()->persist($matchingSerieToken);

        return $matchingSerieToken;
    }

    /**
     * @return \DateTime
     */
    protected function getNewExpireDate()
    {
        return new \DateTime('+1 year');
    }

    /**
     * @param int $userId
     * @param string $serieId
     */
    public function removeSerie($userId, $serieId)
    {
        $serieToken = $this->getMapper()->find($userId, $serieId);
        if ($serieToken) {
            $this->getMapper()->remove($serieToken);
        }
    }

    /**
     * @note Should NOT contain semicolons.
     *
     * @return string
     */
    private function generateRandom()
    {
        return Rand::getString(10);
    }

    /**
     * @return SerieTokenMapper
     */
    public function getMapper()
    {
        return $this->mapper;
    }

    /**
     * @param SerieTokenMapper $mapper
     * @return $this
     */
    public function setMapper($mapper)
    {
        $this->mapper = $mapper;
        return $this;
    }

    /**
     * @return ModuleOptions
     */
    public function getModuleOptions()
    {
        return $this->moduleOptions;
    }

    /**
     * @param ModuleOptions $moduleOptions
     * @return $this
     */
    public function setModuleOptions($moduleOptions)
    {
        $this->moduleOptions = $moduleOptions;
        return $this;
    }

    /**
     * @param RemoteAddress $ipService
     * @return $this
     */
    public function setIpService($ipService)
    {
        $this->ipService = $ipService;
        return $this;
    }

    /**
     * @return RemoteAddress
     */
    public function getIpService()
    {
        return $this->ipService;
    }

    /**
     * @param Request $request
     */
    public function setRequest($request)
    {
        $this->request = $request;
    }

    /**
     * @return Request
     */
    public function getRequest()
    {
        return $this->request;
    }
}
