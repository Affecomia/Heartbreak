<?php

declare(strict_types=1);

namespace Heartbreak\Helpers;

use InvalidArgumentException;

final class Id
{
    private int $id;

    public static function fromInt(int $id): Id
    {
        self::validate($id);

        return new self($id);
    }

    private function __construct(int $id)
    {
        $this->id = $id;
    }

    public function toInt(): int
    {
        return $this->id;
    }

    private static function validate(int $id): void
    {
        if ($id <= 0) {
            throw new InvalidArgumentException('Invalid ID given');
        }
    }
}
