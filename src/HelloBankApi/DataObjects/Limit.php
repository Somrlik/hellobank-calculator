<?php

namespace Somrlik\HelloBankApi\DataObjects;

class Limit implements \JsonSerializable {

    /**
     * minimalni vyse uveru
     * @var int
     */
    public $minimumSizeOfLoan;

    /**
     * maximalni vyse uveru
     * @var int
     */
    public $maximumSizeOfLoan;

    /**
     * minimalni pocet splatek
     * @var int
     */
    public $minimumNumberOfInstallments;

    /**
     * maximalni pocet splatek
     * @var int
     */
    public $maximumNumberOfInstallments;

    /**
     * minimalni odklad splatek
     * @var int
     */
    public $minimumPostponeOfFirstInstallment;

    /**
     * maximalni odklad splatek
     * @var int
     */
    public $maximumPostponeOfFirstInstallment;

    const POSTPONING_REQUIRED = 1;
    CONST POSTPONING_OPTIONAL = 0;

    /**
     * parametr jestli je odklad vyzadovany
     * 1 - odklad je vyzadovan
     * 0 - odklad muze byt nastaveni na 0 mesicu
     *
     * @var int
     */
    public $isPostponingRequired;

    /**
     * Data objekt limitu
     *
     * @param int $minuver minimalni vyse uveru
     * @param int $maxuver maximalni vyse uveru
     * @param int $minpocspl minimalni pocet splatek
     * @param int $maxpocspl maximalni pocet splatek
     * @param int $minodklad minimalni odklad splatek
     * @param int $maxodklad maximalni odklad splatek
     * @param int $reqodklad
     */
    public function __construct(
        $minuver,
        $maxuver,
        $minpocspl,
        $maxpocspl,
        $minodklad = null,
        $maxodklad = null,
        $reqodklad = self::POSTPONING_OPTIONAL
    ) {
        $this->minimumSizeOfLoan = $minuver;
        $this->maximumSizeOfLoan = $maxuver;
        $this->minimumNumberOfInstallments = $minpocspl;
        $this->maximumNumberOfInstallments = $maxpocspl;
        $this->minimumPostponeOfFirstInstallment = $minodklad;
        $this->maximumPostponeOfFirstInstallment = $maxodklad;
        $this->isPostponingRequired = $reqodklad;
    }

    public function getOldRepresentation() {
        return [
            'minuver' => $this->minimumSizeOfLoan,
            'maxuver' => $this->maximumSizeOfLoan,
            'minpocspl' => $this->minimumNumberOfInstallments,
            'maxpocspl' => $this->maximumNumberOfInstallments,
            'minodklad' => $this->minimumPostponeOfFirstInstallment,
            'maxodklad' => $this->maximumPostponeOfFirstInstallment,
            'reqodklad' => $this->isPostponingRequired,
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function jsonSerialize() {
        return $this->getOldRepresentation();
    }
}
