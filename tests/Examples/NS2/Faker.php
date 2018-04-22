<?php

namespace DependencyInjector\Test\Examples\NS2;

use DependencyInjector\Factory\AbstractFactory;

class Faker extends AbstractFactory
{
    /**
     * Build the product of this factory.
     *
     * @return mixed
     */
    protected function build()
    {
        return new \DOMDocument();
    }
}
