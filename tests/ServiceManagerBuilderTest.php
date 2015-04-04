<?php

namespace Kpacha\Config;

class ServiceManagerBuilderTest extends \PHPUnit_Framework_TestCase
{

    private static $serverConfig = array(
        AbstractServiceManager::SERVER_URL => 'someUrl',
        AbstractServiceManager::SERVICE_MANAGER => '\\Kpacha\\Config\\Dummies\\DummyServiceManager',
        AbstractServiceManager::WATCHED_SERVICES => array('service-a', 'service-b')
    );
    private $configFile;
    private $subject;

    public function setUp()
    {
        $this->configFile = __DIR__ . '/fixtures/' . Configuration::SOLVED_SERVICES . '.yml';
        $this->subject = new ServiceManagerBuilder;
    }

    /**
     * @expectedException \Exception
     * @expectedExceptionMessage The 'service-manager' config key is not set
     */
    public function testItThrowsExceptionIfServerIsNotDefined()
    {
        $this->subject->build($this->mockConfig(Configuration::SERVICE, array()));
    }

    /**
     * @expectedException \Exception
     * @expectedExceptionMessage The '\Some\Unknown\Class' service manager is not defined
     */
    public function testItThrowsExceptionIfServerManagerClassIsNotDefined()
    {
        $config = self::$serverConfig;
        $config[AbstractServiceManager::SERVICE_MANAGER] = '\\Some\\Unknown\\Class';
        $this->subject->build($this->mockConfig(Configuration::SERVICE, $config));
    }

    public function testTheWatchedServicesListIsEmptyIfTheConfigIsEmpty()
    {
        $config = self::$serverConfig;
        unset($config[AbstractServiceManager::WATCHED_SERVICES]);
        $serviceManager = $this->subject->build($this->mockConfig(Configuration::SERVICE, $config));
        $watchedServices = $serviceManager->getWatchedServices();
        $this->assertInternalType('array', $watchedServices);
        $this->assertCount(0, $watchedServices);
    }

    public function testTheWatchedServicesListIsNotEmptyIfTheConfigIsNotEmpty()
    {
        $serviceManager = $this->subject->build($this->mockConfig(Configuration::SERVICE, self::$serverConfig));
        $this->assertCount(2, $serviceManager->getWatchedServices());
    }

    public function testEveryServiceInTheWatchedServicesListIsRequestedToTheClient()
    {
        $serviceManager = $this->subject->build($this->mockConfig(Configuration::SERVICE, self::$serverConfig));
        $this->assertCount(2, $serviceManager->getUpdatedServices());
    }

    private function mockConfig($key, $returnValue)
    {
        $config = $this->getMock('\\Kpacha\\Config\\Configuration', array('get', 'getConfigDir'), array(), 'Config',
                false);
        $config->expects($this->once())->method('get')->with($key)->willReturn($returnValue);
        $config->expects($this->any())->method('getConfigDir')->willReturn(dirname($this->configFile));
        return $config;
    }

}
