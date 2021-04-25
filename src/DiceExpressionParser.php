<?php
declare(strict_types=1);

namespace Fervo\Rollo;

use Fervo\Rollo\Parser\OptimizationPasses\PassInterface;

class DiceExpressionParser
{
    protected Parser\Lexer $lexer;
    protected Parser\Parser $parser;
    protected Parser\Compiler $compiler;
    /** @var PassInterface[]  */
    protected array $passes = [];

    public function __construct()
    {
        $this->lexer = new Parser\Lexer();
        $this->parser = new Parser\Parser();
        $this->compiler = new Parser\Compiler();
        $this->passes[] = new Parser\OptimizationPasses\MergeCollectionPass();
        $this->passes[] = new Parser\OptimizationPasses\RemoveSingleCollectionPass();
    }

    public function parseExpression(string $expression): DieInterface
    {
        $tokenStream = $this->lexer->tokenize($expression);
        $node = $this->parser->parse($tokenStream);
        $aDie = $this->compiler->compile($node);
        foreach ($this->passes as $pass) {
            $aDie = $pass->run($aDie);
        }

        return $aDie;
    }
}
