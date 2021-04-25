<?php
declare(strict_types=1);

namespace Fervo\Rollo\Parser\OptimizationPasses;

use Fervo\Rollo\DieCollection;
use Fervo\Rollo\DieInterface;

class RemoveSingleCollectionPass implements PassInterface
{
    public function run(DieInterface $theDie): DieInterface
    {
        if ($theDie instanceOf DieCollection && 1 === \count($theDie->getDice())) {
            return $theDie->getDice()[0];
        }

        return $theDie;
    }
}
