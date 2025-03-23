<?php

namespace Dibi\ReposModelsGenerator\Repositories\Read;

use Illuminate\Support\Collection;
use Illuminate\Database\Eloquent\Builder;
use Dibi\ReposModelsGenerator\Contracts\ReadRepo;

/**
 * @template-covariant A
 */
abstract class Repository implements ReadRepo
{
    const ID_FIELD = 'id';

    public function __construct(private $repo = null)
    {
    }

    public function all(array $columns = ['*']): Collection
    {
        if ($this->repo && $result = $this->repo->all($columns)) {
            return $result;
        }

        return $this->model()->all($columns);
    }

    public function exists(array $where): bool
    {
        return $this->model()->where($where)->exists();
    }

    public function findBy(string $field, int|string|bool $value)
    {
        return $this->model()->where($field, '=', $value)->first();
    }

    public function findById(int $id)
    {
        return $this->findBy(self::ID_FIELD, $id);
    }

    public function findByIdOrFail(int $id)
    {
        return $this->findByOrFail(self::ID_FIELD, $id);
    }

    public function findByMany(string $field, array $values): Collection
    {
        if (isset($this->repo) && $result = $this->repo->findManyBy($field, $values)) {
            return $result;
        }

        return $this->model()->whereIn($field, $values)->get();
    }

    public function findByManyIds(array $ids): Collection
    {
        if ($this->repo && $result = $this->repo->findManyById($ids)) {
            return $result;
        }

        return $this->model()->find($ids);
    }

    public function findByOrFail(string $field, int|string|bool $value)
    {
        return $this->model()->where($field, '=', $value)->firstOrFail();
    }

    public function findManyBy(string $field, int|string|bool $value): Collection
    {
        return $this->model()->where($field, '=', $value)->get();
    }

    public function getBy(array $args, array $columns = ['*']): Collection
    {
        return $this->model()->where($args)->get($columns);
    }

    public function query(array $args, array $columns = ['*']): Builder
    {
        return $this->model()->query();
    }

    abstract protected function model();
}
