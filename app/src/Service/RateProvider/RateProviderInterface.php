<?php

declare(strict_types=1);

namespace App\Service\RateProvider;

use App\Service\RateProvider\Exception\RateProviderFailedException;
use App\ValueObject\CurrencyValue;
use App\ValueObject\RateItem;

interface RateProviderInterface
{
    /**
     * @param CurrencyValue $currency
     *
     * @return RateItem[]
     * @throws RateProviderFailedException
     */
    public function provideRates(CurrencyValue $currency): array;
}
