<?php

declare(strict_types=1);

namespace App\Entity;

use App\ValueObject\CurrencyValue;

class Currency
{
    /**
     * @var CurrencyValue
     */
    private $code;

    /**
     * @var string
     */
    private $name;

    /**
     * @param CurrencyValue $code
     * @param string $name
     */
    public function __construct(CurrencyValue $code, string $name)
    {
        $this->code = $code;
        $this->name = $name;
    }

    /**
     * @return CurrencyValue
     */
    public function getCode(): CurrencyValue
    {
        return $this->code;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }
}
