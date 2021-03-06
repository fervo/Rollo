<?php

/*
 * This file is based on the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Fervo\Rollo\Parser;

/**
 * Represents a Token.
 *
 * @author Fabien Potencier <fabien@symfony.com>
 */
class Token
{
    public $value;
    public $type;
    public $cursor;

    const EOF_TYPE         = 'end of expression';
    const MULTIDIE_TYPE    = 'multidie';
    const COCDIE_TYPE      = 'cocdie';
    const INT_TYPE         = 'int';
    const OPERATOR_TYPE    = 'operator';

    /**
     * Constructor.
     *
     * @param integer $type   The type of the token
     * @param string  $value  The token value
     * @param integer $cursor The cursor position in the source
     */
    public function __construct($type, $value, $cursor)
    {
        $this->type = $type;
        $this->value = $value;
        $this->cursor = $cursor;
    }

    /**
     * Returns a string representation of the token.
     *
     * @return string A string representation of the token
     */
    public function __toString()
    {
        return sprintf('%3d %-11s %s', $this->cursor, strtoupper($this->type), $this->value);
    }

    /**
     * Tests the current token for a type and/or a value.
     *
     * @param array|integer $type  The type to test
     * @param string|null   $value The token value
     *
     * @return Boolean
     */
    public function test($type, $value = null)
    {
        return $this->type === $type && (null === $value || $this->value == $value);
    }
}
