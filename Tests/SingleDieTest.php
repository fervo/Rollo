<?php

namespace Fervo\Rollo\Tests;

use Fervo\Rollo\SingleDie;

/**
* 
*/
class DieTest extends \PHPUnit_Framework_TestCase
{
    public function testCanConstruct()
    {
        $sixDie = new SingleDie(6);

        $this->assertInstanceOf('Fervo\Rollo\SingleDie', $sixDie);
    }

    public function testDieDoesntRollOnConstruct()
    {
        $tenDie = new SingleDie(10);

        $this->assertNull($tenDie->getValue());
    }

    public function testDieCanBeRolled()
    {
        $tenDie = new SingleDie(10);
        $tenDie->roll();

        $this->assertGreaterThanOrEqual(1, $tenDie->getValue());
        $this->assertLessThanOrEqual(10, $tenDie->getValue());
    }

    public function testDieKeepsValueBetweenFetches()
    {
        $tenThousandDie = new SingleDie(10000);

        $firstValue = $tenThousandDie->getValue();

        $this->assertEquals($firstValue, $tenThousandDie->getValue());
    }

    public function testDieCanBeReRolled()
    {
        $tenThousandDie = new SingleDie(10000);

        $firstValue = $tenThousandDie->getValue();
        $tenThousandDie->roll();

        $this->assertNotEquals($firstValue, $tenThousandDie->getValue(), "This might fail randomly, it's one in ten thousand.");
    }

    public function testSingleDieGivesDieAndValueAsDescription()
    {
        $tenDie = new SingleDie(10);
        $tenDie->roll();

        $this->assertEquals('D10:'.$tenDie->getValue(), $tenDie->getValueDescription());
    }

    public function testUnrolledSingleDieGivesDieAndAsteriskAsDescription($value='')
    {
        $tenDie = new SingleDie(10);

        $this->assertEquals('D10:*', $tenDie->getValueDescription());
    }

    public function testIsSufficientlyRandom()
    {
        $allowedVariance = 0.1;
        $iterations = 10000;

        $buckets = [
            1 => 0,
            2 => 0,
            3 => 0,
            4 => 0,
            5 => 0,
            6 => 0
        ];

        $d6 = new SingleDie(6);

        for ($i=0; $i < $iterations; $i++) {
            $d6->roll();
            $buckets[$d6->getValue()]++;
        }

        $average = array_sum($buckets)/6;

        foreach ($buckets as $key => $value) {
            $this->assertGreaterThanOrEqual($average*(1-$allowedVariance), $value);
            $this->assertLessThanOrEqual($average*(1+$allowedVariance), $value);
        }
    }
}
