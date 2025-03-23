<?php

namespace Dibi\ReposModelsGenerator\Contracts;

interface WriteRepo
{
    public function create(array $args);

    public function delete(string $uuid): bool;

    public function deleteBy(array $where): bool;

    public function insert(array $rows);

    public function insertGetId(array $args): int;

    public function update(string $uuid, array $args);

    public function updateBy(array $where, array $args): bool;

    public function updateOrCreate(array $where, array $args = []);

    public function updateOrInsert(array $where, array $args = []);

    public function upsert(array $args = [], array $uniqueColumns = []): int;
}
