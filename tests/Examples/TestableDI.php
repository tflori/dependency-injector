<?php

namespace DependencyInjector\Test\Examples;

use DependencyInjector\DI;

class TestableDI extends DI
{
    public static function getContainer()
    {
        return parent::getContainer();
    }
}
