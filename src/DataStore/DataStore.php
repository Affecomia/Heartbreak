<?php

declare(strict_types=1);

namespace Heartbreak\DataStore;

interface DataStore
{
    public static function fromArray(array $data): self;

    public function createId(): int;

    public function save(int $id, array $data): void;

    public function getById(int $id): array;

    public function getKeys(): array;

    public function delete(int $id): void;

    public function count(): int;

    public function random(): int;

    public function shuffle(): void;

    public function all(): array;

    public function where(string $key, $value): array;
}
