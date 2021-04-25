<?php
declare(strict_types=1);

namespace Fervo\Rollo\Parser\Node;


use Fervo\Rollo\DieInterface;
use Fervo\Rollo\Parser\Compiler;

interface NodeInterface
{
    public function __toString();

    public function compile(Compiler $compiler): DieInterface;
}
