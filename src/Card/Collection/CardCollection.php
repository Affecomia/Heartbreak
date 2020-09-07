<?php

declare(strict_types=1);

namespace Heartbreak\Cards\Collection;

use Heartbreak\Cards\Card;
use Heartbreak\Cards\Enums\CardSuit;
use Heartbreak\Cards\Enums\CardValue;
use Heartbreak\DataStore\DataStore;
use Heartbreak\DataStore\InMemoryDataStore;
use Heartbreak\Helpers\Id;
use OutOfBoundsException;

use function array_pop;
use function is_object;
use function shuffle;
use function sprintf;

final class CardCollection
{
    private DataStore $dataStore;
    private array $order = [];

    public static function fromArray(array $array): CardCollection
    {
        return new self(InMemoryDataStore::fromArray($array));
    }

    public static function generateNewDeck(): CardCollection
    {
        $cardCollection = new self(new InMemoryDataStore());

        foreach (CardSuit::$possibleSuits as $id => $suit) {
            foreach (CardValue::$allValues as $numericalValue => $stringValue) {
                $card = new Card($cardCollection->generateId(), CardSuit::fromId($id), CardValue::fromNumericalValue($numericalValue));
                $cardCollection->save($card->getId(), $card);
            }
        }

        return $cardCollection;
    }

    public function __construct(DataStore $dataStore)
    {
        $this->dataStore = $dataStore;
    }

    public function generateId(): Id
    {
        return Id::fromInt($this->dataStore->createId());
    }

    public function findById(int $id): Card
    {
        try {
            $arrayData = $this->dataStore->getById($id);
        } catch (OutOfBoundsException $e) {
            throw new OutOfBoundsException(sprintf('Card with id %d does not exist', $id), 0, $e);
        }

        return Card::fromState($arrayData);
    }

    public function save(Id $id, Card $card): void
    {
        $this->dataStore->save($id->toInt(), [
            'id'    => $card->getId()->toInt(),
            'suit'  => $card->getSuit()->toInt(),
            'value' => [
                'numericalValue' => $card->getValue()->getNumericalValue(),
                'stringValue'    => $card->getValue()->getStringValue(),
            ],
        ]);
    }

    public function getRaw(): array
    {
        return $this->dataStore->all();
    }

    public function all(): array
    {
        $resultsArray = [];

        foreach ($this->dataStore->all() as $result) {
            if (is_object($result)) {
//                die(var_dump($result));
            }

            $resultsArray[] = Card::fromState($result);
        }

        return $resultsArray;
    }

    public function count(): int
    {
        return $this->dataStore->count();
    }

    public function where(string $key, $value): array
    {
        $resultsArray = [];

        foreach ($this->dataStore->where($key, $value) as $result) {
            $resultsArray[] = Card::fromState($result);
        }

        return $resultsArray;
    }

    public function random(): Card
    {
        $randomCardId = $this->dataStore->random();

        return $this->findById($randomCardId);
    }

    public function shuffle(): void
    {
        $this->order = $this->dataStore->getKeys();
        shuffle($this->order);
    }

    public function drawRandom(): Card
    {
        $randomCardId = $this->dataStore->random();

        return $this->draw($randomCardId);
    }

    public function draw(?int $cardId = null): Card
    {
        $cardId ??= array_pop($this->order);
        $card     = $this->findById($cardId);
        $this->dataStore->delete($cardId);

        return $card;
    }
}
