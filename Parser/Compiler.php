<?php
declare(strict_types=1);

namespace Fervo\Rollo\Parser;

use Fervo\Rollo\CallOfCthulhu7EdD100Die;
use Fervo\Rollo\ConstantDie;
use Fervo\Rollo\Parser\Node\NodeInterface;
use Fervo\Rollo\SingleDie;
use Fervo\Rollo\DieCollection;
use Fervo\Rollo\DieInterface;

class Compiler
{
    public function compile(NodeInterface $node): DieInterface
    {
        return $node->compile($this);
    }

    public function compileConstant(int $value): ConstantDie
    {
        return new ConstantDie($value);
    }

    public function compileMultiDie(int $num, int $sides): DieCollection
    {
        $dice = [];

        for ($i=0; $i < $num; $i++) {
            $dice[] = new SingleDie($sides);
        }

        return new DieCollection($dice);
    }

    public function compileCocDie(int $num, int $bonus, int $penalty): DieCollection
    {
        $dice = [];

        for ($i=0; $i < $num; $i++) {
            $dice[] = new CallOfCthulhu7EdD100Die($bonus, $penalty);
        }

        return new DieCollection($dice);
    }

    public function compileBinary(string $operator, DieInterface $left, DieInterface $right): DieCollection
    {
        $dice = [$left, $right];
        if ('+' === $operator) {
            $op = DieCollection::OPERATOR_ADDITION;
        } else {
            $op = DieCollection::OPERATOR_SUBTRACTION;
        }

        return new DieCollection($dice, $op);
    }
}
