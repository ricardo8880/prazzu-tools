<?php

namespace App\Core\Acquisition\Contracts;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface AcquisitionContextAdministration
{
    /** @return LengthAwarePaginator<int, array<string, mixed>> */
    public function paginate(?string $search, ?string $status, int $perPage = 15): LengthAwarePaginator;

    /** @return array<string, mixed>|null */
    public function find(int $id): ?array;

    /** @param array<string, mixed> $data */
    public function save(?int $id, array $data): int;

    public function toggle(int $id): bool;

    public function delete(int $id): void;

    /** @return list<array{slug:string,name:string}> */
    public function toolOptions(): array;

    /** @return list<array{slug:string,title:string}> */
    public function articleOptions(): array;
}
