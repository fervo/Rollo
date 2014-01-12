<?php

namespace Fervo\Rollo;

class ConstantDie implements DieInterface
{
    protected $value;

    public function __construct($value)
    {
        $this->value = $value;
    }

    public function roll()
    {
    }

    public function getValue()
    {
        return $this->value;
    }

    public function getValueDescription()
    {
        return '#'.$this->value;
    }

    public function getExpression()
    {
        return $this->value;
    }
}
