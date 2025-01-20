<?php

declare(strict_types=1);

namespace App\Contracts\LawnCare;

interface CareData
{
    /**
     * @param  array<string, mixed>  $data
     */
    public static function from(array $data): self;

    /**
     * convert the data to array
     *
     * @return array<string, mixed>
     */
    public function toArray(): array;
}
