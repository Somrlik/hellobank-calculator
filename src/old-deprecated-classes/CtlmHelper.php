<?php

Ctlm::$helper = CtlmHelper::getInstance();

/**
 * Helper funkce
 * @deprecated
 */
class CtlmHelper {
	
	/**
	 * instance helperu
	 * @var CtlmHelper
     * @deprecated
     */
	protected static $instance = null;
	
	/**
	 * vrati instanci helperu
	 * @return CtlmHelper
     * @deprecated
     */
	public static function getInstance()
	{
		if (self::$instance == null)
			self::$instance = new self();
		return self::$instance;
	}
	
	private function __construct() {
		
	}

    /**
     * @param $text
     * @return mixed
     * @deprecated
     */
	public function cp1250($text) {
		return $text;
// 		return iconv('UTF-8', 'cp1250', $text);
	}
	
	/**
	 * vrati cislo naformatovane jako penize v kc
	 * @param number $number
     * @deprecated
     * @return string
	 */
	public function money($number) {
		return number_format($number, 0, '.', ' ') . ' Kč';
	}
	
	
	
}
