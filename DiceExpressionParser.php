<?php

namespace Fervo\Rollo;

class DiceExpressionParser
{
    protected $lexer;
    protected $parser;
    protected $compiler;
    protected $passes = [];

    public function __construct()
    {
        $this->lexer = new Parser\Lexer();
        $this->parser = new Parser\Parser();
        $this->compiler = new Parser\Compiler();
        $this->passes[] = new Parser\OptimizationPasses\MergeCollectionPass();
        $this->passes[] = new Parser\OptimizationPasses\RemoveSingleCollectionPass();
    }

    public function parseExpression($expression)
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
