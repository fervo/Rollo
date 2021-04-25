<?php
declare(strict_types=1);

namespace Fervo\Rollo;


use Fervo\Rollo\Parser\SyntaxError;

class RollExpressionParser
{
    private DiceExpressionParser $diceExpressionParser;

    public function __construct(DiceExpressionParser $diceExpressionParser)
    {
        $this->diceExpressionParser = $diceExpressionParser;
    }

    public function parseRollExpression(string $rollExpression): Roll
    {
        if (!preg_match("/(.+?)\s*(([=!><]+)\s*(\d+))?$/", $rollExpression, $matches)) {
            throw new SyntaxError("Unknown roll expression", 0);
        }

        $die = $this->diceExpressionParser->parseExpression($matches[1]);

        if (isset($matches[2]) && $matches[3] && $matches[4]) {
            return new Roll($die, $matches[3], (int)$matches[4]);
        }

        return new Roll($die);
    }
}
