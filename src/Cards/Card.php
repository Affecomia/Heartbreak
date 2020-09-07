<?php

declare(strict_types=1);

namespace Heartbreak\Cards;

use Heartbreak\Cards\Enums\CardSuit;
use Heartbreak\Cards\Enums\CardValue;
use Heartbreak\Helpers\Id;

use function sprintf;

final class Card
{
    private Id $id;
    private CardSuit $suit;
    private CardValue $value;

    public static function fromState(array $state)
    {
        return new self(
            Id::fromInt($state['id']),
            CardSuit::fromId($state['suit']),
            CardValue::fromNumericalValue($state['value']['numericalValue'])
        );
    }

    public function __construct(Id $id, CardSuit $suit, CardValue $value)
    {
        $this->id    = $id;
        $this->suit  = $suit;
        $this->value = $value;
    }

    public function getId(): Id
    {
        return $this->id;
    }

    public function getSuit(): CardSuit
    {
        return $this->suit;
    }

    public function getValue(): CardValue
    {
        return $this->value;
    }

    public function __toString(): string
    {
        return sprintf('%s%s', $this->suit, $this->value);
    }
}
