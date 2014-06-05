<?php

namespace JwPersistentUser\Service;

use JwPersistentUser\Model\SerieToken;

use Zend\Http\Request,
    Zend\Http\Response,
    Zend\Http\Header\SetCookie;

abstract class CookieMonster
{
    const COOKIE_NAME = 'JwPersistentUser';

    /**
     * @param Request $request
     * @param Response|null $response
     * @return SerieToken|null
     */
    public static function read(Request $request, Response $response = null)
    {
        $cookie = $request->getCookie();
        if (!isset($cookie[self::COOKIE_NAME])) {
            return null;
        }

        $parts = explode(':', $cookie[self::COOKIE_NAME]);
        if (!is_array($parts) || count($parts) !== 3) {
            if ($response) {
                self::writeNull($response);
            }
            return null;
        }

        $serieToken = new SerieToken;
        return $serieToken
            ->setUserId($parts[0])
            ->setSerie($parts[1])
            ->setToken($parts[2]);
    }

    /**
     * @return SetCookie
     */
    public static function writeNull(Response $response)
    {
        self::setCookie($response, new SetCookie(
            self::COOKIE_NAME,
            null,
            time()  - 3600,
            '/'
        ));
    }

    /**
     * @param SerieToken $serieToken
     * @return SetCookie
     */
    public static function writeSerie(Response $response, SerieToken $serieToken)
    {
        $serieRepresentation =
                  $serieToken->getUserId() .
            ':' . $serieToken->getSerie() .
            ':' . $serieToken->getToken();

        self::setCookie($response, new SetCookie(
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
}
