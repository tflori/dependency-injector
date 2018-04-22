<?php

namespace DependencyInjector\Factory;

class StringArgument
{
    /** @var string */
    protected $string;

    /**
     * StringArgument constructor.
     *
     * @param string $string
     */
    public function __construct(string $string)
    {
        $this->string = $string;
    }

    public function getString()
    {
        return $this->string;
    }
}
