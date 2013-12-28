<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Fervo\Rollo\Parser;

/**
 * Lexes an expression.
 *
 * @author Fabien Potencier <fabien@symfony.com>
 */
class Lexer
{
    /**
     * Tokenizes an expression.
     *
     * @param string $expression The expression to tokenize
     *
     * @return TokenStream A token stream instance
     *
     * @throws SyntaxError
     */
    public function tokenize($expression)
    {
        $expression = str_replace(array("\r", "\n", "\t", "\v", "\f"), ' ', $expression);
        $cursor = 0;
        $tokens = array();
        $end = strlen($expression);

        while ($cursor < $end) {
            if (' ' == $expression[$cursor]) {
                ++$cursor;

                continue;
            }


            if (preg_match('/[0-9]*(C|c)100[PpBb]*/A', $expression, $match, null, $cursor)) {
                // cocdie
                $tokens[] = new Token(Token::COCDIE_TYPE, $match[0], $cursor + 1);
                $cursor += strlen($match[0]);
            } elseif (preg_match('/[0-9]*(D|d)[0-9]+/A', $expression, $match, null, $cursor)) {
                // multidie
                $tokens[] = new Token(Token::MULTIDIE_TYPE, $match[0], $cursor + 1);
                $cursor += strlen($match[0]);
            } elseif (preg_match('/[0-9]+/A', $expression, $match, null, $cursor)) {
                // integers
                $number = (int) $match[0];  // integers
                $tokens[] = new Token(Token::INT_TYPE, $number, $cursor + 1);
                $cursor += strlen($match[0]);
            } elseif (preg_match('/\+|\-/A', $expression, $match, null, $cursor)) {
                // operators
                $tokens[] = new Token(Token::OPERATOR_TYPE, $match[0], $cursor + 1);
                $cursor += strlen($match[0]);
            } else {
                // unlexable
                throw new SyntaxError(sprintf('Unexpected character "%s"', $expression[$cursor]), $cursor);
            }
        }

        $tokens[] = new Token(Token::EOF_TYPE, null, $cursor + 1);

        return new TokenStream($tokens);
    }
}
