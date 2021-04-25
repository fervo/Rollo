<?php
declare(strict_types=1);

namespace Fervo\Rollo\Parser\OptimizationPasses;

use Fervo\Rollo\DieCollection;
use Fervo\Rollo\DieInterface;

class MergeCollectionPass implements PassInterface
{
    public function run(DieInterface $theDie): DieInterface
    {
        if ($theDie instanceOf DieCollection) {
            $this->doRun($theDie);
        }

        return $theDie;
    }

    protected function doRun(DieCollection $coll): void
    {
        foreach ($coll->getDice() as $innerDie) {
            if ($innerDie instanceOf DieCollection) {
                $this->action($coll, $innerDie);
            }
        }
    }

    public function action(DieCollection $outerColl, DieCollection $innerColl): void
    {
        $this->doRun($innerColl);

        if ($innerColl->getOperator() === $outerColl->getOperator() || 1 === \count($innerColl->getDice())) {
            $outerColl->replaceDieWithDice($innerColl, $innerColl->getDice());
        }
    }
}
