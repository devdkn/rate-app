<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\Currency;
use App\ValueObject\CurrencyValue;

class ApplicationCurrencyService
{
    /**
     * @var Currency[]
     */
    private $supported = [];

    /**
     * @param string[] $supported
     *
     * @phpstan-param array<string, string> $supported
     */
    public function __construct(array $supported)
    {
        foreach ($supported as $k => $v) {
            $this->supported[] = new Currency(new CurrencyValue($k), $v);
        }
    }

    /**
     * @return Currency[]
     */
    public function getSupported(): array
    {
        return $this->supported;
    }
}
