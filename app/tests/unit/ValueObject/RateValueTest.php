<?php

declare(strict_types=1);

namespace App\Tests\unit\ValueObject;

use App\ValueObject\RateValue;
use PHPUnit\Framework\TestCase;

class RateValueTest extends TestCase
{
    /**
     * @return array
     */
    public function getInvalidRateFormatTestCases(): array
    {
        return [
            [''],
            ['a'],
            ['a0.01'],
            ['-1'],
            ['0..01'],
            ['0.00000000001'],
        ];
    }

    /**
     * @dataProvider getInvalidRateFormatTestCases
     *
     * @param string $value
     */
    public function testInvalidRateFormat(string $value): void
    {
        $this->expectException(\LogicException::class);
        $this->expectExceptionMessage('Invalid rate format');

        new RateValue($value);
    }

    /**
     * @return array
     */
    public function getValidRateFormatTestCases(): array
    {
        return [
            ['0', '0'],
            ['0.00000', '0'],
            ['9999999999999999999.999999900', '9999999999999999999.9999999'],
            ['0000011.11111', '11.11111'],
            ['0000011000.000', '11000'],
        ];
    }

    /**
     * @dataProvider getValidRateFormatTestCases
     *
     * @param string $value
     * @param string $expectedValue
     *
     * @throws \PHPUnit\Framework\ExpectationFailedException
     */
    public function testValidRateFormat(string $value, string $expectedValue): void
    {
        $rateValue = new RateValue($value);
        self::assertSame($expectedValue, $rateValue->getValue());
    }

    public function testFromFloatInvalid(): void
    {
        $this->expectException(\LogicException::class);
        $this->expectExceptionMessage('Rate cannot be negative');

        RateValue::fromFloat(-0.00001);
    }

    /**
     * @return array
     */
    public function getFromFloatTestCases(): array
    {
        return [
            [0.0000000001, '0.0000000001'],
            [0.00000000001, '0'],
            [123.123, '123.123'],
            [12300.000, '12300'],
        ];
    }

    /**
     * @dataProvider getFromFloatTestCases
     *
     * @param float  $input
     * @param string $expectedValue
     */
    public function testFromFloat(float $input, string $expectedValue): void
    {
        $rate = RateValue::fromFloat($input);
        self::assertSame($expectedValue, $rate->getValue());
    }

    /**
     * @return array
     */
    public function getInverseTestCases(): array
    {
        return [
            ['0', '0'],
            ['1', '1'],
            ['2', '0.5'],
            ['10', '0.1'],
            ['9', '0.1111111111'],
        ];
    }

    /**
     * @dataProvider getInverseTestCases
     *
     * @param string $input
     * @param string $expectedValue
     */
    public function testInverse(string $input, string $expectedValue): void
    {
        $rate = new RateValue($input);
        self::assertSame($expectedValue, $rate->inverse()->getValue());
    }

    /**
     * @return array
     */
    public function getToFloatTestCases(): array
    {
        return [
            ['0.1', 0.1],
            ['0.0', 0],
            ['123123123.12312', 123123123.12312],
        ];
    }

    /**
     * @dataProvider getToFloatTestCases
     *
     * @param string $input
     * @param float  $expected
     *
     * @throws \PHPUnit\Framework\ExpectationFailedException
     */
    public function testToFloat(string $input, float $expected): void
    {
        $rate = new RateValue($input);
        self::assertSame($expected, $rate->toFloat());
    }
}
