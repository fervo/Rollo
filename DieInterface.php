<?php

namespace Fervo\Rollo;

interface DieInterface
{
    public function roll();

    public function getValue();

    public function getValueDescription();

    public function getExpression();
}
