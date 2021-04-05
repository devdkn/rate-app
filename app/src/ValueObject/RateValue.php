<?php

declare(strict_types=1);

namespace App\ValueObject;

final class RateValue
{
    public const MAX_DECIMALS = 10;

    /**
     * @var string
     */
    private $value;

    /**
     * @param string $value
     */
    public function __construct(string $value)
    {
        if (!\preg_match('/^[0-9]+(?:\.[0-9]{1,' . self::MAX_DECIMALS . '})?$/', $value)) {
            throw new \LogicException('Invalid rate format');
        }
        if (\strpos($value, '.') !== false) {
            $value = \rtrim(\rtrim($value, '0'), '.');
        }
        $this->value = \ltrim($value, '0');
        if ($this->value === '') {
            $this->value = '0';
        } elseif ($this->value[0] === '.') {
            $this->value = '0' . $this->value;
        }
    }

    /**
     * @param float $rate
     *
     * @return static
     */
    public static function fromFloat(float $rate): self
    {
        if ($rate < 0) {
            throw new \LogicException('Rate cannot be negative');
        }
        return new self(
            \number_format($rate, self::MAX_DECIMALS, '.', '')
        );
    }

    /**
     * @return static
     */
    public function inverse(): self
    {
        if ($this->value === '0') {
            $newValue = '0';
        } else {
            $newValue = \bcdiv('1', $this->value, self::MAX_DECIMALS);
            if ($newValue === null) {
                throw new \LogicException('Unexpected null from bcdiv()');
            }
        }
        return new RateValue($newValue);
    }

    /**
     * @return string
     */
    public function getValue(): string
    {
        return $this->value;
    }

    /**
     * @return float
     */
    public function toFloat(): float
    {
        return (float) $this->value;
    }
}
