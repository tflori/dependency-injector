<?php

namespace DependencyInjector\Test\Examples\NS2;

use DependencyInjector\AbstractFactory;

class Faker extends AbstractFactory
{
    /**
     * Build the product of this factory.
     *
     * @return mixed
     */
    public function build()
    {
        return new \DOMDocument();
    }
}
