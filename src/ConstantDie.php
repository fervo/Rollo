<?php
declare(strict_types=1);

namespace Fervo\Rollo;

class ConstantDie implements DieInterface
{
    protected int $value;

    public function __construct(int $value)
    {
        $this->value = $value;
    }

    public function roll(): void
    {
    }

    public function getValue(): ?int
    {
        return $this->value;
    }

    public function getValueDescription(): string
    {
        return '#'.$this->value;
    }

    public function getExpression(): string
    {
        return (string)$this->value;
    }
}
