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
    const OPERATOR_LEFT = 1;
    const OPERATOR_RIGHT = 2;

    private $stream;
    private $unaryOperators;
    private $binaryOperators;

    public function __construct()
    {
        $this->unaryOperators = array(
            '-'   => array('precedence' => 500),
            '+'   => array('precedence' => 500),
        );
        $this->binaryOperators = array(
            '+'       => array('precedence' => 30,  'associativity' => Parser::OPERATOR_LEFT),
            '-'       => array('precedence' => 30,  'associativity' => Parser::OPERATOR_LEFT),
        );
    }

    /**
     * Converts a token stream to a node tree.
     *
     * @param TokenStream $stream A token stream instance
     * @param array       $names  An array of valid names
     *
     * @return Node A node tree
     *
     * @throws SyntaxError
     */
    public function parse(TokenStream $stream, $names = array())
    {
        $this->stream = $stream;
        $this->names = $names;

        $node = $this->parseExpression();
        if (!$stream->isEOF()) {
            throw new SyntaxError(sprintf('Unexpected token "%s" of value "%s"', $stream->current->type, $stream->current->value), $stream->current->cursor);
        }

        return $node;
    }

    public function parseExpression($precedence = 0)
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

        if ($token->test(Token::OPERATOR_TYPE) && isset($this->unaryOperators[$token->value])) {
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

                preg_match('/([0-9]*)(C|c)100([BbPp]*)/', $token->value, $match);

                if (!count($match) == 5) {
                    throw new SyntaxError(sprintf("MultiDie token %s has invalid format", $token->value));
                }

                $bonus = 0;
                $penalty = 0;

                foreach (str_split($match[3]) as $extra) {
                    if (strtolower($extra) == 'b') {
                        $bonus++;
                    } elseif (strtolower($extra) == 'p') {
                        $penalty++;
                    }
                }

                return new Node\CocDieNode((empty($match[1]) ? 1 : $match[1]), $bonus, $penalty);

            case Token::MULTIDIE_TYPE:
                $this->stream->next();

                preg_match('/([0-9]*)(D|d)([0-9]+)/', $token->value, $match);

                if (!count($match) == 4) {
                    throw new SyntaxError(sprintf("MultiDie token %s has invalid format", $token->value));
                }

                return new Node\MultiDieNode((empty($match[1]) ? 1 : $match[1]), $match[3]);
            default:
                throw new SyntaxError(sprintf('Unexpected token "%s" of value "%s"', $token->type, $token->value), $token->cursor);
        }

        return $node;
    }
}
