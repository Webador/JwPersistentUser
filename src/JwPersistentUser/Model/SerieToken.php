<?php

namespace JwPersistentUser\Model;

class SerieToken implements SerieTokenInterface
{
    /**
     * @var int
     */
    protected $userId;

    /**
     * @var string
     */
    protected $serie;

    /**
     * @var string
     */
    protected $token;

    /**
     * @var \DateTime
     */
    protected $expiresAt;

    /**
     * @var string
     */
    protected $ipAddress;

    /**
     * @var string
     */
    protected $userAgent;

    /**
     * @param null|int $userId
     * @param null|string $serie
     * @param null|string $token
     */
    public function __construct($userId = null, $serie = null, $token = null)
    {
        $this
            ->setUserId($userId)
            ->setSerie($serie)
            ->setToken($token);
    }

    public function getSerie()
    {
        return $this->serie;
    }

    public function setSerie($serie)
    {
        $this->serie = $serie;
        return $this;
    }

    public function getToken()
    {
        return $this->token;
    }

    public function setToken($token)
    {
        $this->token = $token;
        return $this;
    }

    public function getUserId()
    {
        return $this->userId;
    }

    public function setUserId($userId)
    {
        $this->userId = $userId;
        return $this;
    }

    public function getExpiresAt()
    {
        return $this->expiresAt;
    }

    public function setExpiresAt($expiresAt)
    {
        $this->expiresAt = $expiresAt;
        return $this;
    }

    public function getIpAddress()
    {
        return $this->ipAddress;
    }

    public function setIpAddress($ipAddress)
    {
        $this->ipAddress = $ipAddress;
        return $this;
    }

    public function setUserAgent($userAgent)
    {
        $this->userAgent = $userAgent;
        return $this;
    }

    public function getUserAgent()
    {
        return $this->userAgent;
    }
}
