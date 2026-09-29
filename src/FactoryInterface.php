<?php

namespace DependencyInjector;

interface FactoryInterface
{
    /**
     * Build the product of this factory and return an instance.
     *
     * Sharing has to be handled here.
     *
     * @return mixed
     */
    public function getInstance();
}
