<?php
declare(strict_types=1);

namespace Fervo\Rollo;

use RuntimeException;

/**
* 
*/
class NotUniqueException extends RuntimeException
{
    public function __construct(DieInterface $die)
    {
        parent::__construct("Not unique");
    }
}
