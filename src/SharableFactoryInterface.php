<?php

namespace DependencyInjector;

interface SharableFactoryInterface extends FactoryInterface
{
    public function share(bool $share = true);
    public function isShared(): bool;
}
