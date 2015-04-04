<?php

namespace Kpacha\Config;

use Symfony\Component\Config\ConfigCache;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Yaml\Yaml;

abstract class AbstractServiceManager
{

    const SERVER_URL = 'server';
    const SERVICE_MANAGER = 'service-manager';
    const WATCHED_SERVICES = 'service-names';

    private $serverUrl;
    private $configDir;
    private $watchedServices;
    private $client;

    public function __construct($serverUrl, $configDir, $watchedServices = array())
    {
        $this->configDir = $configDir;
        $this->serverUrl = $serverUrl;
        $this->watchedServices = $watchedServices;
    }

    public function getWatchedServices()
    {
        return $this->watchedServices;
    }

    public function getUpdatedServices()
    {
        $services = array();
        $client = $this->getClient();
        foreach ($this->watchedServices as $service) {
            $services[$service] = $client->getAll($service);
        }
        return $services;
    }

    public function dumpSolved()
    {
        try {
            $this->dump($this->getUpdatedServices());
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    public function refreshConfigWithSolvedServices()
    {
        if ($this->dumpSolved()) {
            $this->refreshConfig();
        }
    }

    protected function dump($data)
    {
        $configCache = new ConfigCache($this->getSolvedServicesFileName(), true);
        $configCache->write(Yaml::dump($data));
    }

    protected function refreshConfig()
    {
        if ($this->hasToCleanCache()) {
            $this->cleanCache();
        }
        new Configuration($this->getConfigDir(), true);
    }

    protected function hasToCleanCache()
    {
        return $this->isCacheConfigFilePresent() &&
                (!$this->isMetaCacheConfigFilePresent() || !$this->isSolvedServicesFileTracked());
    }

    private function isCacheConfigFilePresent()
    {
        return is_file($this->getCacheConfigFileName());
    }

    private function isMetaCacheConfigFilePresent()
    {
        return is_file($this->getMetaCacheConfigFileName());
    }

    protected function cleanCache()
    {
        $filesystem = new Filesystem();
        $filesystem->remove($this->getCacheConfigFileName());
    }

    protected function getClient()
    {
        if (!$this->client) {
            $this->client = $this->buildClient($this->serverUrl);
        }
        return $this->client;
    }
    
    abstract protected function buildClient($serverUrl);

    protected function isSolvedServicesFileTracked()
    {
        $solvedServicesFileName = $this->getSolvedServicesFileName();
        $meta = $this->getStoredMetadata();
        foreach ($meta as $resource) {
            if ($resource === $solvedServicesFileName) {
                return true;
            }
        }

        return false;
    }

    protected function getStoredMetadata()
    {
        return unserialize(file_get_contents($this->getMetaCacheConfigFileName()));
    }

    protected function getConfigDir()
    {
        return $this->configDir;
    }

    protected function getCacheConfigFileName()
    {
        return $this->getConfigDir() . '/' . Configuration::CACHE_FILE;
    }

    protected function getMetaCacheConfigFileName()
    {
        return $this->getCacheConfigFileName() . '.meta';
    }

    protected function getSolvedServicesFileName()
    {
        return $this->getConfigDir() . '/' . Configuration::SOLVED_SERVICES . '.yml';
    }

}
