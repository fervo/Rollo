<?php

namespace Fervo\Rollo\Tests;

use Fervo\Rollo\CallOfCthulhu7EdD100Die;

/**
* 
*/
class CallOfCthulhu7EdD100DieTest extends \PHPUnit_Framework_TestCase
{
    public function testCanConstruct()
    {
        $d100 = new CallOfCthulhu7EdD100Die();

        $this->assertInstanceOf('Fervo\Rollo\CallOfCthulhu7EdD100Die', $d100);
    }

    public function testDieDoesntRollOnConstruct()
    {
        $d100 = new CallOfCthulhu7EdD100Die();

        $this->assertNull($d100->getValue());
    }

    public function testDieCanBeRolled()
    {
        $d100 = new CallOfCthulhu7EdD100Die();
        $d100->roll();

        $this->assertGreaterThanOrEqual(1, $d100->getValue());
        $this->assertLessThanOrEqual(100, $d100->getValue());
    }

    public function testDieKeepsValueBetweenFetches()
    {
        $d100 = new CallOfCthulhu7EdD100Die();

        $firstValue = $d100->getValue();

        $this->assertEquals($firstValue, $d100->getValue());
    }

    public function testDieCanBeReRolled()
    {
        $d100 = new CallOfCthulhu7EdD100Die();

        $firstValue = $d100->getValue();
        $d100->roll();

        $this->assertNotEquals($firstValue, $d100->getValue(), "This might fail randomly, it's one in 100.");
    }

    public function testRolledDiceGivesCorrectDescription()
    {
        $d100 = new CallOfCthulhu7EdD100Die();
        $d100->roll();

        $this->assertRegExp('/D100\:\{[0-9]+\+[0-9]\}=[0-9]+/', $d100->getValueDescription());

        $d100bb = new CallOfCthulhu7EdD100Die(2);
        $d100bb->roll();

        $this->assertRegExp('/D100\:\{B[0-9]+,B[0-9]+,[0-9]+\+[0-9]\}=[0-9]+/', $d100bb->getValueDescription());

        $d100p = new CallOfCthulhu7EdD100Die(0, 1);
        $d100p->roll();

        $this->assertRegExp('/D100\:\{P[0-9]+,[0-9]+\+[0-9]\}=[0-9]+/', $d100p->getValueDescription());
    }

    public function testUnrolledDieGivesCorrectDescription()
    {
        $d100 = new CallOfCthulhu7EdD100Die();

        $this->assertEquals('D100:{*+*}=*', $d100->getValueDescription());

        $d100bb = new CallOfCthulhu7EdD100Die(2);

        $this->assertEquals('D100:{B*,B*,*+*}=*', $d100bb->getValueDescription());
    }
}
