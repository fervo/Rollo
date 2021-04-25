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

/**
 * Represents a node in the AST.
 *
 * @author Fabien Potencier <fabien@symfony.com>
 */
abstract class Node implements NodeInterface
{
    /** @var NodeInterface[]  */
    public array $nodes = [];
    public array $attributes = [];

    /**
     * Constructor.
     *
     * @param NodeInterface[] $nodes      An array of nodes
     * @param array $attributes An array of attributes
     */
    public function __construct(array $nodes = [], array $attributes = [])
    {
        $this->nodes = $nodes;
        $this->attributes = $attributes;
    }

    public function __toString()
    {
        $attributes = [];
        foreach ($this->attributes as $name => $value) {
            $attributes[] = sprintf('%s: %s', $name, str_replace("\n", '', var_export($value, true)));
        }

        $repr = [str_replace('Fervo\Rollo\Parser\Node\\', '', \get_class($this)).'('.implode(', ', $attributes)];

        if (\count($this->nodes)) {
            foreach ($this->nodes as $node) {
                foreach (explode("\n", (string) $node) as $line) {
                    $repr[] = '    '.$line;
                }
            }

            $repr[] = ')';
        } else {
            $repr[0] .= ')';
        }

        return implode("\n", $repr);
    }

    abstract public function compile(Compiler $compiler): DieInterface;
}
