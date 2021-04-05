<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Currency;
use App\ValueObject\CurrencyValue;

interface CurrencyRepositoryInterface
{
    /**
     * @param CurrencyValue $currencyFor
     * @param Currency[]    $currencies
     */
    public function addFor(CurrencyValue $currencyFor, array $currencies): void;

    /**
     * @param CurrencyValue $currency
     *
     * @return Currency[]
     */
    public function findFor(CurrencyValue $currency): array;
}
