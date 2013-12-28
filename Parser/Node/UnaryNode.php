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

class UnaryNode extends Node
{
    private static $operators = array(
        '+' => '+',
        '-' => '-',
    );

    public function __construct($operator, ConstantNode $node)
    {
        parent::__construct(
            array('node' => $node),
            array('operator' => $operator)
        );
    }

    public function compile(Compiler $compiler)
    {
        $node = $this->nodes['node'];
        if ($this->attributes['operator'] == '-') {
            $node = $node->getNegated();
        }

        return $node->compile($compiler);
    }
}
