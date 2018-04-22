<?php

namespace DependencyInjector\Test\Examples\NS1;

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
        return simplexml_load_string('<html></html>');
    }
}
