<?php

namespace Kpacha\Config;

use Symfony\Component\Config\ConfigCache;

class Configuration
{

    const CACHE_FILE = 'config_file.php';
    const SERVICE = 'services';
    const SOLVED_SERVICES = 'services_solved';
    const DEFAULT_SERVICE_KEY = 'default';

    private $configCache;
    private $config;
    private $configDir;

    public function __construct($configDir, $debug)
    {
        $this->configDir = $configDir;
        $this->configCache = new ConfigCache($configDir . '/' . self::CACHE_FILE, $debug);

        if (!$this->configCache->isFresh()) {
            $this->loadFreshConfig($configDir);
        }

        require $this->configCache;
        $this->config = $config;
        $this->initServices();
    }
    
    public function getConfigDir()
    {
        return $this->configDir;
    }

    public function get($key)
    {
        if (!isset($this->config[$key])) {
            throw new \Exception("The key $key does not exist");
        }
        return $this->config[$key];
    }

    private function loadFreshConfig($configDir)
    {
        $parser = $this->getParser($configDir);
        $dumper = new ConfigurationDumper($this->configCache);
        $dumper->dump($parser->getConfig(), $parser->getResources());
    }

    private function getParser($configDir)
    {
        $parser = new ConfigurationParser;
        $parser->load($configDir);
        return $parser;
    }

    private function initServices()
    {
        if(!isset($this->config[self::SOLVED_SERVICES])){
            $this->config[self::SOLVED_SERVICES] = $this->getDefaultServices();
        }
    }
    
    private function getDefaultServices()
    {
        if(!isset($this->config[self::SERVICE]) || !isset($this->config[self::SERVICE][self::DEFAULT_SERVICE_KEY])){
            throw new \Exception(self::SERVICE . ' configuration not found inf config dir');
        }
        return $this->config[self::SERVICE][self::DEFAULT_SERVICE_KEY];
    }

}
