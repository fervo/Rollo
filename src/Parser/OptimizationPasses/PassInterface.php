<?php
declare(strict_types=1);

namespace Fervo\Rollo\Parser\OptimizationPasses;

use Fervo\Rollo\DieInterface;

interface PassInterface
{
    public function run(DieInterface $theDie): DieInterface;
}
