<?php

namespace DependencyInjector;

interface PatternFactoryInterface extends FactoryInterface
{
    public function matches(string $name): bool;
    public function getInstance(string $name = null);
}
