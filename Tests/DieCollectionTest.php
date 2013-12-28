<?php

namespace Fervo\Rollo\Tests;

use Fervo\Rollo\SingleDie;
use Fervo\Rollo\ConstantDie;
use Fervo\Rollo\DieCollection;

/**
* 
*/
class DieCollectionTest extends \PHPUnit_Framework_TestCase
{
    public function testCanConstruct()
    {
        $twoD6 = new DieCollection();

        $this->assertInstanceOf('Fervo\Rollo\DieCollection', $twoD6);
    }

    public function testDieDoesntRollOnConstruct()
    {
        $twoD6 = new DieCollection([new SingleDie(6), new SingleDie(6)]);
        $this->assertNull($twoD6->getValue());
    }

    public function testCanBeRolled()
    {
        $twoD6 = new DieCollection([new SingleDie(6), new SingleDie(6)]);
        $twoD6->roll();

        $this->assertGreaterThanOrEqual(1, $twoD6->getValue());
        $this->assertLessThanOrEqual(12, $twoD6->getValue());
    }

    /**
     * @expectedException        \Fervo\Rollo\NotUniqueException
     */
    public function testCannotAddSameDieTwice()
    {
        $d6 = new SingleDie(6);
        $twoD6 = new DieCollection([$d6, $d6]);
    }

    public function testDieKeepsValueBetweenFetches()
    {
        $twoD1000 = new DieCollection([new SingleDie(1000), new SingleDie(1000)]);

        $firstValue = $twoD1000->getValue();

        $this->assertEquals($firstValue, $twoD1000->getValue());
    }

    public function testDieCanBeReRolled()
    {
        $twoD1000 = new DieCollection([new SingleDie(1000), new SingleDie(1000)]);

        $firstValue = $twoD1000->getValue();
        $twoD1000->roll();

        $this->assertNotEquals($firstValue, $twoD1000->getValue(), "This might fail randomly, it's very rare.");
    }

    public function testUnrolledDieCollectionGivesCorrectDescription()
    {
        $coll = new DieCollection([new SingleDie(6), new ConstantDie(3)]);

        $this->assertEquals('[D6:* + #3]=*', $coll->getValueDescription());
    }

    public function testDieCollectionGivesCorrectDescriptionForSubtraction()
    {
        $threeD6 = new DieCollection([new ConstantDie(1), new ConstantDie(2), new ConstantDie(3)], DieCollection::OPERATOR_SUBTRACTION);

        $this->assertEquals('[#1 - #2 - #3]=-4', $threeD6->getValueDescription());
    }

    public function testDieCollectionGivesCorrectDescription()
    {
        $threeD6 = new DieCollection([new ConstantDie(1), new ConstantDie(2), new ConstantDie(3)]);

        $this->assertEquals('[#1 + #2 + #3]=6', $threeD6->getValueDescription());
    }

    public function testComplextCollectionGivesCorrectDescriptionForSubtraction()
    {
        $threeD6 = new DieCollection([new ConstantDie(1), new ConstantDie(2), new ConstantDie(3)]);
        $threeD6MinusOne = new DieCollection([$threeD6, new ConstantDie(1)], DieCollection::OPERATOR_SUBTRACTION);

        $this->assertEquals('[[#1 + #2 + #3]=6 - #1]=5', $threeD6MinusOne->getValueDescription());
    }
}
