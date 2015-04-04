<?php

namespace Kpacha\Config;

use Symfony\Component\Config\ConfigCache;

class ConfigurationDumper
{
    private $configCache;
    
    public function __construct(ConfigCache $configCache)
    {
        $this->configCache = $configCache;
    }

    public function dump(array $config, $meta = array())
    {   
        $this->configCache->write($this->addTemplate($config), $meta);
    }
    
    private function addTemplate(array $config)
    {
        $exportedConfig = var_export($config, true);
        $template = <<<TXT
<?php

\$config = $exportedConfig;
TXT;
        return $template;
    }
}
