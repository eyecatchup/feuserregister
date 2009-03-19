<?php
/***************************************************************
 *  Copyright notice
 *
 *  (c) 2009 Frank Naegler <typo3@naegler.net>
 *  All rights reserved
 *
 *  This script is part of the TYPO3 project. The TYPO3 project is
 *  free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 2 of the License, or
 *  (at your option) any later version.
 *
 *  The GNU General Public License can be found at
 *  http://www.gnu.org/copyleft/gpl.html.
 *
 *  This script is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  This copyright notice MUST APPEAR in all copies of the script!
 ***************************************************************/
/**
 * $Id$
 */

abstract class tx_feuserregister_AbstractTransformer {
	const TYPE_DATABASE	= 'database';
	const TYPE_HTML		= 'html';
	
	protected $_name = '';
	protected $_type = null;
	protected $_value = '';
	protected $_options = array();
	
	public function __construct() {
	}
	
	/**
	 * get the name of the transformer
	 *
	 * @return string the name of the transformer, must be the same as the TypoScript name
	 */
	public function getName() {
		return $this->_name;
	}
	
	/**
	 * get type
	 *
	 * @return string $type
	 */
	public function getType() {
		if ($this->_type === null) {
			$exceptionClass = t3lib_div::makeInstanceClassName('tx_feuserregister_exception_Transformer');
			throw new $exceptionClass("Transformer '{$this->_name}' has no type defined", 4100);
		}
		return $this->_type;
	}
	
	/**
	 * set options
	 *
	 * @param array $options
	 */
	public function setOptions(array $options) {
		$this->_options = $options;
	}
	
	/**
	 * set type
	 *
	 * @param string $type
	 */
	public function setType($type) {
		$this->_type = $type;
	}
	
	/**
	 * set value
	 *
	 * @param string $value
	 */
	public function setValue($value) {
		$this->_value = $value;
	}
	
	/**
	 * abstract method for preapring $this->_value for the html output
	 * @abstract 
	 * @return mixed the for html output prepared value
	 */
	abstract public function transform();
}

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/feuserregister/classes/transformer/class.tx_feuserregister_abstracttransformer.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/feuserregister/classes/transformer/class.tx_feuserregister_abstracttransformer.php']);
}

?>