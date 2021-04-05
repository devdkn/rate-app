<?php

declare(strict_types=1);

namespace App\ResponseBuilder;

use App\Dto\Output\CurrencyItem;
use App\Entity\Currency;

class CurrencyResponseBuilder
{
    /**
     * @param Currency[] $items
     *
     * @return CurrencyItem[]
     */
    public function createResponse(array $items): array
    {
        $res = [];
        foreach ($items as $item) {
            $res[] = new CurrencyItem($item->getCode()->getValue(), $item->getName());
        }

        return $res;
    }
}
