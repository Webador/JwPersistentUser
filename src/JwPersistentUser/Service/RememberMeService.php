<?php

namespace JwPersistentUser\Service;

use JwPersistentUser\Model\SerieToken,
    JwPersistentUser\Mapper\SerieTokenMapperInterface;

use Zend\Math\Rand;

class RememberMeService
{
    /**
     * @var SerieTokenMapperInterface
     */
    protected $mapper;

    /**
     * @param int $userId
     * @return SerieToken
     */
    public function createNew($userId)
    {
        $serieToken = new SerieToken;
        $serieToken
            ->setUserId($userId)
            ->setSerie($this->generateRandom())
            ->setToken($this->generateRandom())
            ->setExpiresAt(new \DateTime('+1 year'));

        $this->getMapper()->persist($serieToken);

        return $serieToken;
    }

    /**
     * @param SerieToken $serieToken
     * @return SerieToken|null
     */
    public function getNextInSerie(SerieToken $serieToken)
    {
        $matchingSerieToken = $this->getMapper()->find($serieToken->getUserId(), $serieToken->getSerie());
        if (!$matchingSerieToken) {
            return null;
        } else if (!$matchingSerieToken->getExpiresAt() || $matchingSerieToken->getExpiresAt() < new \DateTime) {
            return null;
        } else if ($matchingSerieToken->getToken() !== $serieToken->getToken()) {
            $this->removeSerie($serieToken->getUserId(), $serieToken->getSerie());
            return null;
        }

        // Generate new token in the serie
        $matchingSerieToken->setToken($this->generateRandom());
        $this->getMapper()->persist($matchingSerieToken);

        return $matchingSerieToken;
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
}
