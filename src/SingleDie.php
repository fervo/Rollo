<?php
declare(strict_types=1);

namespace Fervo\Rollo;

class SingleDie implements DieInterface
{
    protected int $sides;
    protected ?int $result = null;

    public function __construct(int $sides)
    {
        if ($sides < 2) {
            throw new \RuntimeException("Cannot have die with less than two sides");
        }

        $this->sides = $sides;
    }

    public function roll(): void
    {
        /** @noinspection PhpUnhandledExceptionInspection */
        $this->result = random_int(1, $this->sides);
    }

    public function getSides(): int
    {
        return $this->sides;
    }

    public function getValue(): ?int
    {
        return $this->result;
    }

    public function getValueDescription(): string
    {
        if ($this->result) {
            return sprintf('D%d: %d', $this->sides, $this->result);
        }

        return sprintf('D%d: *', $this->sides);
    }

    public function getExpression(): string
    {
        return 'D'.$this->sides;
    }
}
