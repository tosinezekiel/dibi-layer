<?php

namespace Dibi\ReposModelsGenerator\Repositories\Write;

use Illuminate\Database\Eloquent\Builder;
use Dibi\ReposModelsGenerator\Contracts\WriteRepo;

abstract class Repository implements WriteRepo
{
    const ID_FIELD = 'id';

    public function create(array $args)
    {
        return $this->model()->create($args);
    }

    public function delete(int $id): bool
    {
        return $this->model()->where('id', '=', $id)->delete();
    }

    public function deleteBy(array $where): bool
    {
        return $this->model()->where($where)->delete();
    }

    public function insert(array $rows)
    {
        return $this->model()->insert($rows);
    }

    public function insertGetId(array $args): int
    {
        return $this->model()->insertGetId($args);
    }

    public function update(int $id, array $args): bool
    {
        return $this->model()->where('id', '=', $id)->update($args);
    }

    public function updateBy(array $where, array $args): bool
    {
        return $this->model()->where($where)->update($args);
    }

    public function updateOrCreate(array $where, array $args = [])
    {
        return $this->model()->updateOrCreate($where, $args);
    }

    public function updateOrInsert(array $where, array $args = []): Builder
    {
        return $this->model()->updateOrInsert($where, $args);
    }

    public function upsert(array $args = [], array $uniqueColumns = []): int
    {
        return $this->model()->upsert($args, $uniqueColumns);
    }

    abstract protected function model();
}
