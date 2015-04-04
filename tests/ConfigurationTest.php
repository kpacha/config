<?php

namespace Kpacha\Config;

use Symfony\Component\Filesystem\Filesystem;

class ConfigurationTest extends \PHPUnit_Framework_TestCase
{

    private $filesystem;
    private $configFile;
    private $helper;

    public function setUp()
    {
        $this->configFile = __DIR__ . '/fixtures/' . Configuration::CACHE_FILE;
        $this->filesystem = new Filesystem();
        $this->helper = new ConfigFileHelper($this->configFile, $this->filesystem);
    }

    public function tearDown()
    {
        $this->cleanConfigFiles();
    }

    public function testCacheConfigFileIsCreatedWhenItDoesNotExist()
    {
        $this->cleanConfigFiles();

        $configuration = new Configuration(dirname($this->configFile), true);

        $this->assertArrayHasKey(Configuration::DEFAULT_SERVICE_KEY,
                $configuration->get(Configuration::SERVICE));
    }

    public function testCacheConfigFileIsNotCreatedWhenItIsFresh()
    {
        $this->initConfigFiles($this->getDefaultConfig());

        $configuration = new Configuration(dirname($this->configFile), true);

        $this->assertArrayHasKey(Configuration::DEFAULT_SERVICE_KEY,
                $configuration->get(Configuration::SERVICE));
    }

    public function testDoNotOverrideSolvedServers()
    {
        $config = array_merge(
                $this->getDefaultConfig(),
                array(
            Configuration::SOLVED_SERVICES => array('solved' => array(true))
                )
        );
        $this->initConfigFiles($config);

        $configuration = new Configuration(dirname($this->configFile), true);

        $this->assertArrayHasKey('solved', $configuration->get(Configuration::SOLVED_SERVICES));
    }

    /**
     * @expectedException \Exception
     * @expectedExceptionMessage The key LOREM does not exist
     */
    public function testItTrhowsAnExceptionIfUnknownKey()
    {
        $this->initConfigFiles($this->getDefaultConfig());

        $configuration = new Configuration(dirname($this->configFile), true);

        $configuration->get('LOREM');
    }

    /**
     * @expectedException \Exception
     * @expectedExceptionMessage services configuration not found inf config dir
     */
    public function testItTrhowsAnExceptionIfServicesAreNotDefined()
    {
        $this->initConfigFiles(array());

        new Configuration(dirname($this->configFile), true);
    }

    private function getDefaultConfig()
    {
        return $this->helper->getDefaultConfig();
    }

    private function initConfigFiles($config)
    {
        $this->helper->initConfigFiles($config);
    }

    private function cleanConfigFiles()
    {
        $this->helper->cleanConfigFiles();
    }

}
