<?php

namespace JwPersistentUserTest\Service;

use JwPersistentUser\Model\SerieToken;
use JwPersistentUser\Model\ModuleOptions;
use JwPersistentUser\Service\RememberMeService;
use JwPersistentUser\Mapper\SerieTokenMapperInterface;

class RememberMeServiceTest extends \PHPUnit_Framework_TestCase
{
    const IP = '1.2.3.4';

    /**
     * @var RememberMeService
     */
    protected $service;

    /**
     * @var SerieTokenMapperInterface
     */
    protected $mapper;

    /**
     * @var ModuleOptions
     */
    protected $options;

    public function setUp()
    {
        parent::setUp();

        $this->service = new RememberMeService;

        $this->mapper = $this->getMock('JwPersistentUser\Mapper\SerieTokenMapperInterface');
        $this->service->setMapper($this->mapper);

        $this->service->setModuleOptions($this->options = new ModuleOptions);
        $this->options->setSerieTokenEntityClass('JwPersistentUser\Model\SerieToken');

        $ipService = $this->getMock('Zend\Http\PhpEnvironment\RemoteAddress');
        $ipService->expects($this->any())
            ->method('getIpAddress')
            ->will($this->returnValue(self::IP));
        $this->service->setIpService($ipService);
    }

    public function testCreateNew()
    {
        $this->mapper->expects($this->once())
            ->method('persist');

        $response = $this->service->createNew(3);

        $this->assertEquals(3, $response->getUserId());
        $this->assertEquals(10, strlen($response->getSerie()));
        $this->assertEquals(10, strlen($response->getToken()));
        $this->assertEquals(self::IP, $response->getIpAddress());
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

    public function testGetNextSerie()
    {
        $this->assumeSerie($serie = new SerieToken(3, 'abc', 'def'));
        $serie->setExpiresAt(new \DateTime('tomorrow'));

        $nextSerie = $this->service->getNextInSerie($serie);

        $this->assertSame($serie, $nextSerie);
        $this->assertNotEquals('def', $serie->getToken());
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
