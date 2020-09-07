<?php
require_once __DIR__ . '/vendor/autoload.php';

use Heartbreak\CardGame;
use Heartbreak\Cards\Collection\CardCollection;
use Heartbreak\Player\Collection\PlayerCollection;
use Heartbreak\Player\Player;
use Heartbreak\DataStore\InMemoryDataStore;

$players = ['John', 'Jane', 'Jan', 'Otto'];

/** App starts here. */
$deck = CardCollection::generateNewDeck();
$playerCollection = new PlayerCollection(new InMemoryDataStore);
foreach ($players as $player) {
    $player = Player::fromName($playerCollection->generateId(), $player, new CardCollection(new InMemoryDataStore()));
    $playerCollection->save($player->getId(), $player);
}


$game = new CardGame($deck, $playerCollection);

$game->startGame();