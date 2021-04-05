<?php

declare(strict_types=1);

namespace App\Service\RateProvider;

use App\Service\RateProvider\Exception\RateProviderFailedException;
use App\ValueObject\CurrencyValue;
use App\ValueObject\RateItem;
use App\ValueObject\RateValue;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\Exception\GuzzleException;

class BitpayRateProvider implements RateProviderInterface
{
    /**
     * @var ClientInterface
     */
    private $guzzleClient;

    /**
     * @var string
     */
    private $apiUrl;

    /**
     * @var string
     */
    private $currencyPlaceholder;

    /**
     * @param ClientInterface $guzzleClient
     * @param string          $apiUrl
     * @param string          $currencyPlaceholder
     */
    public function __construct(ClientInterface $guzzleClient, string $apiUrl, string $currencyPlaceholder)
    {
        $this->guzzleClient = $guzzleClient;
        $this->apiUrl = $apiUrl;
        $this->currencyPlaceholder = $currencyPlaceholder;
    }

    /**
     * {@inheritdoc}
     */
    public function provideRates(CurrencyValue $currency): array
    {
        try {
            $res = $this->guzzleClient->request(
                'GET',
                \str_replace($this->currencyPlaceholder, $currency->getValue(), $this->apiUrl)
            );
        } catch (GuzzleException $e) {
            throw new RateProviderFailedException('Rate provider failed: ' . $e->getMessage());
        }

        $body = \trim((string) $res->getBody());
        if ($body !== '') {
            $decoded = \json_decode($body, true);
            if (\is_array($decoded)) {
                return $this->createItems($decoded, $currency);
            }
            throw new \LogicException('Unexpected return value type from BitPay. Expected array, got ' . $body);
        }

        return [];
    }

    /**
     * @param array[]       $rawBitPayList
     * @param CurrencyValue $currency
     *
     * @return RateItem[]
     */
    private function createItems(array $rawBitPayList, CurrencyValue $currency): array
    {
        $res = [];
        foreach ($rawBitPayList as $item) {
            if (!isset($item['code']) || !\is_string($item['code'])) {
                throw new \LogicException('Invalid item format: the "code" key is missing or is not string.');
            }
            $currentCurrency = new CurrencyValue($item['code']);
            if ($currentCurrency->isSame($currency)) {
                continue;
            }
            if (!isset($item['rate']) || (!\is_float($item['rate']) && !\is_int($item['rate']))) {
                throw new \LogicException('Invalid item format: the "rate" key is missing or is not float or int.');
            }
            if (!isset($item['name']) || !\is_String($item['name'])) {
                throw new \LogicException('Invalid item format: the "name" key is missing or is not string');
            }
            $res[] = new RateItem($currentCurrency, $item['name'], RateValue::fromFloat($item['rate']));
        }

        return $res;
    }
}
