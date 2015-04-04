<?php

namespace Kpacha\Config\Dummies;

use Kpacha\Config\AbstractServiceManager;

class DummyServiceManager extends AbstractServiceManager
{

    protected function buildClient($serverUrl)
    {
        return $this;
    }

    public function getAll()
    {
        return array();
    }

}
