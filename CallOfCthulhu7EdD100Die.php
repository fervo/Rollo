<?php

namespace Fervo\Rollo;

class CallOfCthulhu7EdD100Die implements DieInterface
{
    protected $tens;
    protected $units;

    protected $extraTens = [];
    protected $isPenalty = false;

    public function __construct($bonus = 0, $penalty = 0)
    {
        $this->tens = new SingleDie(10);
        $this->units = new SingleDie(10);

        if ($bonus > $penalty) {
            $bonusDice = $bonus - $penalty;

            for ($i=0; $i < $bonusDice; $i++) {
                $this->extraTens[] = new SingleDie(10);
            }
        } else {
            $penaltyDice = $penalty - $bonus;

            for ($i=0; $i < $penaltyDice; $i++) {
                $this->extraTens[] = new SingleDie(10);
                $this->isPenalty = true;
            }
        }
    }

    public function roll()
    {
        $this->tens->roll();
        $this->units->roll();

        foreach ($this->extraTens as $theDie) {
            $theDie->roll();
        }
    }

    public function getValue()
    {
        $tens = $this->getUsedTensDie();

        return $this->doGetDiceValue($tens, $this->units);
    }

    protected function doGetDiceValue(SingleDie $tens, SingleDie $units)
    {
        if ($tens->getValue() === null || $units->getValue() === null) {
            return null;
        }

        $value = $this->getTensValue($tens) + $this->getUnitsValue($units);
        if ($value == 0) {
            return 100;
        }

        return $value;
    }

    public function getValueDescription()
    {
        $total = $this->getValue();

        if ($total === null) {
            $total = '*';
        }

        $usedTens = $this->getUsedTensDie();
        $unusedTens = $this->getUnusedTensDice();

        $unusedDiceDescriptions = array_map(function($theDie) {
            return sprintf(
                "%s%s",
                ($this->isPenalty ? 'P' : 'B'),
                ($theDie->getValue() == null ? '*' : $this->getTensValue($theDie))
            );
        }, $unusedTens);

        $unused = implode(',', $unusedDiceDescriptions);
        if (strlen($unused) > 0) {
            $unused .= ',';
        }

        return sprintf(
            "D100:{%s%s+%s}=%s",
            $unused,
            $this->getTensValueDescription($usedTens),
            $this->getUnitsValueDescription($this->units),
            $total
        );
    }

    public function getExpression()
    {
        return str_pad('C100', 4+count($this->extraTens), ($this->isPenalty ? 'p' : 'b'));
    }

    protected function getUsedTensDie()
    {
        if ($this->tens->getValue() == null) {
            return $this->tens;
        }

        $currentDie = $this->tens;

        foreach ($this->extraTens as $theDie) {
            $currentValue = $this->doGetDiceValue($currentDie, $this->units);
            $newValue = $this->doGetDiceValue($theDie, $this->units);

            if ((!$this->isPenalty && $currentValue > $newValue) || ($this->isPenalty && $currentValue < $newValue)) {
                $currentDie = $theDie;
            }
        }

        return $currentDie;
    }

    protected function getUnusedTensDice()
    {
        $dice = array_merge([$this->tens], $this->extraTens);

        $usedTens = $this->getUsedTensDie();

        return array_filter($dice, function ($theDie) use ($usedTens) { return $theDie !== $usedTens; });
    }

    protected function getTensValue(SingleDie $tens)
    {
        return ($tens->getValue() - 1) * 10;
    }

    protected function getUnitsValue(SingleDie $units)
    {
        return $units->getValue() - 1;
    }

    protected function getTensValueDescription(SingleDie $tens)
    {
        if ($tens->getValue() == null) {
            return '*';
        }

        return $this->getTensValue($tens);
    }

    protected function getUnitsValueDescription(SingleDie $units)
    {
        if ($units->getValue() == null) {
            return '*';
        }

        return $this->getUnitsValue($units);
    }
}
