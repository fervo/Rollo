<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Fervo\Rollo\Tests\Parser;

use Fervo\Rollo\Parser\Lexer;
use Fervo\Rollo\Parser\Token;
use Fervo\Rollo\Parser\TokenStream;

class LexerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider getTokenizeData
     */
    public function testTokenize($tokens, $expression)
    {
        $tokens[] = new Token('end of expression', null, strlen($expression) + 1);
        $lexer = new Lexer();
        $this->assertEquals(new TokenStream($tokens), $lexer->tokenize($expression));
    }

    public function getTokenizeData()
    {
        return [
            [
                [new Token('int', '12', 1)],
                '12'
            ],
            [
                [new Token('int', '12', 3)],
                '  12  '
            ],
            [
                [new Token('multidie', 'D6', 1)],
                'D6'
            ],
            [
                [new Token('multidie', '3D6', 1)],
                '3D6'
            ],
            [
                [new Token('multidie', '4d20', 1)],
                '4d20'
            ],
            [
                [new Token('multidie', '4d20', 1), new Token('operator', '+', 6), new Token('int', '3', 8)],
                '4d20 + 3'
            ],
        ];
    }
}
