<?php

namespace Kpacha\Config;

class ServiceManagerBuilder
{

    public function build(Configuration $config)
    {
        $serviceConfig = $config->get(Configuration::SERVICE);
        $serviceClass = $this->getServiceClass($serviceConfig);
        $serverUrl = $this->extractParameter(AbstractServiceManager::SERVER_URL, $serviceConfig);
        $watchedServices = $this->getWatchedServices($serviceConfig);
        return new $serviceClass($serverUrl, $config->getConfigDir(), $watchedServices);
    }

    private function getServiceClass($serviceConfig)
    {
        $serviceClass = $this->extractParameter(AbstractServiceManager::SERVICE_MANAGER, $serviceConfig);
        if (!class_exists($serviceClass)) {
            throw new \Exception("The '$serviceClass' service manager is not defined");
        }
        return $serviceClass;
    }

    private function getWatchedServices($serviceConfig)
    {
        return ($this->extractParameter(AbstractServiceManager::WATCHED_SERVICES, $serviceConfig, false))? : array();
    }

    private function extractParameter($name, $config, $required = true)
    {
        if (isset($config[$name])) {
            return $config[$name];
        }
        if ($required) {
            throw new \Exception("The '$name' config key is not set");
        }
        return null;
    }

}
