<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Fervo\Rollo\Parser\Node;

use Fervo\Rollo\Parser\Compiler;

class BinaryNode extends Node
{
    public function __construct($operator, Node $left, Node $right)
    {
        parent::__construct(
            array('left' => $left, 'right' => $right),
            array('operator' => $operator)
        );
    }

    public function compile(Compiler $compiler)
    {
        $compiledLeft = $this->nodes['left']->compile($compiler);
        $compiledRight = $this->nodes['right']->compile($compiler);

        return $compiler->compileBinary($this->attributes['operator'], $compiledLeft, $compiledRight);
    }
}
