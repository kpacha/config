<?php

namespace Kpacha\Config;

use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Config\ConfigCache;

class ConfigurationDumperTest extends \PHPUnit_Framework_TestCase
{

    const TEST_FILE = '/fixtures/config.php.test';

    private $filesystem;
    private $configFile;

    public function setUp()
    {
        $this->configFile = __DIR__ . self::TEST_FILE;
        $this->filesystem = new Filesystem();
        $this->filesystem->touch($this->configFile);
    }
    
    public function tearDown()
    {
        $this->filesystem->remove($this->configFile);
        $this->filesystem->remove($this->configFile . '.meta');
    }

    public function testConfigurationFileIsWrite()
    {
        $originalConfig = array(
            'a' => array(
                0 => 'b',
                array('c', 'd', 1),
                123456
            ),
            'b' => true
        );

        $configDumper = new ConfigurationDumper(new ConfigCache($this->configFile, true));
        $configDumper->dump($originalConfig);

        require $this->configFile;

        $this->assertEquals($originalConfig, $config);
    }

}
