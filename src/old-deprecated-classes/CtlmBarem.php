<?php

/**
 * Data objekt baremu
 * @deprecated
 */
class CtlmBarem {
	
	/**
	 * id baremu v ciselniku
	 * @var string
     * @deprecated
	 */
	public $id;
	
	/**
	 * nazev baremu
	 * @var string
     * @deprecated
	 */
	public $name;
	
	/**
	 * definice limitu baremu
	 * @var CtlmLimit
     * @deprecated
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
     * @deprecated
	 */
	public $platba;
	
	/**
	 * prima platba
	 * @var unknown
     * @deprecated
	 */
	public $platbaValue;
	
	/**
	 * Data objekt baremu
	 * @param string $id id baremu v ciselniku
	 * @param string $name nazev baremu
	 * @param CtlmLimit $limit definice limitu baremu
     * @deprecated
	 */
	public function __construct($id, $name, CtlmLimit $limit = null, $platba, $platbaValue) 
	{
		$this->id = $id;
		$this->name = $name;
		if ($limit != null)
			$this->limit = $limit;
		
		$this->platba = $platba;
		$this->platbaValue = $platbaValue;
	}
	
}
