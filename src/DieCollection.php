<?php
declare(strict_types=1);

namespace Fervo\Rollo;

use Doctrine\Common\Collections\ArrayCollection;
use RuntimeException;

/**
* 
*/
class DieCollection implements DieInterface
{
    public const OPERATOR_ADDITION = '+';
    public const OPERATOR_SUBTRACTION = '-';

    protected $operator;
    protected ArrayCollection $dice;

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

    public function getDice(): array
    {
        return $this->dice->toArray();
    }

    public function replaceDieWithDice(DieInterface $oldDie, array $dice): void
    {
        // @todo optimize
        $index = $this->dice->indexOf($oldDie);

        if (false === $index) {
            throw new RuntimeException("Old die to be replaced doesn't exist in collection");
        }

        if (0 === $index) {
            $left = [];
        } else {
            $left = $this->dice->slice(0, $index);
        }

        $right = $this->dice->slice($index + 1);

        $this->dice = new ArrayCollection($left);
        $this->addDice($dice);
        $this->addDice($right);
    }

    public function addDice(array $dice): void
    {
        foreach ($dice as $theDie) {
            $this->addDie($theDie);
        }
    }

    public function addDie(DieInterface $theDie): void
    {
        if (false !== $this->dice->indexOf($theDie)) {
            throw new NotUniqueException($theDie);
        }

        $this->dice[] = $theDie;
    }

    public function roll(): void
    {
        foreach ($this->dice as $theDie) {
            $theDie->roll();
        }
    }

    public function getValue(): ?int
    {
        // @todo optimize
        foreach ($this->dice as $theDie) {
            if (null === $theDie->getValue()) {
                return null;
            }
        }

        $total = $this->dice->first()->getValue();

        foreach ($this->dice->slice(1) as $theDie) {
            if ($this->operator === self::OPERATOR_ADDITION) {
                $total += $theDie->getValue();
            } else {
                $total -= $theDie->getValue();
            }
        }

        return $total;
    }

    public function getValueDescription(): string
    {
        // @todo optimize
        $diceDescriptions = $this->dice->map(function($theDie) { return $theDie->getValueDescription(); });

        $description = '[';
        $description .= implode(' '.$this->operator.' ', $diceDescriptions->getValues());
        $description .= '] = '.($this->getValue() ?? '*');

        return $description;
    }

    public function getExpression(): string
    {
        return '('.implode(' '.$this->operator.' ', $this->getSubExpressions($this->dice)).')';
    }

    /** @noinspection ForeachInvariantsInspection */
    protected function getSubExpressions($expressions): array
    {
        $newExpressions = [];
        for ($i=0, $iMax = \count($expressions); $i < $iMax; $i++) {
            $current = $expressions[$i];

            if ($current instanceOf SingleDie) {
                $counter = 1;

                for ($j=$i+1, $jMax = \count($expressions); $j < $jMax; $j++) {
                    $inner = $expressions[$j];
                    if ($inner instanceOf SingleDie) {
                        if ($current->getExpression() === $inner->getExpression()) {
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
