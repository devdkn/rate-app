<?php

declare(strict_types=1);

namespace App\ResponseBuilder;

use App\Dto\Output\RateResponseItem;
use App\Entity\Rate;

class RateResponseBuilder
{
    /**
     * @param Rate[] $rates
     *
     * @return RateResponseItem[]
     */
    public function createResponse(array $rates): array
    {
        $res = [];
        foreach ($rates as $rate) {
            $res[] = new RateResponseItem(
                $rate->getReceivedAt(),
                $rate->getRate()->toFloat()
            );
        }

        return $res;
    }
}
