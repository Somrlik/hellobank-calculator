<?php

namespace Somrlik\Tests;

use PHPUnit\Framework\TestCase;
use Somrlik\HelloBankApi\CZKCurrencyFormatter;
use Somrlik\HelloBankApi\HelloBankApi;

class HelloBankApiTest extends TestCase {

    /** @var int */
    private $productionMerchantId;

    public function testConstructing() {
        $api = new HelloBankApi();
        $this->assertInstanceOf(CZKCurrencyFormatter::class, $api->getCurrencyFormatter());
        $this->assertSame(HelloBankApi::TEST_MERCHANT_ID, $api->getMerchantId());
        $this->assertSame(HelloBankApi::ENVIRONMENT_TEST, $api->getEnvironment());

        $api = new HelloBankApi(
            0,
            'test',
            null,
            null,
            null

        );
        $this->assertInstanceOf(CZKCurrencyFormatter::class, $api->getCurrencyFormatter());
        $this->assertSame(0, $api->getMerchantId());
        $this->assertSame(HelloBankApi::ENVIRONMENT_TEST, $api->getEnvironment());

        $api = new HelloBankApi(
            0,
            HelloBankApi::ENVIRONMENT_PRODUCTION,
            null,
            null,
            null
        );
        $this->assertInstanceOf(CZKCurrencyFormatter::class, $api->getCurrencyFormatter());
        $this->assertSame(0, $api->getMerchantId());
        $this->assertSame(HelloBankApi::ENVIRONMENT_PRODUCTION, $api->getEnvironment());
    }

    public function testBaremsGetDummy() {
        $api = new HelloBankApi(
            0,
            HelloBankApi::ENVIRONMENT_DUMMY
        );
        $this->assertInstanceOf(CZKCurrencyFormatter::class, $api->getCurrencyFormatter());
        $this->assertSame(0, $api->getMerchantId());
        $this->assertSame(HelloBankApi::ENVIRONMENT_DUMMY, $api->getEnvironment());

        $barems = $api->getBarems();

        $this->assertNotFalse($barems);
        $this->assertSame($barems[100]->limit->maximumSizeOfLoan, 400000);
        $this->assertSame($barems[623]->name, 'Bez navýšení 10% + 9 x 10%');
    }

    /**
     * @throws \Somrlik\HelloBankApi\InvalidRequestException
     */
    public function testBaremsGetTest() {
        $api = new HelloBankApi(
            HelloBankApi::TEST_MERCHANT_ID,
            HelloBankApi::ENVIRONMENT_TEST,
            null,
            null,
            null
        );

        $barems = $api->getBarems();
        $this->assertNotFalse($barems);

        $insurance = $api->getInsurance();
        $this->assertNotFalse($insurance);

        $incomeType = $api->getIncomeType();
        $this->assertNotFalse($incomeType);
    }

    public function testBaremsGetProduction() {
        $api = new HelloBankApi(
            $this->productionMerchantId,
            HelloBankApi::ENVIRONMENT_PRODUCTION,
            null,
            null,
            null
        );

        $barems = $api->getBarems();
        $this->assertNotFalse($barems);

        $insurance = $api->getInsurance();
        $this->assertNotFalse($insurance);

        $incomeType = $api->getIncomeType();
        $this->assertNotFalse($incomeType);
    }

    protected function setUp() {
        parent::setUp();
        $this->productionMerchantId = $_ENV['PRODUCTION_MERCHANT_ID'];
    }

}
