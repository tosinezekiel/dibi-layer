<?php

namespace Dibi\ReposModelsGenerator\Contracts;

use Illuminate\Support\Collection;
use Illuminate\Database\Eloquent\Builder;

interface ReadRepo
{
    public function all(array $columns = ['*']): Collection;

    public function exists(array $where): bool;

    public function findBy(string $field, int|string|bool $value);

    public function findById(int $id);

    public function findByIdOrFail(int $id);

    public function findByMany(string $field, array $values): Collection;

    public function findByManyIds(array $ids): Collection;

    public function findByOrFail(string $field, int|string|bool $value);

    public function findManyBy(string $field, int|string|bool $value): Collection;

    public function getBy(array $args, array $columns = ['*']): Collection;

    public function query(): Builder;
}
