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

class ConstantNode extends Node
{
    public function __construct($value)
    {
        parent::__construct(
            [],
            ['value' => $value]
        );
    }

    public function getNegated(): ConstantNode
    {
        return new static((int)$this->attributes['value'] * -1);
    }

    public function compile(Compiler $compiler): DieInterface
    {
        return $compiler->compileConstant($this->attributes['value']);
    }
}
