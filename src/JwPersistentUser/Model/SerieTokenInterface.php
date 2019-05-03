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
     * The cookie will expire at this date. We should make sure to renew the cookie when this serie is used before that
     * time.
     *
     * @return \DateTime
     */
    public function getExpiresAt();

    /**
     * @param \DateTime $expiresAt
     */
    public function setExpiresAt($expiresAt);

    /**
     * This serie is no longer valid after this date.
     *
     * If `null`, this serie can stay valid forever.
     *
     * @return \DateTime|null
     */
    public function getValidUntil();

    /**
     * @param \DateTime|null $validUntil
     */
    public function setValidUntil($validUntil);
}
