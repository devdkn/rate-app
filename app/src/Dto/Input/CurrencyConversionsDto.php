<?php

declare(strict_types=1);

namespace App\Dto\Input;

use App\ValueObject\CurrencyValue;
use Symfony\Component\Validator\Constraints as Assert;

class CurrencyConversionsDto
{
    /**
     * @Assert\NotIdenticalTo("")
     *
     * @var string
     */
    private $currency;

    /**
     * @param string $currency
     */
    public function __construct(string $currency)
    {
        $this->currency = $currency;
    }

    /**
     * @return CurrencyValue
     */
    public function getCurrencyValue(): CurrencyValue
    {
        return new CurrencyValue($this->currency);
    }
}
