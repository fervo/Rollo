<?php
declare(strict_types=1);

namespace Fervo\Rollo;


use RuntimeException;

class Roll
{
    private static array $allowedOperators = [
        null,
        '=',
        '!=',
        '<',
        '>',
        '>=',
        '<=',
    ];

    private DieInterface $die;
    private ?string $operator;
    private ?int $threshold;
    private bool $deltaGreenMode = false;

    public function __construct(DieInterface $die, ?string $operator = null, ?int $threshold = null)
    {
        $this->die = $die;

        if (!\in_array($operator, self::$allowedOperators, true)) {
            throw new RuntimeException($operator." is not an allowed operator.");
        }

        $this->operator = $operator;
        $this->threshold = $threshold;
    }

    public function setDeltaGreenMode(bool $deltaGreenMode): void
    {
        $this->deltaGreenMode = $deltaGreenMode;
    }

    public function roll(): void
    {
        $this->die->roll();
    }

    public function getResult(): int|bool|null
    {
        $value = $this->die->getValue();

        if (null === $this->threshold) {
            return $value;
        }

        if (null === $value) {
            return null;
        }

        switch ($this->operator) {
            case null:
                return $value;
            case '=':
                return $value === $this->threshold;
            case '!=':
                return $value !== $this->threshold;
            case '>':
                return $value > $this->threshold;
            case '<':
                return $value < $this->threshold;
            case '>=':
                return $value >= $this->threshold;
            case '<=':
                return $value <= $this->threshold;
            default:
                return null;
        }
    }

    public function getResultDescription(): string
    {
        if (null === $this->operator || null === $this->threshold || null === $this->die->getValue()) {
            return $this->die->getValueDescription();
        }

        if ($this->die instanceof CallOfCthulhu7EdD100Die && '<=' === $this->operator) {
            return sprintf(
                "%s %s %d -- %s",
                $this->die->getValueDescription(),
                $this->operator,
                $this->threshold,
                $this->formatCoc7thResult()
            );
        }

        return sprintf(
            "%s %s %d -- %s",
            $this->die->getValueDescription(),
            $this->operator,
            $this->threshold,
            $this->formatResult()
        );
    }

    private function formatResult(): string
    {
        if ($this->deltaGreenMode && $this->die instanceof SingleDie && 100 === $this->die->getSides()) {
            return $this->formatDeltaGreenResult();
        }

        return true === $this->getResult() ? 'Success' : 'Failure';
    }

    private function formatCoc7thResult(): string
    {
        $value = $this->die->getValue();

        switch (true) {
            case 1 === $value:
                return 'Critical success';
            case $value <= floor($this->threshold / 5):
                return 'Extreme success';
            case $value <= floor($this->threshold / 2):
                return 'Hard success';
            case $value <= $this->threshold:
                return 'Regular success';
            case $this->threshold <= 50 && $value >= 96:
            case 100 === $value:
                return 'Fumble';
            default:
                return 'Failure';
        }
    }

    private function formatDeltaGreenResult(): string
    {
        $value = $this->die->getValue();

        if (\in_array($value, [1, 11, 22, 33, 44, 55, 66, 77, 88, 99, 100], true)) {
            return true === $this->getResult() ? 'Critical success' : 'Critical failure';
        }

        return true === $this->getResult() ? 'Success' : 'Failure';
    }
}
