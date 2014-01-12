<?php

namespace Fervo\Rollo;

use Doctrine\Common\Collections\ArrayCollection;

/**
* 
*/
class DieCollection implements DieInterface
{
    const OPERATOR_ADDITION = '+';
    const OPERATOR_SUBTRACTION = '-';

    protected $operator;
    protected $dice;

    public function __construct(array $dice = [], $operator = self::OPERATOR_ADDITION)
    {
        $this->operator = $operator;
        $this->dice = new ArrayCollection();
        $this->addDice($dice);
    }

    public function getOperator()
    {
        return $this->operator;
    }

    public function getDice()
    {
        return $this->dice->toArray();
    }

    public function replaceDieWithDice(DieInterface $oldDie, array $dice)
    {
        // @todo optimize
        $index = $this->dice->indexOf($oldDie);

        if ($index === false) {
            throw new \RuntimeException("Old die to be replaced doesn't exist in collection");
        }

        if ($index === 0) {
            $left = [];
        } else {
            $left = $this->dice->slice(0, $index);
        }

        $right = $this->dice->slice($index + 1);

        $this->dice = new ArrayCollection($left);
        $this->addDice($dice);
        $this->addDice($right);
    }

    public function addDice(array $dice)
    {
        foreach ($dice as $theDie) {
            $this->addDie($theDie);
        }
    }

    public function addDie(DieInterface $theDie)
    {
        if ($this->dice->indexOf($theDie) !== false) {
            throw new NotUniqueException($theDie);
        }

        $this->dice[] = $theDie;
    }

    public function roll()
    {
        foreach ($this->dice as $theDie) {
            $theDie->roll();
        }
    }

    public function getValue()
    {
        // @todo optimize
        foreach ($this->dice as $theDie) {
            if ($theDie->getValue() === null) {
                return null;
            }
        }

        $total = $this->dice->first()->getValue();

        foreach ($this->dice->slice(1) as $theDie) {
            if ($this->operator == self::OPERATOR_ADDITION) {
                $total += $theDie->getValue();
            } else {
                $total -= $theDie->getValue();
            }
        }

        return $total;
    }

    public function getValueDescription()
    {
        // @todo optimize
        $diceDescriptions = $this->dice->map(function($theDie) { return $theDie->getValueDescription(); });

        $description = '[';
        $description .= implode(' '.$this->operator.' ', $diceDescriptions->getValues());
        $description .= ']='.($this->getValue() === null ? '*' : $this->getValue());

        return $description;
    }

    public function getExpression()
    {
        return '('.implode(' '.$this->operator.' ', $this->getSubExpressions($this->dice)).')';
    }

    protected function getSubExpressions($expressions)
    {
        $newExpressions = [];
        for ($i=0; $i < count($expressions); $i++) {
            $current = $expressions[$i];

            if ($current instanceOf SingleDie) {
                $counter = 1;

                for ($j=$i+1; $j < count($expressions); $j++) {
                    $inner = $expressions[$j];
                    if ($inner instanceOf SingleDie) {
                        if ($current->getExpression() == $inner->getExpression()) {
                            $i++;
                            $counter++;
                        }
                    } else {
                        break;
                    }
                }

                if ($counter > 1) {
                    $newExpressions[] = $counter.$current->getExpression();
                } else {
                    $newExpressions[] = $current->getExpression();
                }
            } else {
                $newExpressions[] = $current->getExpression();
            }
        }

        return $newExpressions;
    }
}
