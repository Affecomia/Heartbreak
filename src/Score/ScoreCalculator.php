<?php

declare(strict_types=1);

namespace Heartbreak\Score;

use Heartbreak\Cards\Card;
use Heartbreak\Cards\Enums\CardSuit;
use Heartbreak\Cards\Enums\CardValue;
use Heartbreak\Player\Collection\PlayerCollection;
use Heartbreak\Player\Player;

use function array_search;
use function assert;
use function sprintf;

final class ScoreCalculator
{
    public static function calculate(Card $originalCard, array $cards, PlayerCollection $playerCollection): void
    {
        $points              = 0;
        $highestMatchingCard = $originalCard;
        foreach ($cards as $card) {
            assert($card instanceof Card);

            if ($card->getSuit()->toInt() === CardSuit::HEARTS_ID) {
                $points++;
            }

            if (
                $card->getSuit()->toInt() === CardSuit::CLUBS_ID
                && $card->getValue()->getStringValue() === CardValue::JACK
            ) {
                $points += 2;
            }

            if (
                $card->getSuit()->toInt() === CardSuit::SPADES_ID
                && $card->getValue()->getNumericalValue() === CardValue::QUEEN
            ) {
                $points += 5;
            }

            if ($card->getSuit()->toInt() !== $originalCard->getSuit()->toInt()) {
                continue;
            }

            if ($card->getValue()->getNumericalValue() <= $highestMatchingCard->getValue()->getNumericalValue()) {
                continue;
            }

            $highestMatchingCard = $card;
        }

        $loser = $playerCollection->findById(array_search($highestMatchingCard, $cards));
        $loser->addToScore($points);
        $playerCollection->save($loser->getId(), $loser);

        print sprintf("\r\n%s played %s, the highest matching card of this match and got %d point added to his/her total
score. %s’s total score is %d point(s).\r\n", $loser, $highestMatchingCard, $points, $loser, $loser->getScore());

        if ($loser->getScore() < 50) {
            return;
        }

        print sprintf("%s loses the game!\r\n\r\n", $loser);
        self::endGame($playerCollection);
    }

    private static function endGame(PlayerCollection $playerCollection): void
    {
        print "Scores:\r\n";
        foreach ($playerCollection->all() as $player) {
            assert($player instanceof Player);
            print sprintf("%s: %d\r\n", $player, $player->getScore());
        }

        exit(1);
    }
}
