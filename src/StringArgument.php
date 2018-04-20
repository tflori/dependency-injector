<?php

namespace DependencyInjector;

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

    public function __toString()
    {
        return $this->getString();
    }

    public function getString()
    {
        return $this->string;
    }
}
