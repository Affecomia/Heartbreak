<?php

namespace Tests;

use Heartbreak\Cards\Card;
use Heartbreak\Cards\Collection\CardCollection;
use Heartbreak\Cards\Enums\CardSuit;
use Heartbreak\Cards\Enums\CardValue;
use OutOfBoundsException;
use PHPUnit\Framework\TestCase;

/** I want those bonus points ¯\_(ツ)_/¯ */
class CardCollectionTest extends TestCase
{
    private CardCollection $cardCollection;

    protected function setUp(): void
    {
        $this->cardCollection = new CardCollection(new \Heartbreak\DataStore\InMemoryDataStore);
    }

    public function testCanGenerateId() {
        $this->assertEquals(1, $this->cardCollection->generateId()->toInt());
    }

    public function testThrowsExceptionWhenTryingToFindInvalidCard()
    {
        $this->expectException(OutOfBoundsException::class);
        $this->expectExceptionMessage('Cards with id 42 does not exist');

        $this->cardCollection->findById(42);
    }

    public function testCanSaveCard()
    {
        $newId = $this->cardCollection->generateId();
        $card = new Card($newId, CardSuit::fromId(CardSuit::SPADES_ID), CardValue::fromStringValue(CardValue::QUEEN));
        $this->cardCollection->save($newId, $card);

        $this->assertEquals($newId, $this->cardCollection->findById($newId->toInt())->getId());
    }
}