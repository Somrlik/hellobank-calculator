<?php

namespace Somrlik\HelloBankApi\DataObjects;

class IncomeType implements \JsonSerializable {

    /**
     * id typu prijmu v ciselniku
     * @var string
     */
    public $id;

    /**
     * nazev typu prijmu
     * @var string
     */
    public $name;

    /**
     * nasobek
     * @var string
     */
    public $multiplier;


    /**
     * minimum
     * @var string
     */
    public $minimalValue;

    /**
     * Data objekt pojisteni
     * @param string $id id typu prijmu v ciselniku
     * @param string $titul nazev typu prijmu
     * @param string $nasobek nasobek
     * @param string $minimum minimum
     */
    public function __construct($id, $titul, $nasobek, $minimum)
    {
        $this->id = $id;
        $this->name = $titul;
        $this->multiplier = $nasobek;
        $this->minimalValue = $minimum;
    }

    public function getOldRepresentation() {
        return [
            'id' => $this->id,
            'titul' => $this->name,
            'nasobek' => $this->multiplier,
            'minimum' => $this->minimalValue,
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function jsonSerialize() {
        return $this->getOldRepresentation();
    }

}
