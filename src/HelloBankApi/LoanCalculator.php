<?php

namespace Somrlik\HelloBankApi;

class LoanCalculator {

    /**
     * @var HelloBankApi
     */
    private $api;

    /**
     * @var bool
     */
    private $isResolved;

    public function __construct(HelloBankApi $api) {
        $this->api = $api;
    }



    /**
     * Resolves the required loan in API
     */
    public function resolve() {

    }

    /**
     * @return bool
     */
    public function isResolved(): bool {
       return $this->isResolved;
    }
}
