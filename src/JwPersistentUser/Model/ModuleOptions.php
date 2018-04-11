<?php

namespace JwPersistentUser\Model;

use Zend\Stdlib\AbstractOptions;

class ModuleOptions extends AbstractOptions
{
    /**
     * @var string
     */
    protected $serieTokenEntityClass = 'JwPersistentUser\Model\SerieToken';

    /**
     * When enabled we will refresh the token each time it is used.
     *
     * If somebody were to hijack a session of an active user; this would eventually lead to this token being revoked.
     *
     * However: the big downside to this approach is that a token will also be revoked if a user did not receive
     * the (cookie's in the) response from the server. For example because the page loaded slow and he or she closed
     * the tab.
     *
     * @var bool
     */
    protected $tokenRefreshedAfterLogin = true;

    /**
     * @return string
     */
    public function getSerieTokenEntityClass()
    {
        return $this->serieTokenEntityClass;
    }

    /**
     * @param string $serieTokenEntityClass
     * @return $this
     */
    public function setSerieTokenEntityClass($serieTokenEntityClass)
    {
        $this->serieTokenEntityClass = $serieTokenEntityClass;
        return $this;
    }

    /**
     * @return bool
     */
    public function isTokenRefreshedAfterLogin()
    {
        return $this->tokenRefreshedAfterLogin;
    }

    /**
     * @param bool $tokenRefreshedAfterLogin
     */
    public function setTokenRefreshedAfterLogin($tokenRefreshedAfterLogin)
    {
        $this->tokenRefreshedAfterLogin = $tokenRefreshedAfterLogin;
    }
}
