<?php

namespace DependencyInjector\Test\Examples\NS1;

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
        return simplexml_load_string('<html></html>');
    }
}
