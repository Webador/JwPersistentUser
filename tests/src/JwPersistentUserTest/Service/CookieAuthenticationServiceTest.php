<?php

namespace JwPersistentUserTest\Service;

use JwPersistentUser\Model\SerieToken;
use JwPersistentUser\Service\CookieService;
use JwPersistentUser\Service\RememberMeService;
use JwPersistentUser\Service\CookieAuthenticationService;

use Zend\Http\Request;
use Zend\Http\Response;
use Zend\Authentication\AuthenticationServiceInterface;

class CookieAuthenticationServiceTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var CookieAuthenticationService
     */
    protected $service;

    /**
     * @var RememberMeService
     */
    protected $rememberMeService;

    /**
     * @var AuthenticationServiceInterface
     */
    protected $authService;

    /**
     * @var CookieService
     */
    protected $cookieService;

    public function setUp()
    {
        parent::setUp();

        $this->service = new CookieAuthenticationService;

        $this->authService = $this->getMock('Zend\Authentication\AuthenticationServiceInterface');
        $this->service->setAuthService($this->authService);

        $this->rememberMeService = $this->getMock('JwPersistentUser\Service\RememberMeService');
        $this->service->setRememberMeService($this->rememberMeService);

        $this->cookieService = $this->getMock('JwPersistentUser\Service\CookieService');
        $this->service->setCookieService($this->cookieService);
    }

    public function testValidLogin()
    {
        $request = new Request;
        $response = new Response;

        $serieTokenInCookie = new SerieToken(1, 'abc', 'def');
        $newSerie = new SerieToken(1, 'abc', 'ghi');

        // Request contains cookie
        $this->cookieService->expects($this->once())
            ->method('read')
            ->with($request, $response)
            ->will($this->returnValue($serieTokenInCookie));

        // Response contains updated cookie
        $this->cookieService->expects($this->once())
            ->method('writeSerie')
            ->with($response, $newSerie);

        $newSerie->setExpiresAt(new \DateTime('+3 days'));
        $this->rememberMeService->expects($this->once())
            ->method('getNextInSerie')
            ->with($serieTokenInCookie)
            ->will($this->returnValue($newSerie));

        $this->authService->expects($this->once())
            ->method('authenticate');
        
        $this->service->loginFrom($request, $response);
    }

    /**
     * Assert that two dates do not differ more than $treshold seconds.
     *
     * This is a convenient method to test if dates are propably equal to each other.
     *
     * @param $expected
     * @param $actual
     * @param int $threshold
     */
    protected function assertDateTimeEquals($expected, $actual, $threshold = 10)
    {
        $this->assertInstanceOf('\DateTime', $expected);
        $this->assertInstanceOf('\DateTime', $actual);

        $diff = abs($expected->getTimestamp() - $actual->getTimestamp());

        $this->assertLessThan(
            $threshold,
            $diff,
            'Date objects differ too much (' . $diff . ' seconds, treshold is ' . $threshold . ')'
        );
    }
}
