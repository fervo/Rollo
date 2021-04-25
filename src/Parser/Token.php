<?php
declare(strict_types=1);

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
    public string $value;
    public string $type;
    public int $cursor;

    public const EOF_TYPE         = 'end of expression';
    public const MULTIDIE_TYPE    = 'multidie';
    public const COCDIE_TYPE      = 'cocdie';
    public const INT_TYPE         = 'int';
    public const OPERATOR_TYPE    = 'operator';

    public function __construct(string $type, string $value, int $cursor)
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
     * @param string $type The type to test
     * @param string|null $value The token value
     *
     * @return Boolean
     */
    public function test(string $type, ?string $value = null): bool
    {
        return $this->type === $type && (null === $value || $this->value === $value);
    }
}
