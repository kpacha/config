<?php

namespace Kpacha\Config\Loader;

use Symfony\Component\Yaml\Yaml;

class YamlLoader extends AbstractLoader
{

    const SUPPORTED_TYPE = 'yml';

    protected function parse($resource)
    {
        return Yaml::parse($resource);
    }

    protected function getSupportedType()
    {
        return self::SUPPORTED_TYPE;
    }

}
