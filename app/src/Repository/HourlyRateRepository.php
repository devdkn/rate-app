<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Rate;
use App\ValueObject\CurrencyValue;
use App\ValueObject\RateValue;

class HourlyRateRepository implements HourlyRateRepositoryInterface
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
     * @param Rate[] $rateItems
     */
    public function persistBatch(array $rateItems): void
    {
        $pipe = $this->redis->pipeline();

        foreach ($rateItems as $rateItem) {
            $itemDate = $this->normalizeDate($rateItem->getReceivedAt());

            $key = $this->generateKey($rateItem->getCurrencyFrom(), $rateItem->getCurrencyTo());

            $time = (string) $itemDate->getTimestamp();

            $this->addItem($pipe, $key, $time, $time . ':' . $rateItem->getRate()->getValue());
        }

        $pipe->exec();
    }

    /**
     * @param \Redis $client
     * @param string $key
     * @param string $score
     * @param string $item
     */
    private function addItem(\Redis $client, string $key, string $score, string $item): void
    {
        $client->eval(
            $this->getAddScript(),
            [
                $key,
                $score,
                $item,
            ],
            1
        );
    }

    /**
     * @return string
     */
    private function getAddScript(): string
    {
        return <<<'LUA'
redis.replicate_commands();
if #redis.call("ZRANGEBYSCORE", KEYS[1], ARGV[1], ARGV[1]) == 0 then
return redis.call("ZADD", KEYS[1], ARGV[1], ARGV[2])
end
return 0
LUA;
    }

    /**
     * @param CurrencyValue           $from
     * @param CurrencyValue           $to
     * @param \DateTimeInterface|null $since
     * @param \DateTimeInterface|null $until
     *
     * @return Rate[]
     */
    public function findRates(CurrencyValue $from, CurrencyValue $to, ?\DateTimeInterface $since, ?\DateTimeInterface $until): array
    {
        $redisRes = $this->redis->zRangeByScore(
            $this->generateKey($from, $to),
            $since !== null ? (string) $since->getTimestamp() : '0',
            $until !== null ? (string) $until->getTimestamp() : '+inf'
        );
        $res = [];
        foreach ($redisRes as $v) {
            [$time, $rate] = \explode(':', $v);
            /** @var null|string $rate Rate can be null if $v does not have a colon */
            if ($rate === null) {
                throw new \LogicException('Invalid item format in Redis: ' . $v);
            }

            $res[] = new Rate(
                $from,
                $to,
                new RateValue($rate),
                new \DateTimeImmutable('@' . $time)
            );
        }

        return $res;
    }

    /**
     * @param \DateTimeImmutable $itemRawDate
     *
     * @return \DateTimeImmutable
     */
    private function normalizeDate(\DateTimeImmutable $itemRawDate): \DateTimeImmutable
    {
        return $itemRawDate->setTime((int) $itemRawDate->format('H'), 0, 0);
    }

    /**
     * @param CurrencyValue $from
     * @param CurrencyValue $to
     *
     * @return string
     */
    private function generateKey(CurrencyValue $from, CurrencyValue $to): string
    {
        return 'r:' . $from->getValue() . ':' . $to->getValue() . ':h';
    }
}
