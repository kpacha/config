<?php

namespace Kpacha\Config;

use Symfony\Component\Finder\Finder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\Config\Resource\FileResource;
use Symfony\Component\Config\Loader\FileLoader;
use Kpacha\Config\Loader\YamlLoader;

class ConfigurationParser
{

    const EXTENSION_YAML = 'yml';

    private $config = array();
    private $resources = array();

    public function getConfig()
    {
        return $this->config;
    }

    public function getResources()
    {
        return $this->resources;
    }

    public function load($configDir)
    {
        $this->config = array_merge($this->config, $this->getYamlConfig($configDir));
    }

    private function getYamlConfig($configDir)
    {
        $yamlLoader = new YamlLoader(new FileLocator($configDir));
        return $this->readConfig('yml', $yamlLoader, $configDir);
    }

    private function readConfig($type, FileLoader $fileLoader, $configDir)
    {
        $finder = new Finder();
        $finder->name('*.' . $type)->in($configDir);
        $config = array();
        foreach ($finder as $file) {
            $config[$file->getBasename('.' . $type)] = $fileLoader->load($file->getRealPath());
            $this->resources[] = new FileResource($file->getRealPath());
        }
        return $config;
    }

}
