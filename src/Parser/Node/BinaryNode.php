<?php
declare(strict_types=1);

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Fervo\Rollo\Parser\Node;

use Fervo\Rollo\DieInterface;
use Fervo\Rollo\Parser\Compiler;

class BinaryNode extends Node
{
    public function __construct($operator, NodeInterface $left, NodeInterface $right)
    {
        parent::__construct(
            ['left' => $left, 'right' => $right],
            ['operator' => $operator]
        );
    }

    public function compile(Compiler $compiler): DieInterface
    {
        $compiledLeft = $this->nodes['left']->compile($compiler);
        $compiledRight = $this->nodes['right']->compile($compiler);

        return $compiler->compileBinary($this->attributes['operator'], $compiledLeft, $compiledRight);
    }
}
