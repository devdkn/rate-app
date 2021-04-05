<?php

declare(strict_types=1);

namespace App\Dto\Output;

class RateResponseItem
{
    /**
     * @var \DateTimeInterface
     */
    private $date;

    /**
     * @var float
     */
    private $rate;

    /**
     * @param \DateTimeInterface $date
     * @param float              $rate
     */
    public function __construct(\DateTimeInterface $date, float $rate)
    {
        $this->date = $date;
        $this->rate = $rate;
    }

    /**
     * @return \DateTimeInterface
     */
    public function getDate(): \DateTimeInterface
    {
        return $this->date;
    }

    /**
     * @return float
     */
    public function getRate(): float
    {
        return $this->rate;
    }
}
