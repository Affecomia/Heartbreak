<?php

declare(strict_types=1);

namespace Heartbreak\Player\Collection;

use Heartbreak\DataStore\DataStore;
use Heartbreak\Helpers\Id;
use Heartbreak\Player\Player;
use OutOfBoundsException;

use function array_pop;
use function array_push;
use function range;
use function shuffle;
use function sprintf;

final class PlayerCollection
{
    private DataStore $dataStore;
    private array $order = [];

    public function __construct(DataStore $dataStore)
    {
        $this->dataStore = $dataStore;
    }

    public function generateId(): Id
    {
        return Id::fromInt($this->dataStore->createId());
    }

    public function findById(int $id)
    {
        try {
            $arrayData = $this->dataStore->getById($id);
        } catch (OutOfBoundsException $e) {
            throw new OutOfBoundsException(sprintf('Player with id %d does not exist', $id), 0, $e);
        }

        return Player::fromState($arrayData);
    }

    public function count(): int
    {
        return $this->dataStore->count();
    }

    public function generateNewPlayerOrder(): void
    {
        $this->order = range(1, $this->count());
        shuffle($this->order);
    }

    public function getOrder(): array
    {
        return $this->order;
    }

    public function shiftBeginningPlayer(): void
    {
        $id = array_pop($this->order);
        array_push($this->order, $id);
    }

    public function all(): array
    {
        $resultsArray = [];

        foreach ($this->dataStore->all() as $result) {
            $resultsArray[] = Player::fromState($result);
        }

        return $resultsArray;
    }

    public function save(Id $id, Player $player): void
    {
        $this->dataStore->save($id->toInt(), [
            'id' => $player->getId()->toInt(),
            'name' => $player->getName(),
            'hand' => $player->getHand()->getRaw(),
            'score' => $player->getScore(),
        ]);
    }
}
