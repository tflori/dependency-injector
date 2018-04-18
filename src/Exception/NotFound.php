<?php

namespace DependencyInjector\Exception;

use DependencyInjector\Exception;
use Psr\Container\NotFoundExceptionInterface;

class NotFound extends Exception implements NotFoundExceptionInterface
{
}
