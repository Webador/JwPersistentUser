<?php

namespace JwPersistentUserTest\Listener;

use JwPersistentUser\Model\SerieToken,
    JwPersistentUser\Service\RememberMeService,
    JwPersistentUser\Listener\WriteTokenToCookie;

use Zend\Http\Request,
    Zend\Http\Response,
    Zend\Http\Header\Cookie;

use ZfcUser\Authentication\Adapter\AdapterChainEvent;

class WriteToCookieTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var WriteTokenToCookie
     */
    protected $listener;

    /**
     * @var RememberMeService
     */
    protected $rememberMeService;

    /**
     * @var Request
     */
    protected $request;

    /**
     * @var Response
     */
    protected $response;

    public function setUp()
    {
        parent::setUp();

        $this->listener = new WriteTokenToCookie;

        $this->listener->setRequest($this->request = new Request);
        $this->listener->setResponse($this->response = new Response);

        $this->rememberMeService = $this->getMock('JwPersistentUser\Service\RememberMeService');
        $this->listener->setRememberMeService($this->rememberMeService);
    }

    /**
     * @dataProvider methods
     */
    public function testDoesNotActOnNonHttpRequest($method)
    {
        $this->listener->setRequest($this->getMock('Zend\StdLib\RequestInterface'));

        $this->rememberMeService->expects($this->never())
            ->method('createNew');

        $this->listener->{$method}(new AdapterChainEvent);
    }

    /**
     * @dataProvider methods
     */
    public function testDoesNotActOnNonHttpRespose($method)
    {
        $this->listener->setResponse($this->getMock('Zend\StdLib\ResponseInterface'));

        $this->rememberMeService->expects($this->never())
            ->method('createNew');

        $this->listener->{$method}(new AdapterChainEvent);
    }

    public function testGeneratesSerieToken()
    {
        $event = new AdapterChainEvent;
        $event->setIdentity(3);

        $returnToken = new SerieToken(3, 'abc', 'def');
        $returnToken->setExpiresAt(new \DateTime('+3 days'));

        $this->rememberMeService->expects($this->once())
            ->method('createNew')
            ->with(3)
            ->will($this->returnValue($returnToken));

        $this->listener->authenticate($event);

        // Cookie gets set
        $this->assertTrue($this->response->getHeaders()->has('SetCookie'));
        $cookie = $this->response->getHeaders()->get('SetCookie')->current();
        $this->assertInstanceOf('Zend\Http\Header\SetCookie', $cookie);
        $this->assertEquals('JwPersistentUser', $cookie->getName());
        $this->assertEquals('3:abc:def', $cookie->getValue());
        $this->assertDateTimeEquals(new \DateTime('+3 days'), new \DateTime($cookie->getExpires()));
        $this->assertEquals('/', $cookie->getPath());
    }

    public function testLogout()
    {
        // Request contains cookie
        $cookies = new Cookie([
            'JwPersistentUser' => '1:abc:def'
        ]);
        $headers = $this->request->getHeaders();
        $headers->addHeader($cookies);

        // Mentioned session is removed
        $this->rememberMeService->expects($this->once())
            ->method('removeSerie')
            ->with(1, 'abc');

        $this->listener->logout(new AdapterChainEvent);

        // Cookies gets deleted
        $this->assertTrue($this->response->getHeaders()->has('SetCookie'));
        $cookie = $this->response->getHeaders()->get('SetCookie')->current();
        $this->assertInstanceOf('Zend\Http\Header\SetCookie', $cookie);
        $this->assertEquals('JwPersistentUser', $cookie->getName());
        $this->assertDateTimeEquals(new \DateTime('-3600 seconds'), new \DateTime($cookie->getExpires()));
    }

    public function methods()
    {
        return [
            ['authenticate'],
            ['logout'],
        ];
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
