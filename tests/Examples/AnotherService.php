<?php

namespace DependencyInjector\Test\Examples;

class AnotherService
{
    public function __construct(SomeService $service)
    {
    }
}
