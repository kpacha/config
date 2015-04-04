<?php

namespace Kpacha\Config\Console;

use Symfony\Component\Console\Application as BaseApplication;
use Kpacha\Config\Command\UpdateServices;

class Application extends BaseApplication
{

    const APP_NAME = 'Config';
    const VERSION = '1.0.0';

    public function __construct()
    {
        parent::__construct(self::APP_NAME, self::VERSION);
    }

    protected function getDefaultCommands()
    {
        return array_merge(parent::getDefaultCommands(), array(new UpdateServices));
    }

}
