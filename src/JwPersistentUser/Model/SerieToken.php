<?php

namespace JwPersistentUser\Model;

class SerieToken
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
     * @var string|null
     */
    protected $ipAddress;

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

    /**
     * @return string
     */
    public function getSerie()
    {
        return $this->serie;
    }

    /**
     * @param string $serie
     * @return $this
     */
    public function setSerie($serie)
    {
        $this->serie = $serie;
        return $this;
    }

    /**
     * @return string
     */
    public function getToken()
    {
        return $this->token;
    }

    /**
     * @param string $token
     * @return $this
     */
    public function setToken($token)
    {
        $this->token = $token;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getUserId()
    {
        return $this->userId;
    }

    /**
     * @param mixed $userId
     * @return $this
     */
    public function setUserId($userId)
    {
        $this->userId = $userId;
        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getExpiresAt()
    {
        return $this->expiresAt;
    }

    /**
     * @param \DateTime $expiresAt
     * @return $this
     */
    public function setExpiresAt($expiresAt)
    {
        $this->expiresAt = $expiresAt;
        return $this;
    }

    /**
     * @return null|string
     */
    public function getIpAddress()
    {
        return $this->ipAddress;
    }

    /**
     * @param null|string $ipAddress
     * @return $this
     */
    public function setIpAddress($ipAddress)
    {
        $this->ipAddress = $ipAddress;
        return $this;
    }
}
