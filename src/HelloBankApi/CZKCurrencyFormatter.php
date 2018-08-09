<?php

namespace Somrlik\HelloBankApi;

final class CZKCurrencyFormatter implements CurrencyFormatterInterface {

    /**
     * {@inheritdoc}
     */
    function formatCurrency(int $money): string
    {
        return number_format($money / 100, 0, '.', ' ') . ' Kč';
    }
}
