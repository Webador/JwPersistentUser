<?php

namespace JwPersistentUserTest\Service;

use JwPersistentUser\Test\TestCase;
use JwPersistentUser\Model\SerieToken;
use JwPersistentUser\Model\ModuleOptions;
use JwPersistentUser\Service\RememberMeService;
use JwPersistentUser\Mapper\SerieTokenMapperInterface;
use JwPersistentUser\Service\UserValidityInterface;

class RememberMeServiceTest extends TestCase
{
    /**
     * @var RememberMeService
     */
    protected $service;

    /**
     * @var SerieTokenMapperInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $mapper;

    /**
     * @var ModuleOptions|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $options;

    /**
     * @var UserValidityInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $userValidityInterface;

    public function setUp()
    {
        parent::setUp();

        $this->service = new RememberMeService;

        $this->mapper = $this->getMock('JwPersistentUser\Mapper\SerieTokenMapperInterface');
        $this->service->setMapper($this->mapper);

        $this->userValidityInterface = $this->getMock(UserValidityInterface::class);
        $this->service->setUserValidityInterface($this->userValidityInterface);

        $this->service->setModuleOptions($this->options = new ModuleOptions);
        $this->options->setSerieTokenEntityClass('JwPersistentUser\Model\SerieToken');
    }

    public function testCreateNew()
    {
        $this->mapper->expects($this->once())
            ->method('persist');

        $this
            ->userValidityInterface
            ->expects($this->any())
            ->method('getValidUntilForUser')
            ->with(3)
            ->willReturn(null);

        $response = $this->service->createNew(3);

        $this->assertEquals(3, $response->getUserId());
        $this->assertEquals(10, strlen($response->getSerie()));
        $this->assertEquals(10, strlen($response->getToken()));
        $this->assertNull($response->getValidUntil());
    }

    public function testCreateNewWithValidity()
    {
        $this->mapper->expects($this->once())
            ->method('persist');

        $validUntil = new \DateTime('tomorrow');
        $this
            ->userValidityInterface
            ->expects($this->any())
            ->method('getValidUntilForUser')
            ->with(3)
            ->willReturn($validUntil);

        $response = $this->service->createNew(3);

        $this->assertEquals(3, $response->getUserId());
        $this->assertEquals(10, strlen($response->getSerie()));
        $this->assertEquals(10, strlen($response->getToken()));
        $this->assertEquals($validUntil, $response->getValidUntil());
    }

    public function testRemoveSerie()
    {
        $this->assumeSerie($serie = new SerieToken(3, 'abc', 'def'));
        $serie->setExpiresAt(new \DateTime('tomorrow'));

        $this->mapper->expects($this->once())
            ->method('remove')
            ->with($serie);

        $this->service->removeSerie(3, 'abc');
    }

    public function testGetNextSerieWhenRefreshDisabled()
    {
        $this->options->setTokenRefreshedAfterLogin(false);

        $this->assumeSerie($serie = new SerieToken(3, 'abc', 'def'));
        $serie->setExpiresAt(new \DateTime('tomorrow'));

        $nextSerie = $this->service->getNextInSerie($serie);

        $this->assertSame($serie, $nextSerie);
        $this->assertEquals('def', $serie->getToken());

        $this->assertDateTimeEquals(new \DateTime('+1 year'), $serie->getExpiresAt());
    }

    public function testGetNextSerie()
    {
        $this->assumeSerie($serie = new SerieToken(3, 'abc', 'def'));
        $serie->setExpiresAt(new \DateTime('tomorrow'));

        $nextSerie = $this->service->getNextInSerie($serie);

        $this->assertSame($serie, $nextSerie);
        $this->assertNotEquals('def', $serie->getToken());

        $this->assertDateTimeEquals(new \DateTime('+1 year'), $serie->getExpiresAt());
    }

    public function testSerieExpired()
    {
        $this->assumeSerie($serie = new SerieToken(3, 'abc', 'def'));
        $serie->setExpiresAt(new \DateTime('yesterday'));

        $nextSerie = $this->service->getNextInSerie($serie);

        $this->assertNull($nextSerie);
    }

    public function testNoSerieFound()
    {
        $serie = new SerieToken(3, 'abc', 'def');
        $serie->setExpiresAt(new \DateTime('tomorrow'));

        $nextSerie = $this->service->getNextInSerie($serie);

        $this->assertNull($nextSerie);
    }

    public function testSerieWithInvalidTokenResets()
    {
        $this->assumeSerie($correctSerie = new SerieToken(3, 'abc', 'def'));
        $correctSerie->setExpiresAt(new \DateTime('tomorrow'));
        $falseSerie = new SerieToken(3, 'abc', 'gdaf');

        $this->mapper->expects($this->once())
            ->method('remove')
            ->with($correctSerie);

        $nextSerie = $this->service->getNextInSerie($falseSerie);

        $this->assertNull($nextSerie);
    }

    public function testNoLongerValidTokenResets()
    {
        $this->assumeSerie($serie = new SerieToken(3, 'abc', 'def'));
        $serie->setExpiresAt(new \DateTime('tomorrow'));
        $serie->setValidUntil(new \DateTime('yesterday'));

        $nextSerie = $this->service->getNextInSerie($serie);

        $this->assertNull($nextSerie);
    }

    /**
     * @param SerieToken $serie
     */
    protected function assumeSerie(SerieToken $serie)
    {
        $this->mapper->expects($this->any())
            ->method('find')
            ->with($serie->getUserId(), $serie->getSerie())
            ->will($this->returnValue($serie));
    }
}
