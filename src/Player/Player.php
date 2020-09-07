<?php

declare(strict_types=1);

namespace Heartbreak\Player;

use Heartbreak\Cards\Card;
use Heartbreak\Cards\Collection\CardCollection;
use Heartbreak\Helpers\Id;

use function array_pop;
use function is_null;

final class Player
{
    private Id $id;
    private string $name;
    private CardCollection $cardCollection;
    private int $score = 0;

    public static function fromName(Id $id, string $name, CardCollection $cardCollection): Player
    {
        return new self($id, $name, $cardCollection);
    }

    public static function fromState(array $state): Player
    {
        return new self(Id::fromInt($state['id']), $state['name'], CardCollection::fromArray($state['hand']), $state['score']);
    }

    private function __construct(Id $id, string $name, CardCollection $hand, int $score = 0)
    {
        $this->id             = $id;
        $this->name           = $name;
        $this->cardCollection = $hand;
        $this->score          = $score;
    }

    public function getId(): Id
    {
        return $this->id;
    }

    public function getHand(): CardCollection
    {
        return $this->cardCollection;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getScore(): int
    {
        return $this->score;
    }

    public function addToScore(int $score): void
    {
        $this->score += $score;
    }

    public function __toString(): string
    {
        return $this->getName();
    }

    public function playMatchingCard(?Card $cardToMatch): ?Card
    {
        $matchingCards = [];

        if (is_null($cardToMatch)) {
            return $this->getHand()->drawRandom();
        }

        $matchingCards = $this->getHand()->where('suit', $cardToMatch->getSuit()->toInt());
        $lowestCard    = $this->findLowestCard($matchingCards);

        return is_null($lowestCard) ? $this->getHand()->drawRandom() : $this->getHand()->draw($lowestCard->getId()->toInt());
    }

    private function findLowestCard(array $cards): ?Card
    {
        $lowestCard = array_pop($cards);
        foreach ($cards as $card) {
            if ($card->getValue()->getNumericalValue() >= $lowestCard->getValue()->getNumericalValue()) {
                continue;
            }

            $lowestCard = $card;
        }

        return $lowestCard;
    }
}
