<?php

namespace Kpacha\Config\Loader;

use Symfony\Component\Config\Loader\FileLoader;

abstract class AbstractLoader extends FileLoader
{

    private $config;

    public function load($resource, $type = null)
    {
        $this->config = $this->parse($resource);
        return $this->config;
    }

    public function supports($resource, $type = null)
    {
        return is_string($resource) && $this->getSupportedType() === pathinfo(
                        $resource, PATHINFO_EXTENSION
        );
    }

    protected abstract function parse($resource);

    protected abstract function getSupportedType();
}
