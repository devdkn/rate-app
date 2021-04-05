<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\Rate;
use App\Repository\HourlyRateRepositoryInterface;
use App\ValueObject\CurrencyValue;
use App\ValueObject\RateItem;

class HourlyRateService
{
    /**
     * @var HourlyRateRepositoryInterface
     */
    private $repository;

    /**
     * @param HourlyRateRepositoryInterface $repository
     */
    public function __construct(HourlyRateRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    /**
     * @param CurrencyValue      $currency
     * @param \DateTimeImmutable $updateDate
     * @param RateItem[]         $rateItems
     */
    public function addRates(CurrencyValue $currency, \DateTimeImmutable $updateDate, array $rateItems): void
    {
        $rates = [];
        foreach ($rateItems as $rateItem) {
            $rate = $rateItem->getRate();
            $rates[] = new Rate($currency, $rateItem->getCurrency(), $rate, $updateDate);
            $rates[] = new Rate($rateItem->getCurrency(), $currency, $rate->inverse(), $updateDate);
        }

        $this->repository->persistBatch($rates);
    }

    /**
     * @param CurrencyValue           $from
     * @param CurrencyValue           $to
     * @param \DateTimeInterface|null $since
     * @param \DateTimeInterface|null $until
     *
     * @return Rate[]
     */
    public function findRates(CurrencyValue $from, CurrencyValue $to, ?\DateTimeInterface $since, ?\DateTimeInterface $until): array
    {
        return $this->repository->findRates($from, $to, $since, $until);
    }
}
