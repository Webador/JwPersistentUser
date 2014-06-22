<?php

namespace JwPersistentUser\Service;

use JwPersistentUser\Model\ModuleOptions;
use JwPersistentUser\Model\SerieTokenInterface;

use Zend\Http\Request;
use Zend\Http\Response;
use Zend\Http\Header\SetCookie;

class CookieService
{
    const COOKIE_NAME = 'JwPersistentUser';

    /**
     * @var ModuleOptions
     */
    protected $moduleOptions;

    /**
     * @param Request $request
     * @param Response|null $response
     * @return SerieTokenInterface|null
     */
    public function read(Request $request, Response $response = null)
    {
        $cookie = $request->getCookie();
        if (!isset($cookie[self::COOKIE_NAME])) {
            return null;
        }

        $parts = explode(':', $cookie[self::COOKIE_NAME]);
        if (!is_array($parts) || count($parts) !== 3) {
            if ($response) {
                $this->writeNull($response);
            }
            return null;
        }

        $serieTokenEntityClass = $this->getModuleOptions()->getSerieTokenEntityClass();
        $serieToken = new $serieTokenEntityClass;
        $serieToken->setUserId($parts[0]);
        $serieToken->setSerie($parts[1]);
        $serieToken->setToken($parts[2]);

        return $serieToken;
    }

    /**
     * @return SetCookie
     */
    public function writeNull(Response $response)
    {
        $this->setCookie($response, new SetCookie(
            self::COOKIE_NAME,
            null,
            time()  - 3600,
            '/'
        ));
    }

    /**
     * @param SerieTokenInterface $serieToken
     * @return SetCookie
     */
    public function writeSerie(Response $response, SerieTokenInterface $serieToken)
    {
        $serieRepresentation =
                  $serieToken->getUserId() .
            ':' . $serieToken->getSerie() .
            ':' . $serieToken->getToken();

        $this->setCookie($response, new SetCookie(
            self::COOKIE_NAME,
            $serieRepresentation,
            $serieToken->getExpiresAt()->getTimestamp(),
            '/'
        ));
    }

    /**
     * @param Response $response
     * @param SetCookie $cookie
     */
    protected static function setCookie(Response $response, SetCookie $cookie)
    {
        $response->getHeaders()->addHeader($cookie);
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
}
