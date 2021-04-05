<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\Currency;
use App\Repository\CurrencyRepositoryInterface;
use App\ValueObject\CurrencyValue;
use App\ValueObject\RateItem;

class CurrencyService
{
    /**
     * @var CurrencyRepositoryInterface
     */
    private $repository;

    /**
     * @param CurrencyRepositoryInterface $repository
     */
    public function __construct(CurrencyRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    /**
     * @param CurrencyValue $currencyFor
     * @param RateItem[]    $rateItems
     */
    public function update(CurrencyValue $currencyFor, array $rateItems): void
    {
        $currencies = [];
        foreach ($rateItems as $rateItem) {
            $currencies[] = new Currency($rateItem->getCurrency(), $rateItem->getCurrencyName());
        }

        $this->repository->addFor($currencyFor, $currencies);
    }

    /**
     * @param CurrencyValue $currency
     *
     * @return Currency[]
     */
    public function getAllFor(CurrencyValue $currency): array
    {
        return $this->repository->findFor($currency);
    }
}
