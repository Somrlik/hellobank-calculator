<?php

namespace Somrlik\Tests;

use PHPUnit\Framework\TestCase;
use Somrlik\HelloBankApi\CZKCurrencyFormatter;

class CZKCurrencyFormatterTest extends TestCase {

    public function dataProvider() {
        return [
            [10, '0 Kč'],
            [100, '1 Kč'],
            [1000, '10 Kč'],
            [1337, '13 Kč'],
            [1999, '20 Kč'],
            [2049, '20 Kč'],
            [1000000, '10 000 Kč'],
            [1000000000, '10 000 000 Kč'],
        ];
    }

    /**
     * @dataProvider dataProvider
     * @param $number
     * @param $result
     */
    public function testFormatCurrency($number, $result) {
        $formatter = new CZKCurrencyFormatter();
        $this->assertEquals($result, $formatter->formatCurrency($number));
    }

}
