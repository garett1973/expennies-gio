<?php

namespace App\DataObjects;

class DataTableQueryParams
{

    /**
     * @param int $start
     * @param int $length
     * @param string $orderBy
     * @param string $orderDirection
     * @param string $searchValue
     * @param int $draw
     */
    public function __construct(
        public readonly int $start,
        public readonly int $length,
        public readonly string $orderBy,
        public readonly string $orderDirection,
        public readonly string $searchValue,
        public readonly int $draw)
    {
    }
}