<?php
namespace Kpacha\Config\Loader;

class YamlLoaderTest extends \PHPUnit_Framework_TestCase
{
    private $subject;
    
    public function setUp()
    {
        $fileLocator = $this->getMock('\\Symfony\\Component\\Config\\FileLocatorInterface');
        $this->subject = new YamlLoader($fileLocator);
    }

    public function testItSupportsYaml()
    {
        $this->assertTrue($this->subject->supports(__DIR__ . '/../fixtures/travis.yml'));
    }

    public function testItLoadsYaml()
    {
        $config = $this->subject->load(__DIR__ . '/../fixtures/travis.yml');
        $this->assertArrayHasKey('language', $config);
        $this->assertEquals('php', $config['language']);
    }
}
