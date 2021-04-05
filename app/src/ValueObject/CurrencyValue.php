<?php

declare(strict_types=1);

namespace App\ValueObject;

final class CurrencyValue
{
    /**
     * @var string
     */
    private $value;

    /**
     * @param string $value
     */
    public function __construct(string $value)
    {
        $this->value = \strtoupper($value);
    }

    /**
     * @return string
     */
    public function getValue(): string
    {
        return $this->value;
    }

    /**
     * @param CurrencyValue $currency
     *
     * @return bool
     */
    public function isSame(CurrencyValue $currency): bool
    {
        return $this->value === $currency->getValue();
    }
}
