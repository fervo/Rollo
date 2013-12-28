<?php

namespace Fervo\Rollo;

class SingleDie implements DieInterface
{
    protected $sides;
    protected $result = null;

    public function __construct($sides)
    {
        $this->sides = $sides;
    }

    public function roll()
    {
        $this->result = mt_rand(1, $this->sides);
    }

    public function getValue()
    {
        return $this->result;
    }

    public function getValueDescription()
    {
        if ($this->result) {
            return sprintf('D%d:%d', $this->sides, $this->result);
        }

        return sprintf('D%d:*', $this->sides);
    }
}
