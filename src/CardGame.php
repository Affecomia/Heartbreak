<?php

declare(strict_types=1);

namespace Heartbreak;

use Heartbreak\Cards\Collection\CardCollection;
use Heartbreak\Dealer\Dealer;
use Heartbreak\Player\Collection\PlayerCollection;
use Heartbreak\Score\ScoreCalculator;

use function floor;
use function implode;
use function is_null;
use function sprintf;

final class CardGame
{
    private PlayerCollection $playerCollection;
    private CardCollection $deck;
    private int $currentRound = 1;

    public function __construct(CardCollection $deck, PlayerCollection $playerCollection)
    {
        $this->deck             = $deck;
        $this->playerCollection = $playerCollection;
    }

    public function startGame(): void
    {
        print sprintf("Starting a game with %s\r\n", implode(', ', $this->playerCollection->all()));
        $this->dealCards();
        $this->playerCollection->generateNewPlayerOrder();
        while (true) {
            $this->playRound();
        }
    }

    private function reshuffle(): void
    {
        $this->deck = CardCollection::generateNewDeck();
        $this->dealCards();
    }

    private function dealCards(): void
    {
        $this->deck->shuffle();
        Dealer::assignCardsToPlayers($this->deck, $this->playerCollection);
    }

    private function playRound(): void
    {
        $playingOrder = $this->playerCollection->getOrder();
        $originalCard = null;
        $playedCards  = [];

        print sprintf("\r\n\r\nRound %s: %s starts the game\r\n", $this->currentRound, $this->playerCollection->findById($playingOrder[0]));

        foreach ($playingOrder as $playerId) {
            $player = $this->playerCollection->findById($playerId);
            $card   = $player->playMatchingCard($originalCard);
            $this->playerCollection->save($player->getId(), $player);
            $playedCards[$player->getId()->toInt()] = $card;
            print sprintf("%s plays: %s\r\n", $player, $card);

            if (is_null($originalCard)) {
                $originalCard = $card;
                continue;
            }
        }

        ScoreCalculator::calculate($originalCard, $playedCards, $this->playerCollection);
        $amountOfCardsPerPlayer = floor(32 / $this->playerCollection->count());

        if ($this->currentRound % $amountOfCardsPerPlayer === 0) {
            print "\r\nPlayers ran out of cards. Reshuffle.\r\n";
            $this->reshuffle();
        }

        $this->currentRound++;
        $this->playerCollection->shiftBeginningPlayer();
    }
}
