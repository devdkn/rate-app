<?php

declare(strict_types=1);

namespace App\Tests\integration\Repository;

use App\Entity\Rate;
use App\Repository\HourlyRateRepository;
use App\Repository\HourlyRateRepositoryInterface;
use App\ValueObject\CurrencyValue;
use App\ValueObject\RateValue;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Snc\RedisBundle\Client\Phpredis\Client;

class HourlyRateRepositoryTest extends KernelTestCase
{
    /**
     * @var Client
     */
    private $client;

    /**
     * {@inheritdoc}
     */
    public static function setUpBeforeClass(): void
    {
        if (!self::$kernel) {
            static::bootKernel();
        }
    }

    /**
     * {@inheritdoc}
     */
    protected function tearDown(): void
    {
        $this->client->flushDB();
    }

    /**
     * {@inheritdoc}
     */
    protected function setUp(): void
    {
        parent::setUp();
        if (!$this->client) {
            $this->client = self::$container->get('snc_redis.default');
        }

        $this->client->flushDB();
    }

    public function testPersistAndFind(): void
    {
        $repository = $this->getRepository();

        $from = new CurrencyValue('1');
        $to = new CurrencyValue('2');

        $t1 = new \DateTimeImmutable('2000-01-01T00:00:00.0Z');
        $t2 = new \DateTimeImmutable('2000-01-01T00:59:59.0Z');
        $t3 = new \DateTimeImmutable('2000-01-01T01:00:00.0Z');

        self::assertCount(0, $repository->findRates($from, $to, null, null));
        self::assertCount(0, $repository->findRates($to, $from, null, null));

        $repository->persistBatch(
            [
                new Rate(
                    $from,
                    $to,
                    new RateValue('1'),
                    $t1
                ),
                new Rate(
                    $from,
                    $to,
                    new RateValue('2'),
                    $t2
                ),
                new Rate(
                    $from,
                    $to,
                    new RateValue('3'),
                    $t3
                ),
                new Rate(
                    $to,
                    $from,
                    new RateValue('5'),
                    $t1
                )
            ]
        );

        $resDirect = $repository->findRates($from, $to, null, null);
        self::assertCount(2, $resDirect);

        self::assertSame($from->getValue(), $resDirect[0]->getCurrencyFrom()->getValue());
        self::assertSame($to->getValue(), $resDirect[0]->getCurrencyTo()->getValue());
        self::assertSame('1', $resDirect[0]->getRate()->getValue());
        self::assertSame($t1->getTimestamp(), $resDirect[0]->getReceivedAt()->getTimestamp());

        self::assertSame($from->getValue(), $resDirect[1]->getCurrencyFrom()->getValue());
        self::assertSame($to->getValue(), $resDirect[1]->getCurrencyTo()->getValue());
        self::assertSame('3', $resDirect[1]->getRate()->getValue());
        self::assertSame($t3->getTimestamp(), $resDirect[1]->getReceivedAt()->getTimestamp());

        $resDirectUntil = $repository->findRates($from, $to, null, $t1);
        self::assertCount(1, $resDirectUntil);
        self::assertSame('1', $resDirectUntil[0]->getRate()->getValue());

        $resDirectSince = $repository->findRates($from, $to, $t3, null);
        self::assertCount(1, $resDirectSince);
        self::assertSame('3', $resDirectSince[0]->getRate()->getValue());

        $resDirectSinceUntil = $repository->findRates($from, $to, $t1, $t3);
        self::assertCount(2, $resDirectSinceUntil);
        self::assertSame('1', $resDirectSinceUntil[0]->getRate()->getValue());
        self::assertSame('3', $resDirectSinceUntil[1]->getRate()->getValue());

        $resDirectSinceUntilInvalid = $repository->findRates($from, $to, $t3, $t1);
        self::assertCount(0, $resDirectSinceUntilInvalid);

        $resInverse = $repository->findRates($to, $from, null, null);
        self::assertCount(1, $resInverse);
        self::assertSame($to->getValue(), $resInverse[0]->getCurrencyFrom()->getValue());
        self::assertSame($from->getValue(), $resInverse[0]->getCurrencyTo()->getValue());
        self::assertSame('5', $resInverse[0]->getRate()->getValue());
        self::assertSame($t1->getTimestamp(), $resInverse[0]->getReceivedAt()->getTimestamp());
    }

    /**
     * @return HourlyRateRepositoryInterface
     */
    private function getRepository(): HourlyRateRepositoryInterface
    {
        return self::$container->get(HourlyRateRepositoryInterface::class);
    }
}
