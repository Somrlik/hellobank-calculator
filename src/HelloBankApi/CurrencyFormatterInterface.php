<?php

namespace Somrlik\HelloBankApi;

interface CurrencyFormatterInterface {

    /**
     * Formats currency in cents to be readable by humans
     *
     * @param int $money money in cents
     * @return string formatted human readable string
     */
    function formatCurrency(int $money): string;

}
