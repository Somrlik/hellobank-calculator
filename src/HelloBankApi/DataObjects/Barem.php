<?php

namespace Somrlik\HelloBankApi\DataObjects;

class Barem implements \JsonSerializable {

    const FIRST_INSTALLMENT_TYPE_FREE = 'free';
    const FIRST_INSTALLMENT_TYPE_FIXED = 'fixed';
    const FIRST_INSTALLMENT_TYPE_PERCENT = 'percent';

    /**
     * id baremu v ciselniku
     * @var string
     */
    public $id;

    /**
     * nazev baremu
     * @var string
     */
    public $name;

    /**
     * definice limitu baremu
     * @var Limit
     */
    public $limit;

    /**
     * typ prime platby
     *
     * 	free 	- primou platbu lze nastavit libovolne
     *  fixed 	- platbaValue obsahuje hodnotu prime platby
     *  percent - platbaValue obsahuje hodnotu procent z cenyZbozi (primou platbu je potreba dopocitat)
     *
     * @var string
     */
    public $typeOfFirstInstallment;

    /**
     * prima platba
     * @var int
     */
    public $firstInstallment;

    /**
     * Data objekt baremu
     *
     * @param string $id id baremu v ciselniku
     * @param string $name nazev baremu
     * @param Limit  $limit definice limitu baremu
     * @param        $typeOfFirstInstallment
     * @param        $firstInstallment
     */
    public function __construct($id, $name, Limit $limit = null, $typeOfFirstInstallment, $firstInstallment)
    {
        $this->id = $id;
        $this->name = $name;
        if (! empty($limit)) $this->limit = $limit;

        $this->typeOfFirstInstallment = $typeOfFirstInstallment;
        $this->firstInstallment = $firstInstallment;
    }

    public function getOldRepresentation() {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'platba' => $this->typeOfFirstInstallment,
            'platbaValue' => $this->firstInstallment,
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function jsonSerialize() {
        return $this->getOldRepresentation();
    }
}
