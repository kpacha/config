<?php

namespace Kpacha\Config;

use Symfony\Component\Filesystem\Filesystem;

class AbstractServiceManagerTest extends \PHPUnit_Framework_TestCase
{

    const FIXTURES_PATH = '/fixtures/';
    const SERVER_URL = 'someUrl';

    private static $watchedServices = array('service-a', 'service-b');
    private static $filesystem;
    private $configPath;
    private $configFile;
    private $globalConfigFile;
    private $globalConfigMetaFile;

    public static function setUpBeforeClass()
    {
        self::$filesystem = new Filesystem;
    }

    public function setUp()
    {
        $this->configPath = __DIR__ . self::FIXTURES_PATH;
        $this->configFile = $this->configPath . Configuration::SOLVED_SERVICES . '.yml';
        $this->globalConfigFile = $this->configPath . Configuration::CACHE_FILE;
        $this->globalConfigMetaFile = $this->globalConfigFile . '.meta';
        $this->clearFilesystem();
    }

    public function tearDown()
    {
        $this->clearFilesystem();
    }

    private function clearFilesystem()
    {
        self::$filesystem->remove($this->configFile);
        self::$filesystem->remove($this->globalConfigFile);
        self::$filesystem->remove($this->globalConfigMetaFile);
    }

    public function testEveryServiceInTheWatchedServicesListIsRequestedToTheClient()
    {
        $serviceManager = $this->getInitSubject();

        $this->assertCount(2, $serviceManager->getUpdatedServices());
    }

    public function testUpdatedDataIsDumped()
    {
        $this->assertFileNotExists($this->configFile);

        $serviceManager = $this->getInitSubject();
        $this->assertTrue($serviceManager->dumpSolved());

        $this->assertFileExists($this->configFile);
    }

    public function testDumperReturnsFalseIfItWasAProblem()
    {
        $this->assertFileNotExists($this->configFile);

        $mockedClient = $this->getMock('Client', array('getAll'), array(), '', false);
        $mockedClient->expects($this->once())->method('getAll')->will($this->throwException(new \Exception('ooops')));

        $serviceManager = $this->mockSubject(self::SERVER_URL, $this->configPath, self::$watchedServices, $mockedClient);

        $this->assertFalse($serviceManager->dumpSolved());
        $this->assertFileNotExists($this->configFile);
    }

    public function testGlobalConfigIsCreatedIfItDidNotExist()
    {
        $this->checkRefreshConfig();
    }

    public function testConfigIsUpdatedIfItDidNotIncludeSolvedServers()
    {
        $helper = new ConfigFileHelper($this->globalConfigFile, self::$filesystem);
        $helper->initConfigFiles($helper->getDefaultConfig());
        $oldFileTime = filemtime($this->globalConfigFile);

        sleep(1);

        $this->checkRefreshConfig();

        $this->assertFileExists($this->configFile);
        $this->assertLessThan(filemtime($this->globalConfigFile), $oldFileTime);
    }

    private function checkRefreshConfig()
    {
        $serviceManager = $this->mockSubject(
                self::SERVER_URL, $this->configPath, self::$watchedServices, $this->getMockedClient()
        );
        $serviceManager->refreshConfigWithSolvedServices();

        $this->assertFileExists($this->globalConfigFile);
        $this->assertFileExists($this->globalConfigMetaFile);
    }

    private function getInitSubject()
    {
        return $this->mockSubject(
                        self::SERVER_URL, $this->configFile, self::$watchedServices, $this->getMockedClient()
        );
    }

    private function getMockedClient()
    {
        $client = $this->getMock('Client', array('getAll'), array(), '', false);
        $client->expects($this->any())->method('getAll')->willReturn(true);
        return $client;
    }

    private function mockSubject($serverUrl, $configDir, $watchedServices, $mockedClient)
    {
        $subject = $this->getMock(
                '\\Kpacha\\Config\\AbstractServiceManager', array('buildClient'),
                array($serverUrl, $configDir, $watchedServices)
        );
        $subject->expects($this->once())->method('buildClient')->willReturn($mockedClient);
        return $subject;
    }

}
