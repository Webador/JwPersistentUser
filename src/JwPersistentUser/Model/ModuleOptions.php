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
     * @var string|null
     */
    protected $cookiePath = '/';

    /**
     * @var string|null
     */
    protected $cookieDomain = null;

    /**
     * @var bool|null
     */
    protected $cookieSecure = null;

    /**
     * @var bool|null
     */
    protected $cookieHttpOnly = null;

    /**
     * @var int|null
     */
    protected $cookieMaxAge = null;

    /**
     * @var int|null
     */
    protected $cookieVersion = null;

    /**
     * A valid SameSite option: `none`, `lax` or `strict`.
     *
     * @var string|null
     */
    protected $cookieSameSite = null;

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

    /**
     * @return string|null
     */
    public function getCookiePath()
    {
        return $this->cookiePath;
    }

    /**
     * @param $cookiePath
     * @return void
     */
    public function setCookiePath($cookiePath)
    {
        $this->cookiePath = $cookiePath;
    }

    /**
     * @return string|null
     */
    public function getCookieDomain()
    {
        return $this->cookieDomain;
    }

    /**
     * @param $cookieDomain
     * @return void
     */
    public function setCookieDomain($cookieDomain)
    {
        $this->cookieDomain = $cookieDomain;
    }

    /**
     * @return bool|null
     */
    public function getCookieSecure()
    {
        return $this->cookieSecure;
    }

    /**
     * @param $cookieSecure
     * @return void
     */
    public function setCookieSecure($cookieSecure)
    {
        $this->cookieSecure = $cookieSecure;
    }

    /**
     * @return bool|null
     */
    public function getCookieHttpOnly()
    {
        return $this->cookieHttpOnly;
    }

    /**
     * @param $cookieHttpOnly
     * @return void
     */
    public function setCookieHttpOnly($cookieHttpOnly)
    {
        $this->cookieHttpOnly = $cookieHttpOnly;
    }

    /**
     * @return int|null
     */
    public function getCookieMaxAge()
    {
        return $this->cookieMaxAge;
    }

    /**
     * @param $cookieMaxAge
     * @return void
     */
    public function setCookieMaxAge($cookieMaxAge)
    {
        $this->cookieMaxAge = $cookieMaxAge;
    }

    /**
     * @return int|null
     */
    public function getCookieVersion()
    {
        return $this->cookieVersion;
    }

    /**
     * @param $cookieVersion
     * @return void
     */
    public function setCookieVersion($cookieVersion)
    {
        $this->cookieVersion = $cookieVersion;
    }

    /**
     * @return string|null
     */
    public function getCookieSameSite()
    {
        return $this->cookieSameSite;
    }

    /**
     * @param $cookieSameSite
     * @return void
     */
    public function setCookieSameSite($cookieSameSite)
    {
        $this->cookieSameSite = $cookieSameSite;
    }
}
