<?php

namespace Dibi\ReposModelsGenerator\Contracts;

use Illuminate\Support\Collection;

interface ReadRepo
{
    public function all(array $columns = ['*']): Collection;

    public function exists(array $where): bool;

    public function findBy(string $field, int|string|bool $value);

    public function findById(string $uuid);

    public function findByIdOrFail(string $uuid);

    public function findByMany(string $field, array $values): Collection;

    public function findByManyIds(array $uuids): Collection;

    public function findByOrFail(string $field, int|string|bool $value);

    public function findManyBy(string $field, int|string|bool $value): Collection;

    public function getBy(array $args, array $columns = ['*']): Collection;
}
