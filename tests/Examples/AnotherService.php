<?php

namespace DependencyInjector\Test\Examples;

class AnotherService
{
    public $args;

    /** @var array */
    public $calls = [];

    public function __construct(SomeService $service, ...$args)
    {
        $this->args = $args;
    }

    public function __call($method, $args)
    {
        if (!isset($this->calls[$method])) {
            $this->calls[$method] = ['calls' => 0, 'args' => []];
        }

        $this->calls[$method]['calls']++;
        $this->calls[$method]['args'][] = $args;
    }
}
