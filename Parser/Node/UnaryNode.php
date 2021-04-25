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

class UnaryNode extends Node
{
    private static array $operators = [
        '+' => '+',
        '-' => '-',
    ];

    public function __construct($operator, ConstantNode $node)
    {
        parent::__construct(
            ['node' => $node],
            ['operator' => $operator]
        );
    }

    public function compile(Compiler $compiler): DieInterface
    {
        $node = $this->nodes['node'];
        if ('-' === $this->attributes['operator'] && $node instanceof ConstantNode) {
            $node = $node->getNegated();
        }

        return $node->compile($compiler);
    }
}
