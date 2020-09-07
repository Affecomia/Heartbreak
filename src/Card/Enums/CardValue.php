<?php

declare(strict_types=1);

namespace Heartbreak\Cards\Enums;

use InvalidArgumentException;

use function array_keys;
use function array_search;
use function in_array;

final class CardValue
{
    public const JACK  = 'J';
    public const QUEEN = 'Q';

    public static array $allValues = [
        0 => '7',
        1 => '8',
        2 => '9',
        3 => '10',
        4 => 'J',
        5 => 'Q',
        6 => 'K',
        7 => 'A',
    ];

    private int $numericalValue;
    private string $stringValue;

    public static function fromNumericalValue(int $numericalValue): CardValue
    {
        self::validateNumericalValue($numericalValue);

        return new self($numericalValue, self::$allValues[$numericalValue]);
    }

    public static function fromStringValue(string $stringValue): CardValue
    {
        self::validateStringValue($stringValue);

        return new self(array_search($stringValue, self::$allValues), $stringValue);
    }

    private function __construct(int $numericalValue, string $stringValue)
    {
        $this->numericalValue = $numericalValue;
        $this->stringValue    = $stringValue;
    }

    public function getNumericalValue(): int
    {
        return $this->numericalValue;
    }

    public function getStringValue(): string
    {
        return $this->stringValue;
    }

    public function __toString()
    {
        return $this->getStringValue();
    }

    private static function validateStringValue(string $value): void
    {
        if (! in_array($value, self::$allValues)) {
            throw new InvalidArgumentException('Invalid numerical card value given.');
        }
    }

    private static function validateNumericalValue(int $value): void
    {
        if (! in_array($value, array_keys(self::$allValues))) {
            throw new InvalidArgumentException('Invalid numerical card value given.');
        }
    }
}
