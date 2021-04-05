<?php

declare(strict_types=1);

namespace App\Service;

use App\Exception\RateUpdateFailedException;
use App\Service\RateProvider\RateProviderInterface;
use App\ValueObject\CurrencyValue;

class RateUpdateService
{
    /**
     * @var RateProviderInterface
     */
    private $rateProvider;

    /**
     * @var HourlyRateService
     */
    private $hourlyRateService;

    /**
     * @var CurrencyService
     */
    private $currencyService;

    /**
     * @param RateProviderInterface $rateProvider
     * @param HourlyRateService     $hourlyRateService
     * @param CurrencyService       $currencyService
     */
    public function __construct(
        RateProviderInterface $rateProvider,
        HourlyRateService $hourlyRateService,
        CurrencyService $currencyService
    ) {
        $this->rateProvider = $rateProvider;
        $this->hourlyRateService = $hourlyRateService;
        $this->currencyService = $currencyService;
    }

    /**
     * @param CurrencyValue      $currency
     * @param \DateTimeImmutable $updateDate
     *
     * @throws RateUpdateFailedException
     */
    public function updateRates(CurrencyValue $currency, \DateTimeImmutable $updateDate): void
    {
        try {
            $rates = $this->rateProvider->provideRates($currency);
        } catch (RateProvider\Exception\RateProviderFailedException $e) {
            throw new RateUpdateFailedException(
                'Rate update failed (' . $e->getCode() .'): ' . $e->getMessage(),
                0,
                $e
            );
        }

        $this->hourlyRateService->addRates($currency, $updateDate, $rates);
        $this->currencyService->update($currency, $rates);
    }
}
