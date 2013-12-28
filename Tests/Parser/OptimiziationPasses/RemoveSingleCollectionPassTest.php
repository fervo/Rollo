<?php

namespace Fervo\Rollo\Tests\Parser\OptimiziationPasses;

use Fervo\Rollo\SingleDie;
use Fervo\Rollo\DieCollection;

use Fervo\Rollo\Parser\OptimizationPasses\RemoveSingleCollectionPass;

class RemoveSingleCollectionPassTest extends \PHPUnit_Framework_TestCase
{
    public function testRemove()
    {
        $coll = new DieCollection([
            new SingleDie(6),
        ]);

        $correctOptim = new SingleDie(6);

        $pass = new RemoveSingleCollectionPass();

        $optimized = $pass->run($coll);

        $this->assertEquals($correctOptim, $optimized);
    }
}
