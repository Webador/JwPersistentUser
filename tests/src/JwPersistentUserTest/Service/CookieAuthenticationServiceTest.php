<?php

namespace JwPersistentUserTest\Service;

use JwPersistentUser\Model\SerieToken,
    JwPersistentUser\Service\RememberMeService,
    JwPersistentUser\Service\CookieAuthenticationService;

use Zend\Http\Request,
    Zend\Http\Response,
    Zend\Http\Header\Cookie,
    Zend\Authentication\AuthenticationServiceInterface;

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

    public function setUp()
    {
        parent::setUp();

        $this->service = new CookieAuthenticationService;

        $this->authService = $this->getMock('Zend\Authentication\AuthenticationServiceInterface');
        $this->service->setAuthService($this->authService);

        $this->rememberMeService = $this->getMock('JwPersistentUser\Service\RememberMeService');
        $this->service->setRememberMeService($this->rememberMeService);
    }

    public function testValidLogin()
    {
        $request = new Request;
        $response = new Response;

        // Request contains cookie
        $cookies = new Cookie([
            'JwPersistentUser' => '1:abc:def'
        ]);
        $headers = $request->getHeaders();
        $headers->addHeader($cookies);

        $this->rememberMeService->expects($this->once())
            ->method('getNextInSerie')
            ->will($this->returnValue($newSerie = new SerieToken(1, 'abc', 'ghi')));
        $newSerie->setExpiresAt(new \DateTime('+3 days'));

        $this->authService->expects($this->once())
            ->method('authenticate');
        
        $this->service->loginFrom($request, $response);

        // Cookie gets set
        $this->assertTrue($response->getHeaders()->has('SetCookie'));
        $cookie = $response->getHeaders()->get('SetCookie')->current();
        $this->assertInstanceOf('Zend\Http\Header\SetCookie', $cookie);
        $this->assertEquals('JwPersistentUser', $cookie->getName());
        $this->assertEquals('1:abc:ghi', $cookie->getValue());
        $this->assertDateTimeEquals(new \DateTime('+3 days'), new \DateTime($cookie->getExpires()));
        $this->assertEquals('/', $cookie->getPath());
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

        $this->assertLessThan($threshold, $diff, 'Date objects differ too much (' . $diff . ' seconds, treshold is ' . $threshold . ')');
    }
}
