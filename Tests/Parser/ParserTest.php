<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\ExpressionLanguage\Tests\Node;

use Fervo\Rollo\Parser\Parser;
use Fervo\Rollo\Parser\Lexer;
use Fervo\Rollo\Parser\Node;

class ParserTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @expectedException        \Fervo\Rollo\Parser\SyntaxError
     * @expectedExceptionMessage Unexpected character "f" around position 0.
     */
    public function testParseWithInvalidName()
    {
        $lexer = new Lexer();
        $parser = new Parser(array());
        $parser->parse($lexer->tokenize('foo'));
    }

    /**
     * @dataProvider getParseData
     */
    public function testParse($node, $expression, $names = array())
    {
        $lexer = new Lexer();
        $parser = new Parser(array());
        $this->assertEquals($node, $parser->parse($lexer->tokenize($expression), $names));
    }

    public function getParseData()
    {
        return [
            [
                new Node\ConstantNode(3),
                '3',
            ],
            [
                new Node\UnaryNode('-', new Node\ConstantNode(3)),
                '-3',
            ],
            [
                new Node\BinaryNode('-', new Node\ConstantNode(3), new Node\ConstantNode(3)),
                '3 - 3',
            ],
            [
                new Node\MultiDieNode(3, 6),
                '3d6',
            ],
            [
                new Node\MultiDieNode(1, 10),
                'd10',
            ],
        ];
    }
}
