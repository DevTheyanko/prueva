<?php
namespace Generated\Interfaces;

interface Test_tablRepositoryInterface
{
    public function getAll(): array;
    public function getAllWithRelations(): array;
    public function getById(int $id): ?array;
    public function create(array $data): bool;
    public function update(int $id, array $data): bool;
    public function delete(int $id): bool;
}