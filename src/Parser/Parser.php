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

use Fervo\Rollo\Parser\Node\NodeInterface;

/**
 * Parsers a token stream.
 *
 * This parser implements a "Precedence climbing" algorithm.
 *
 * @see http://www.engr.mun.ca/~theo/Misc/exp_parsing.htm
 * @see http://en.wikipedia.org/wiki/Operator-precedence_parser
 *
 * @author Fabien Potencier <fabien@symfony.com>
 */
class Parser
{
    public const OPERATOR_LEFT = 1;
    public const OPERATOR_RIGHT = 2;

    private ?TokenStream $stream;
    private array $unaryOperators;
    private array $binaryOperators;

    public function __construct()
    {
        $this->unaryOperators = [
            '-'   => ['precedence' => 500],
            '+'   => ['precedence' => 500],
        ];
        $this->binaryOperators = [
            '+'       => ['precedence' => 30,  'associativity' => self::OPERATOR_LEFT],
            '-'       => ['precedence' => 30,  'associativity' => self::OPERATOR_LEFT],
        ];
    }

    /**
     * Converts a token stream to a node tree.
     *
     * @throws SyntaxError
     */
    public function parse(TokenStream $stream): NodeInterface
    {
        $this->stream = $stream;

        $node = $this->parseExpression();
        if (!$stream->isEOF()) {
            throw new SyntaxError(sprintf('Unexpected token "%s" of value "%s"', $stream->current->type, $stream->current->value), $stream->current->cursor);
        }

        return $node;
    }

    public function parseExpression($precedence = 0): NodeInterface
    {
        $expr = $this->getPrimary();
        $token = $this->stream->current;
        while ($token->test(Token::OPERATOR_TYPE) && isset($this->binaryOperators[$token->value]) && $this->binaryOperators[$token->value]['precedence'] >= $precedence) {
            $op = $this->binaryOperators[$token->value];
            $this->stream->next();

            $expr1 = $this->parseExpression(self::OPERATOR_LEFT === $op['associativity'] ? $op['precedence'] + 1 : $op['precedence']);
            $expr = new Node\BinaryNode($token->value, $expr, $expr1);

            $token = $this->stream->current;
        }

        return $expr;
    }

    protected function getPrimary()
    {
        $token = $this->stream->current;

        if (isset($this->unaryOperators[$token->value]) && $token->test(Token::OPERATOR_TYPE)) {
            $operator = $this->unaryOperators[$token->value];
            $this->stream->next();
            $expr = $this->parseExpression($operator['precedence']);

            if (!$expr instanceOf Node\ConstantNode) {
                throw new SyntaxError("Only constants are allowed as unary node children");
            }

            return new Node\UnaryNode($token->value, $expr);
        }

        return $this->parsePrimaryExpression();
    }

    public function parsePrimaryExpression()
    {
        $token = $this->stream->current;
        switch ($token->type) {
            case Token::INT_TYPE:
                $this->stream->next();

                return new Node\ConstantNode($token->value);

            case Token::COCDIE_TYPE:
                $this->stream->next();

                preg_match('/(\d*)([Cc])100([BbPp]*)/', $token->value, $match);

                if (4 !== \count($match)) {
                    throw new SyntaxError(sprintf("MultiDie token %s has invalid format", $token->value));
                }

                $bonus = 0;
                $penalty = 0;

                foreach (str_split($match[3]) as $extra) {
                    if ('b' === strtolower($extra)) {
                        $bonus++;
                    } elseif ('p' === strtolower($extra)) {
                        $penalty++;
                    }
                }

                return new Node\CocDieNode((empty($match[1]) ? 1 : $match[1]), $bonus, $penalty);

            case Token::MULTIDIE_TYPE:
                $this->stream->next();

                preg_match('/(\d*)([Dd])(\d+)/', $token->value, $match);

                if (4 !== \count($match)) {
                    throw new SyntaxError(sprintf("MultiDie token %s has invalid format", $token->value));
                }

                return new Node\MultiDieNode((empty($match[1]) ? 1 : $match[1]), $match[3]);
            default:
                throw new SyntaxError(sprintf('Unexpected token "%s" of value "%s"', $token->type, $token->value), $token->cursor);
        }
    }
}
