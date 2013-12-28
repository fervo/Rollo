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

class MultiDieNode extends Node
{
    public function __construct($num, $sides)
    {
        parent::__construct(
            array(),
            array('sides' => $sides, 'num' => $num)
        );
    }

    public function compile(Compiler $compiler)
    {
        return $compiler->compileMultiDie($this->attributes['num'], $this->attributes['sides']);
    }
}
