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
    public function tokenize(string $expression): TokenStream
    {
        $expression = str_replace(["\r", "\n", "\t", "\v", "\f"], ' ', $expression);
        $cursor = 0;
        $tokens = [];
        $end = \strlen($expression);

        while ($cursor < $end) {
            if (' ' === $expression[$cursor]) {
                ++$cursor;

                continue;
            }


            if (preg_match('/\d*([Cc])100[PpBb]*/A', $expression, $match, 0, $cursor)) {
                // cocdie
                $tokens[] = new Token(Token::COCDIE_TYPE, $match[0], $cursor + 1);
                $cursor += \strlen($match[0]);
            } elseif (preg_match('/\d*([Dd])\d+/A', $expression, $match, 0, $cursor)) {
                // multidie
                $tokens[] = new Token(Token::MULTIDIE_TYPE, $match[0], $cursor + 1);
                $cursor += \strlen($match[0]);
            } elseif (preg_match('/\d+/A', $expression, $match, 0, $cursor)) {
                // integers
                $number = (int) $match[0];  // integers
                $tokens[] = new Token(Token::INT_TYPE, (string)$number, $cursor + 1);
                $cursor += \strlen($match[0]);
            } elseif (preg_match('/[+\-]/A', $expression, $match, 0, $cursor)) {
                // operators
                $tokens[] = new Token(Token::OPERATOR_TYPE, $match[0], $cursor + 1);
                $cursor += \strlen($match[0]);
            } else {
                // unlexable
                throw new SyntaxError(sprintf('Unexpected character "%s"', $expression[$cursor]), $cursor);
            }
        }

        $tokens[] = new Token(Token::EOF_TYPE, (string)null, $cursor + 1);

        return new TokenStream($tokens);
    }
}
