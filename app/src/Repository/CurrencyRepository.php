<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Currency;
use App\ValueObject\CurrencyValue;

class CurrencyRepository implements CurrencyRepositoryInterface
{
    /**
     * @var \Redis
     */
    private $redis;

    /**
     * @param \Redis $redis
     */
    public function __construct(\Redis $redis)
    {
        $this->redis = $redis;
    }

    /**
     * {@inheritdoc}
     */
    public function addFor(CurrencyValue $currencyFor, array $currencies): void
    {
        $set = [];
        foreach ($currencies as $currency) {
            $set[$currency->getCode()->getValue()] = $currency->getName();
        }

        $this->redis->hMSet($this->generateKey($currencyFor), $set);
    }

    /**
     * {@inheritdoc}
     */
    public function findFor(CurrencyValue $currency): array
    {
        $redisRes = $this->redis->hGetAll($this->generateKey($currency));

        $res = [];
        foreach ($redisRes as $code => $name) {
            $res[] = new Currency(new CurrencyValue($code), $name);
        }

        return $res;
    }

    /**
     * @param CurrencyValue $currency
     *
     * @return string
     */
    private function generateKey(CurrencyValue $currency): string
    {
        return 'c:' . $currency->getValue();
    }
}
