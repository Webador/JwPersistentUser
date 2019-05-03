<?php

namespace JwPersistentUser\Model;

interface SerieTokenInterface
{
    /**
     * @return string
     */
    public function getSerie();

    /**
     * @param string $serie
     */
    public function setSerie($serie);

    /**
     * @return string
     */
    public function getToken();

    /**
     * @param string $token
     */
    public function setToken($token);

    /**
     * @return int
     */
    public function getUserId();

    /**
     * @param int $userId
     */
    public function setUserId($userId);

    /**
     * @return \DateTime
     */
    public function getExpiresAt();

    /**
     * @param \DateTime $expiresAt
     */
    public function setExpiresAt($expiresAt);
}
