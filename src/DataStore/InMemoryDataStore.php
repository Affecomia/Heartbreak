<?php

declare(strict_types=1);

namespace Heartbreak\DataStore;

use OutOfBoundsException;

use function array_keys;
use function array_rand;
use function count;
use function shuffle;
use function sprintf;

final class InMemoryDataStore implements DataStore
{
    private array $data     = [];
    private int $lastUsedId = 0;

    public static function fromArray(array $data): self
    {
        $dataStore       = new self();
        $dataStore->data = $data;

        return $dataStore;
    }

    public function createId(): int
    {
        $this->lastUsedId++;

        return $this->lastUsedId;
    }

    public function save(int $id, array $data): void
    {
        $this->data[$id] = $data;
    }

    public function getById(int $id): array
    {
        if (! isset($this->data[$id])) {
            throw new OutOfBoundsException(sprintf('No data found for ID %d', $id));
        }

        return $this->data[$id];
    }

    public function delete(int $id): void
    {
        if (! isset($this->data[$id])) {
            throw new OutOfBoundsException(sprintf('No data found for ID %d', $id));
        }

        unset($this->data[$id]);
    }

    public function count(): int
    {
        return count($this->data);
    }

    public function all(): array
    {
        return $this->data;
    }

    public function where(string $matchKey, $matchValue): array
    {
        $resultArray = [];

        foreach ($this->data as $entry) {
            if ($entry[$matchKey] !== $matchValue) {
                continue;
            }

            $resultArray[] = $entry;
        }

        return $resultArray;
    }

    public function getKeys(): array
    {
        return array_keys($this->data);
    }

    public function random(): int
    {
        return array_rand($this->data);
    }

    public function shuffle(): void
    {
        shuffle($this->data);
    }
}
