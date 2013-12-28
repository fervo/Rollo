<?php

namespace Fervo\Rollo\Parser\OptimizationPasses;

use Fervo\Rollo\DieCollection;
use Fervo\Rollo\DieInterface;

class RemoveSingleCollectionPass implements PassInterface
{
    public function run(DieInterface $theDie)
    {
        if ($theDie instanceOf DieCollection && count($theDie->getDice()) == 1) {
            return $theDie->getDice()[0];
        }

        return $theDie;
    }
}
