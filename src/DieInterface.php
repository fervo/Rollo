<?php
declare(strict_types=1);

namespace Fervo\Rollo;

interface DieInterface
{
    public function roll(): void;

    public function getValue(): ?int;

    public function getValueDescription(): string;

    public function getExpression(): string;
}
