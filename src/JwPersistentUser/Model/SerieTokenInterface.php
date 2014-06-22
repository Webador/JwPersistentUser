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
     * @return $this
     */
    public function setSerie($serie);

    /**
     * @return string
     */
    public function getToken();

    /**
     * @param string $token
     * @return $this
     */
    public function setToken($token);

    /**
     * @return int
     */
    public function getUserId();

    /**
     * @param mixed $userId
     * @return $this
     */
    public function setUserId($userId);

    /**
     * @return \DateTime
     */
    public function getExpiresAt();

    /**
     * @param \DateTime $expiresAt
     * @return $this
     */
    public function setExpiresAt($expiresAt);

    /**
     * @return string
     */
    public function getIpAddress();

    /**
     * @param string $ipAddress
     * @return $this
     */
    public function setIpAddress($ipAddress);

    /**
     * @return string
     */
    public function getUserAgent();

    /**
     * @param string $userAgent
     * @return $this
     */
    public function setUserAgent($userAgent);
}
