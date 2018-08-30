<?php

namespace Somrlik\Tests;

use Http\Discovery\MessageFactoryDiscovery;
use Http\Discovery\StreamFactoryDiscovery;
use PHPUnit\Framework\Assert;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\RequestFactoryInterface;
use Somrlik\HelloBankApi\AjaxHandler;
use Somrlik\HelloBankApi\HelloBankApi;

class LoanCalculatorTest extends TestCase {

    /** @var HelloBankApi */
    private $api;

    /** @var RequestFactoryInterface */
    private $requestFactory;

    public function testLoanCalculator() {
        $loanCalculator = $this->api->getLoanCalculator();

        $barems = $this->api->getBarems();
        $insurance = $this->api->getInsurance();

        $baremId = key($barems);
        $insuranceId = key($insurance);

        $loanCalculator
            ->setBaremCode($baremId)
            ->setInsuranceCode($insuranceId)
            ->setTotalPrice(10000)
            ->setInstallmentCount(30);
        $resolved = $loanCalculator->resolve();

        $this->assertTrue(!! $resolved);
    }

    protected function setUp() {
        parent::setUp();
        $this->api = new HelloBankApi(
            $_ENV['PRODUCTION_MERCHANT_ID'],
            HelloBankApi::ENVIRONMENT_PRODUCTION
        );
        $this->requestFactory = MessageFactoryDiscovery::find();
    }

}
