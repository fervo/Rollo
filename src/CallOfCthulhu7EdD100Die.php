<?php
declare(strict_types=1);

namespace Fervo\Rollo;

class CallOfCthulhu7EdD100Die implements DieInterface
{
    protected SingleDie $tens;
    protected SingleDie $units;

    protected array $extraTens = [];
    protected bool $isPenalty = false;

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

    public function roll(): void
    {
        $this->tens->roll();
        $this->units->roll();

        foreach ($this->extraTens as $theDie) {
            $theDie->roll();
        }
    }

    public function getValue(): ?int
    {
        $tens = $this->getUsedTensDie();

        return $this->doGetDiceValue($tens, $this->units);
    }

    protected function doGetDiceValue(SingleDie $tens, SingleDie $units)
    {
        if (null === $tens->getValue() || null === $units->getValue()) {
            return null;
        }

        $value = $this->getTensValue($tens) + $this->getUnitsValue($units);
        if (0 === $value) {
            return 100;
        }

        return $value;
    }

    public function getValueDescription(): string
    {
        $total = $this->getValue();

        if (null === $total) {
            $total = '*';
        }

        $usedTens = $this->getUsedTensDie();
        $unusedTens = $this->getUnusedTensDice();

        $unusedDiceDescriptions = array_map(function($theDie) {
            return sprintf(
                "%s%s",
                ($this->isPenalty ? 'P' : 'B'),
                (null === $theDie->getValue() ? '*' : $this->getTensValue($theDie))
            );
        }, $unusedTens);

        $unused = implode(',', $unusedDiceDescriptions);
        if ('' !== $unused) {
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

    public function getExpression(): string
    {
        return str_pad('C100', 4+ \count($this->extraTens), ($this->isPenalty ? 'p' : 'b'));
    }

    protected function getUsedTensDie()
    {
        if (null === $this->tens->getValue()) {
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

        return array_filter($dice, static function ($theDie) use ($usedTens) { return $theDie !== $usedTens; });
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
        if (null === $tens->getValue()) {
            return '*';
        }

        return $this->getTensValue($tens);
    }

    protected function getUnitsValueDescription(SingleDie $units)
    {
        if (null === $units->getValue()) {
            return '*';
        }

        return $this->getUnitsValue($units);
    }
}
