<?php

namespace DependencyInjector\Test\Examples;

class AnotherService
{
    public $args;

    public function __construct(SomeService $service, ...$args)
    {
        $this->args = $args;
    }
}
