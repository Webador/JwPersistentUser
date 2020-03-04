<?php

namespace JwPersistentUserTest\Service;

use JwPersistentUser\Model\SerieToken;
use Zend\Http\Header\Cookie;
use Zend\Http\Request;
use Zend\Http\Response;

use JwPersistentUser\Test\TestCase;
use JwPersistentUser\Model\ModuleOptions;
use JwPersistentUser\Service\CookieService;

class CookieServiceTest extends TestCase
{
    /**
     * @var ModuleOptions
     */
    private $options;

    /**
     * @var CookieService
     */
    protected $service;

    public function setUp()
    {
        $this->service = new CookieService;

        $this->options = new ModuleOptions;
        $this->options->setSerieTokenEntityClass('JwPersistentUser\Model\SerieToken');
        $this->service->setModuleOptions($this->options);
    }

    public function testReadSerie()
    {
        $request = new Request;
        $cookies = new Cookie([
            'JwPersistentUser' => '1:abc:def'
        ]);
        $headers = $request->getHeaders();
        $headers->addHeader($cookies);

        $response = new Response;

        $result = $this->service->read($request, $response);

        $this->assertFalse($response->getHeaders()->has('Set-Cookie'));

        $this->assertNotNull($result);
        $this->assertEquals(1, $result->getUserId());
        $this->assertEquals('abc', $result->getSerie());
        $this->assertEquals('def', $result->getToken());
    }

    public function testNoSerieRead()
    {
        $request = new Request;
        $response = new Response;

        $result = $this->service->read($request, $response);

        $this->assertNull($result);
    }

    public function testInvalidCookieNullifiesCookie()
    {
        $request = new Request;
        $cookies = new Cookie([
            'JwPersistentUser' => '1:in-valid'
        ]);
        $headers = $request->getHeaders();
        $headers->addHeader($cookies);

        $response = new Response;

        $result = $this->service->read($request, $response);

        $this->assertNull($result);

        $this->assertTrue($response->getHeaders()->has('Set-Cookie'));
        $cookie = $response->getHeaders()->get('Set-Cookie')->current();
        $this->assertInstanceOf('Zend\Http\Header\SetCookie', $cookie);
        $this->assertEquals('JwPersistentUser', $cookie->getName());
        $this->assertDateTimeEquals(new \DateTime('-1 hour'), new \DateTime($cookie->getExpires()));
        $this->assertEquals('/', $cookie->getPath());
        $this->assertTrue($cookie->isHttponly());
    }

    public function testWriteNull()
    {
        $response = new Response;

        $this->service->writeNull($response);

        $this->assertTrue($response->getHeaders()->has('Set-Cookie'));
        $cookie = $response->getHeaders()->get('Set-Cookie')->current();
        $this->assertInstanceOf('Zend\Http\Header\SetCookie', $cookie);
        $this->assertEquals('JwPersistentUser', $cookie->getName());
        $this->assertDateTimeEquals(new \DateTime('-1 hour'), new \DateTime($cookie->getExpires()));
        $this->assertEquals('/', $cookie->getPath());
        $this->assertTrue($cookie->isHttponly());
    }

    public function testWriteSerie()
    {
        $serie = new SerieToken(3, 'abc', 'def');
        $serie->setExpiresAt(new \DateTime('+1 year'));
        $response = new Response;

        $this->service->writeSerie($response, $serie);

        $this->assertTrue($response->getHeaders()->has('Set-Cookie'));
        $cookie = $response->getHeaders()->get('Set-Cookie')->current();
        $this->assertInstanceOf('Zend\Http\Header\SetCookie', $cookie);
        $this->assertEquals('JwPersistentUser', $cookie->getName());
        $this->assertEquals('3:abc:def', $cookie->getValue());
        $this->assertDateTimeEquals(new \DateTime('+1 year'), new \DateTime($cookie->getExpires()));
        $this->assertEquals('/', $cookie->getPath());
        $this->assertEquals(null, $cookie->getSameSite());
        $this->assertNull($cookie->isSecure());
    }

    public function testWithCustomCookieOptions()
    {
        $this->options->setCookiePath('/foo');
        $this->options->setCookieDomain('bla.nl');
        $this->options->setCookieHttpOnly(true);
        $this->options->setCookieSameSite('none');
        $this->options->setCookieSecure(true);
        $this->options->setCookieVersion(3);
        $this->options->setCookieMaxAge(300);

        $serie = new SerieToken(3, 'abc', 'def');
        $serie->setExpiresAt(new \DateTime('+1 year'));
        $response = new Response;

        $this->service->writeSerie($response, $serie);

        $this->assertTrue($response->getHeaders()->has('Set-Cookie'));
        $cookie = $response->getHeaders()->get('Set-Cookie')->current();
        $this->assertInstanceOf('Zend\Http\Header\SetCookie', $cookie);

        $this->assertEquals('/foo', $cookie->getPath());
        $this->assertEquals('bla.nl', $cookie->getDomain());
        $this->assertTrue($cookie->isHttpOnly());
        $this->assertEquals('None', $cookie->getSameSite());
        $this->assertEquals(300, $cookie->getMaxAge());
        $this->assertEquals(3, $cookie->getVersion());
        $this->assertTrue($cookie->isSecure());
    }
}
