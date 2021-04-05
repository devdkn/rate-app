<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Rate;
use App\ValueObject\CurrencyValue;

interface HourlyRateRepositoryInterface
{
    /**
     * @param Rate[] $rateItems
     */
    public function persistBatch(array $rateItems): void;

    /**
     * @param CurrencyValue           $from
     * @param CurrencyValue           $to
     * @param \DateTimeInterface|null $since
     * @param \DateTimeInterface|null $until
     *
     * @return Rate[]
     */
    public function findRates(CurrencyValue $from, CurrencyValue $to, ?\DateTimeInterface $since, ?\DateTimeInterface $until): array;
}
