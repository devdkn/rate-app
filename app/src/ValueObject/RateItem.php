<?php

declare(strict_types=1);

namespace App\ValueObject;

final class RateItem
{
    /**
     * @var CurrencyValue
     */
    private $currency;

    /**
     * @var string
     */
    private $currencyName;

    /**
     * @var RateValue
     */
    private $rate;

    /**
     * @param CurrencyValue $currency
     * @param string        $currencyName
     * @param RateValue     $rate
     */
    public function __construct(CurrencyValue $currency, string $currencyName, RateValue $rate)
    {
        $this->currency = $currency;
        $this->rate = $rate;
        $this->currencyName = $currencyName;
    }

    /**
     * @return CurrencyValue
     */
    public function getCurrency(): CurrencyValue
    {
        return $this->currency;
    }

    /**
     * @return string
     */
    public function getCurrencyName(): string
    {
        return $this->currencyName;
    }

    /**
     * @return RateValue
     */
    public function getRate(): RateValue
    {
        return $this->rate;
    }
}
