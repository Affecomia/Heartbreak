<?php

declare(strict_types=1);

namespace Heartbreak\Dealer;

use Heartbreak\Cards\Collection\CardCollection;
use Heartbreak\Player\Collection\PlayerCollection;
use Heartbreak\Player\Player;

use function floor;
use function implode;
use function sprintf;

final class Dealer
{
    public static function assignCardsToPlayers(CardCollection $deck, PlayerCollection $playerCollection): void
    {
        $amountOfCardsPerPlayer = floor($deck->count() / $playerCollection->count());
        foreach ($playerCollection->all() as $player) {
            for ($i = 0; $i < $amountOfCardsPerPlayer; $i++) {
                $card = $deck->draw();
                $player->getHand()->save($card->getId(), $card);
            }

            $playerCollection->save($player->getId(), $player);

            self::printCardsToUI($player);
        }
    }

    private static function printCardsToUI(Player $player): void
    {
        print sprintf("%s has been dealt: %s\r\n", $player->getName(), implode(' ', $player->getHand()->all()));
    }
}
