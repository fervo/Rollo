<?php

namespace Fervo\Rollo\Tests;

use Fervo\Rollo\ConstantDie;

/**
* 
*/
class ConstantDieTest extends \PHPUnit_Framework_TestCase
{
    public function testCanConstruct()
    {
        $two = new ConstantDie(2);

        $this->assertInstanceOf('Fervo\Rollo\ConstantDie', $two);
    }

    public function testSetsValueOnConstruct()
    {
        $two = new ConstantDie(2);

        $this->assertEquals(2, $two->getValue());
    }

    public function testDieKeepsValueBetweenFetches()
    {
        $three = new ConstantDie(3);

        $this->assertEquals(3, $three->getValue());
    }

    public function testDieCannotBeReRolled()
    {
        $three = new ConstantDie(3);

        $firstValue = $three->getValue();
        $three->roll();

        $this->assertEquals($firstValue, $three->getValue());
    }

    public function testConstantDieGivesAsteriskAndValueAsDescription()
    {
        $three = new ConstantDie(3);

        $this->assertEquals('#'.$three->getValue(), $three->getValueDescription());
    }
}
