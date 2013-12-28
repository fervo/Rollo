<?php

namespace Fervo\Rollo\Parser\OptimizationPasses;

use Fervo\Rollo\DieCollection;
use Fervo\Rollo\DieInterface;

class MergeCollectionPass implements PassInterface
{
    public function run(DieInterface $theDie)
    {
        if ($theDie instanceOf DieCollection) {
            $this->doRun($theDie);
        }

        return $theDie;
    }

    protected function doRun(DieCollection $coll)
    {
        foreach ($coll->getDice() as $innerDie) {
            if ($innerDie instanceOf DieCollection) {
                $this->action($coll, $innerDie);
            }
        }
    }

    public function action(DieCollection $outerColl, DieCollection $innerColl)
    {
        $this->doRun($innerColl);

        if ($innerColl->getOperator() == $outerColl->getOperator() || count($innerColl->getDice()) == 1) {
            $outerColl->replaceDieWithDice($innerColl, $innerColl->getDice());
        }
    }
}
