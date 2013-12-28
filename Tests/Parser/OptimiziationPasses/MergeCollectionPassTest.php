<?php

namespace Fervo\Rollo\Tests\Parser\OptimiziationPasses;

use Fervo\Rollo\ConstantDie;
use Fervo\Rollo\SingleDie;
use Fervo\Rollo\DieCollection;

use Fervo\Rollo\Parser\OptimizationPasses\MergeCollectionPass;

class MergeCollectionPassTest extends \PHPUnit_Framework_TestCase
{
    public function testMerge()
    {
        $coll = new DieCollection([
            new DieCollection([
                new SingleDie(6),
                new SingleDie(6),
                new SingleDie(6),
            ]),
            new DieCollection([
                new SingleDie(4),
                new SingleDie(4),
            ], DieCollection::OPERATOR_SUBTRACTION),
            new ConstantDie(3),
            new DieCollection([
                new SingleDie(6),
                new SingleDie(6),
                new DieCollection([
                    new SingleDie(3),
                    new SingleDie(3),
                ]),
            ]),
        ]);

        $correctOptim = new DieCollection([
            new SingleDie(6),
            new SingleDie(6),
            new SingleDie(6),
            new DieCollection([
                new SingleDie(4),
                new SingleDie(4),
            ], DieCollection::OPERATOR_SUBTRACTION),
            new ConstantDie(3),
            new SingleDie(6),
            new SingleDie(6),
            new SingleDie(3),
            new SingleDie(3),
        ]);

        $pass = new MergeCollectionPass();

        $optimized = $pass->run($coll);

        $this->assertEquals($correctOptim, $optimized);
    }
}
