<?php

namespace Somrlik\HelloBankApi\DataObjects;

class Insurance implements \JsonSerializable {

    /**
     * id pojisteni v ciselniku
     * @var string
     */
    public $id;

    /**
     * nazev pojisteni
     * @var string
     */
    public $name;

    /**
     * kratky popis pojisteni
     * @var string
     */
    public $description;


    /**
     * napoveda k pojisteni (muze obsahovat html <small>, <br /> a <strong> tagy)
     * @var string
     */
    public $hint;

    /**
     * Data objekt pojisteni
     *
     * @param string $id id pojisteni v ciselniku
     * @param string $name nazev pojisteni
     * @param        $description
     * @param        $hint
     */
    public function __construct($id, $name, $description, $hint)
    {
        $this->id = $id;
        $this->name = $name;
        $this->description = $description;
        $this->hint = $hint;
    }

    public function getOldRepresentation() {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'popis' => $this->description,
            'napoveda' => $this->hint,
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function jsonSerialize() {
        return $this->getOldRepresentation();
    }

}
