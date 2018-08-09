<?php

/**
 * Staticka fasada pro sluzby Cetelem.
 * Umoznije rychly staticky pristup k singltonum sluzem cetelem
 * @deprecated
 */
class Ctlm {
	
	/**
	 * ciselnik cetelem
	 * @var CtlmCiselnik
     * @deprecated
	 */
	public static $ciselnik = null;
	
	/**
	 * kalkulator cetelem
	 * @var CtlmKalkulator
     * @deprecated
	 */
	public static $kalkulator = null;
	
	/**
	 * helper funkce
	 * @var CtlmHelper
     * @deprecated
	 */
	public static $helper = null;
	
	
}
