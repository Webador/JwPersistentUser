<?php

namespace JwPersistentUser\Service;

use JwPersistentUser\Model\ModuleOptions;
use JwPersistentUser\Model\SerieTokenInterface;
use JwPersistentUser\Mapper\SerieTokenMapperInterface;

use Zend\Math\Rand;

class RememberMeService
{
    /**
     * @var SerieTokenMapperInterface
     */
    protected $mapper;

    /**
     * @var ModuleOptions
     */
    protected $moduleOptions;

    /**
     * @var UserValidityInterface
     */
    protected $userValidityInterface;

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
        $serieToken->setValidUntil($this->getUserValidityInterface()->getValidUntilForUser($userId));

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
        } else if ($matchingSerieToken->getValidUntil() && $matchingSerieToken->getValidUntil() < new \DateTime) {
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
     * @return UserValidityInterface
     */
    public function getUserValidityInterface()
    {
        return $this->userValidityInterface;
    }

    /**
     * @param UserValidityInterface $userValidityInterface
     * @return $this
     */
    public function setUserValidityInterface($userValidityInterface)
    {
        $this->userValidityInterface = $userValidityInterface;
        return $this;
    }
}
