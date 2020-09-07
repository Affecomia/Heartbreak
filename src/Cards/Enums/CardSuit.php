<?php

declare(strict_types=1);

namespace Heartbreak\Cards\Enums;

use InvalidArgumentException;

use function array_keys;
use function in_array;

final class CardSuit
{
    public const HEARTS   = '♥';
    public const SPADES   = '♠';
    public const CLUBS    = '♣';
    public const DIAMONDS = '♦';

    public const HEARTS_ID   = 1;
    public const SPADES_ID   = 2;
    public const CLUBS_ID    = 3;
    public const DIAMONDS_ID = 4;

    public static array $possibleSuits = [
        self::HEARTS_ID   => self::HEARTS,
        self::SPADES_ID   => self::SPADES,
        self::CLUBS_ID    => self::CLUBS,
        self::DIAMONDS_ID => self::DIAMONDS,
    ];

    private int $id;
    private string $suit;

    public static function fromId(int $id): CardSuit
    {
        self::validateId($id);

        return new self($id, self::$possibleSuits[$id]);
    }

    private function __construct(int $id, string $suit)
    {
        $this->id   = $id;
        $this->suit = $suit;
    }

    public function toInt(): int
    {
        return $this->id;
    }

    public function __toString(): string
    {
        return $this->suit;
    }

    private static function validateId(int $id): void
    {
        if (! in_array($id, array_keys(self::$possibleSuits))) {
            throw new InvalidArgumentException('Invalid card suit ID given.');
        }
    }
}
