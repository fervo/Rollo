<?php

namespace Fervo\Rollo\Tests\Parser;

use Fervo\Rollo\DiceExpressionParser;
use Fervo\Rollo\Parser\Lexer;
use Fervo\Rollo\Parser\Parser;
use Fervo\Rollo\Parser\Compiler;
use Fervo\Rollo\Parser\OptimizationPasses\MergeCollectionPass;

class FunctionalTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider getUnoptimizedData
     */
    public function testCompileWithoutOptimization($expression, $expectedDescription)
    {
        $lexer = new Lexer();
        $parser = new Parser(array());
        $compiler = new Compiler();
        $node = $parser->parse($lexer->tokenize($expression));
        $aDie = $compiler->compile($node);

        $this->assertEquals($expectedDescription, $aDie->getValueDescription());
    }

    /**
     * @dataProvider getOptimizedData
     */
    public function testCompileWithOptimization($expression, $expectedDescription)
    {
        $dep = new DiceExpressionParser;
        $aDie = $dep->parseExpression($expression);

        $this->assertEquals($expectedDescription, $aDie->getValueDescription());
    }

    public function getUnoptimizedData()
    {
        return [
            [ '4', '#4' ],
            [ '3D6', '[D6:* + D6:* + D6:*]=*'],
            [ '2C100', '[D100:{*+*}=* + D100:{*+*}=*]=*' ],
            [ '2C100BB', '[D100:{B*,B*,*+*}=* + D100:{B*,B*,*+*}=*]=*' ],
            [ '2C100BPP', '[D100:{P*,*+*}=* + D100:{P*,*+*}=*]=*' ],
            [ '3D6 + 3', '[[D6:* + D6:* + D6:*]=* + #3]=*'],
            [ '1D8 + 1D4 + 1', '[[[D8:*]=* + [D4:*]=*]=* + #1]=*'],
            [ '1D8 + 1D4 - 1', '[[[D8:*]=* + [D4:*]=*]=* - #1]=*'],
            [ '1D8+1D4+1', '[[[D8:*]=* + [D4:*]=*]=* + #1]=*'],
            [ '1D8+1D4-1', '[[[D8:*]=* + [D4:*]=*]=* - #1]=*'],
            [ '1D8-1D4-1', '[[[D8:*]=* - [D4:*]=*]=* - #1]=*'],
            [ '1D8 + 1D4 + -1', '[[[D8:*]=* + [D4:*]=*]=* + #-1]=*'],
        ];
    }

    public function getOptimizedData()
    {
        return [
            [ '4', '#4' ],
            [ '3D6', '[D6:* + D6:* + D6:*]=*'],
            [ '3D6 + 3', '[D6:* + D6:* + D6:* + #3]=*'],
            [ '1D8 + 1D4 + 1', '[D8:* + D4:* + #1]=*'],
            [ '1D8 + 1D4 - 1', '[[D8:* + D4:*]=* - #1]=*'],
            [ '1D8+1D4+1', '[D8:* + D4:* + #1]=*'],
            [ '1D8+1D4-1', '[[D8:* + D4:*]=* - #1]=*'],
            [ '1D8-1D4-1', '[D8:* - D4:* - #1]=*'],
            [ '1D8 + 1D4 + -1', '[D8:* + D4:* + #-1]=*'],
        ];
    }
}
