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

abstract class tx_feuserregister_AbstractValidator {
	protected $_errorMessage = '';
	protected $_fieldname = '';
	protected $_localizationManager = null;
	protected $_name = '';
	protected $_options = array();
	protected $_value = null;
	
	public function __construct() {
		$className = t3lib_div::makeInstanceClassName('tx_feuserregister_LocalizationManager');
		$this->_localizationManager = call_user_func(array($className, 'getInstance'),
			'EXT:feuserregister/lang/locallang_validators.xml', 
			$GLOBALS['TSFE']->tmpl->setup['plugin.']['tx_feuserregister.']
		);
	}
	
	/**
	 * get the error message for wrong validation
	 *
	 * @return string the error message
	 */
	public function getErrorMessage() {
		return $this->_errorMessage;
	}
	
	/**
	 * get the name of the validator
	 *
	 * @return string the name of the validator, must be the same as the TypoScript name
	 */
	public function getName() {
		return $this->_name;
	}
	
	/**
	 * set array of options for the validator
	 *
	 * @param array $value
	 */
	public function setFieldname($fieldname) {
		$this->_fieldname = $fieldname;
		$this->_updateErrorMessage();
	}
	
	/**
	 * set array of options for the validator
	 *
	 * @param array $value
	 */
	public function setOptions(array $options) {
		$this->_options = $options;
	}
	
	/**
	 * set the value to validate
	 *
	 * @param mixed $value
	 */
	public function setValue($value) {
		$this->_value = $value;
	}
	
	protected function _updateErrorMessage() {
		$fieldname = ($this->_fieldname) ? '_'.$this->_fieldname : '';
		$this->_errorMessage = $this->_localizationManager->getLL('error_validator_'.$this->_name.$fieldname);
		if (strlen($this->_errorMessage) == 0) {
			$this->_errorMessage = $this->_localizationManager->getLL('error_validator_'.$this->_name);
		}
	}
	
	/**
	 * abstract method for validation, you have to implement this function in your own class
	 * @abstract 
	 * @return boolean
	 */
	abstract public function validate();
	
}

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/feuserregister/classes/validators/class.tx_feuserregister_abstractvalidator.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/feuserregister/classes/validators/class.tx_feuserregister_abstractvalidator.php']);
}

?>