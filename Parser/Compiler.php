<?php

namespace Fervo\Rollo\Parser;

use Fervo\Rollo\CallOfCthulhu7EdD100Die;
use Fervo\Rollo\ConstantDie;
use Fervo\Rollo\SingleDie;
use Fervo\Rollo\DieCollection;
use Fervo\Rollo\DieInterface;

class Compiler
{
    public function compile(Node\Node $node)
    {
        return $node->compile($this);
    }

    public function compileConstant($value)
    {
        return new ConstantDie($value);
    }

    public function compileMultiDie($num, $sides)
    {
        $dice = [];

        for ($i=0; $i < $num; $i++) {
            $dice[] = new SingleDie($sides);
        }

        return new DieCollection($dice);
    }

    public function compileCocDie($num, $bonus, $penalty)
    {
        $dice = [];

        for ($i=0; $i < $num; $i++) {
            $dice[] = new CallOfCthulhu7EdD100Die($bonus, $penalty);
        }

        return new DieCollection($dice);
    }

    public function compileBinary($operator, DieInterface $left, DieInterface $right)
    {
        $dice = [$left, $right];
        if ($operator == '+') {
            $op = DieCollection::OPERATOR_ADDITION;
        } else {
            $op = DieCollection::OPERATOR_SUBTRACTION;
        }

        return new DieCollection($dice, $op);
    }
}
