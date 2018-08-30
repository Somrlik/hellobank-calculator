<?php

namespace Somrlik\HelloBankApi;

use Nette\InvalidArgumentException;
use Nette\InvalidStateException;

class LoanCalculator {

    /**
     * @var HelloBankApi
     */
    private $api;

    /**
     * @var bool
     */
    private $isResolved;

    /** @var mixed */
    private $baremCode;

    /** @var mixed */
    private $insuranceCode;

    /** @var mixed */
    private $totalPrice;

    /** @var mixed */
    private $installmentCount;

    /** @var mixed */
    private $firstInstallment;

    /** @var mixed */
    private $loanMoney;

    /** @var mixed */
    private $firstInstallmentPause;

    /** @var mixed */
    private $installmentAmount;

    /** @var array */
    private $response = [];

    /** @var string */
    private $url;

    public function __construct(HelloBankApi $api, string $url) {
        $this->api = $api;
        $this->url = $url;
    }

    const REQUIRED_PARAMETERS = [
        'kodBaremu' => 'baremCode',
        'kodPojisteni' => 'insuranceCode',
        'cenaZbozi' => 'totalPrice',
        'pocetSplatek' => 'installmentCount',
    ];

    const OPTIONAL_PARAMETERS = [
        'primaPlatba' => 'firstInstallment',
        'vyseUveru' => 'loanMoney',
        'odklad' => 'firstInstallmentPause',
        'vyseSplatky' => 'installmentAmount',
    ];

    /**
     * Resolves the required loan in API
     */
    public function resolve() {

        if (! $this->isResolved) {
            $requestParameters = [];

            foreach (self::REQUIRED_PARAMETERS as $apiKey => $localKey) {
                $value = $this->{$localKey};
                if (empty($value)) {
                    throw new InvalidArgumentException("You must supply the {$apiKey}/{$localKey} parameter.");
                }
                $requestParameters[$apiKey] = $value;
            }

            foreach (self::OPTIONAL_PARAMETERS as $apiKey => $localKey) {
                $value = $this->{$localKey};
                if (! empty($value)) {
                    $requestParameters[$apiKey] = $value;
                }
            }

            $query = http_build_query($requestParameters);
            $url = $this->url . '&' . $query;
            try {
                $resource = $this->api->getResourceFromUrl($url);
                $this->response = XmlParser::parseLoanCalculatorResponse($resource);
            } catch (\Exception $e) {
                throw new InvalidStateException("Failed to get url {$url}");
            }

            $this->isResolved = true;
        }

        return $this->response;
    }

    /**
     * @return bool
     */
    public function isResolved(): bool {
       return $this->isResolved;
    }

    /**
     * @param mixed $baremCode
     * @return LoanCalculator
     */
    public function setBaremCode($baremCode) {
        $this->baremCode = $baremCode;
        return $this;
    }

    /**
     * @param mixed $insuranceCode
     * @return LoanCalculator
     */
    public function setInsuranceCode($insuranceCode) {
        $this->insuranceCode = $insuranceCode;
        return $this;
    }

    /**
     * @param mixed $totalPrice
     * @return LoanCalculator
     */
    public function setTotalPrice($totalPrice) {
        $this->totalPrice = $totalPrice;
        return $this;
    }

    /**
     * @param mixed $installmentCount
     * @return LoanCalculator
     */
    public function setInstallmentCount($installmentCount) {
        $this->installmentCount = $installmentCount;
        return $this;
    }

    /**
     * @param mixed $firstInstallment
     * @return LoanCalculator
     */
    public function setFirstInstallment($firstInstallment) {
        $this->firstInstallment = $firstInstallment;
        return $this;
    }

    /**
     * @param mixed $loanMoney
     * @return LoanCalculator
     */
    public function setLoanMoney($loanMoney) {
        $this->loanMoney = $loanMoney;
        return $this;
    }

    /**
     * @param mixed $firstInstallmentPause
     * @return LoanCalculator
     */
    public function setFirstInstallmentPause($firstInstallmentPause) {
        $this->firstInstallmentPause = $firstInstallmentPause;
        return $this;
    }

    /**
     * @param mixed $installmentAmount
     * @return LoanCalculator
     */
    public function setInstallmentAmount($installmentAmount) {
        $this->installmentAmount = $installmentAmount;
        return $this;
    }

}
