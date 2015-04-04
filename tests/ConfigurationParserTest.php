<?php

namespace Kpacha\Config;

class ConfigurationParserTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $this->configDir = __DIR__ . '/fixtures/';
    }
    
    public function testYamlConfigurationFilesAreRead()
    {
        $yamlConfig = $this->getInitParser()->getConfig();
        
        $this->assertArrayHasKey('travis', $yamlConfig);
        $this->assertArrayHasKey('test', $yamlConfig);
        $this->assertArrayHasKey('language', $yamlConfig['travis']);
    }
    
    public function testYamlConfigurationFilesAddedToResourceList()
    {
        $this->assertCount(3, $this->getInitParser()->getResources());
    }
    
    private function getInitParser()
    {
        $configParser = new ConfigurationParser;
        $configParser->load($this->configDir);
        return $configParser;
    }
}
