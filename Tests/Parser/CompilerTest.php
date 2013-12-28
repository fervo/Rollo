<?php

namespace Fervo\Rollo\Tests\Parser;

use Fervo\Rollo\Parser\Compiler;
use Fervo\Rollo\Parser\Parser;
use Fervo\Rollo\Parser\Lexer;
use Fervo\Rollo\Parser\Node;

use Fervo\Rollo\ConstantDie;
use Fervo\Rollo\SingleDie;
use Fervo\Rollo\DieCollection;


/**
* 
*/
class CompilerTest extends \PHPUnit_Framework_TestCase
{
    public function testCanCompileConstant()
    {
        $compiler = new Compiler();
        $constant = new Node\ConstantNode(4);

        $this->assertEquals(new ConstantDie(4), $compiler->compile($constant));
    }

    public function testCanCompileMultiDie()
    {
        $compiler = new Compiler();
        $multidie = new Node\MultiDieNode(3,6);

        $coll = new DieCollection([
            new SingleDie(6),
            new SingleDie(6),
            new SingleDie(6),
        ]);

        $this->assertEquals($coll, $compiler->compile($multidie));
    }

    public function testCanCompileNegativeConstant()
    {
        $compiler = new Compiler();
        $unary = new Node\UnaryNode('-', new Node\ConstantNode(5));

        $this->assertEquals(new ConstantDie(-5), $compiler->compile($unary));
    }

    public function testCanCompileBinaryNode()
    {
        $compiler = new Compiler();
        $binary = new Node\BinaryNode('+', new Node\MultiDieNode(3,6), new Node\ConstantNode(3));

        $dice = new DieCollection([
            new DieCollection([
                new SingleDie(6),
                new SingleDie(6),
                new SingleDie(6),
            ]),
            new ConstantDie(3),
        ]);

        $this->assertEquals($dice, $compiler->compile($binary));
    }
}
