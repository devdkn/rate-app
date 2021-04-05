<?php

declare(strict_types=1);

namespace App\Entity;

use App\ValueObject\CurrencyValue;
use App\ValueObject\RateValue;

class Rate
{
    /**
     * @var CurrencyValue
     */
    private $currencyFrom;

    /**
     * @var CurrencyValue
     */
    private $currencyTo;

    /**
     * @var RateValue
     */
    private $rate;

    /**
     * @var \DateTimeImmutable
     */
    private $receivedAt;

    /**
     * @param CurrencyValue      $from
     * @param CurrencyValue      $to
     * @param RateValue          $rate
     * @param \DateTimeImmutable $receivedAt
     */
    public function __construct(CurrencyValue $from, CurrencyValue $to, RateValue $rate, \DateTimeImmutable $receivedAt)
    {
        $this->currencyFrom = $from;
        $this->currencyTo = $to;
        $this->rate = $rate;
        $this->receivedAt = $receivedAt;
    }

    /**
     * @return CurrencyValue
     */
    public function getCurrencyFrom(): CurrencyValue
    {
        return $this->currencyFrom;
    }

    /**
     * @return CurrencyValue
     */
    public function getCurrencyTo(): CurrencyValue
    {
        return $this->currencyTo;
    }

    /**
     * @return RateValue
     */
    public function getRate(): RateValue
    {
        return $this->rate;
    }

    /**
     * @return \DateTimeImmutable
     */
    public function getReceivedAt(): \DateTimeImmutable
    {
        return $this->receivedAt;
    }
}
