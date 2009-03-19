<?php
/***************************************************************
 *  Copyright notice
 *
 *  (c) 2009 Frank N�gler <typo3@naegler.net>
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
 * $Id: class.tx_feuserregister_abstracttcafield.php 371 2009-03-19 14:19:19Z franae $
 */

abstract class tx_feuserregister_AbstractTcaField {
	const PARSE_DATABASE	= 'database';
	const PARSE_HTML		= 'html';
	
	protected $_attributes = array();
	protected $_configuration = array();
	protected $_fieldConfiguration = array();
	protected $_fieldName = '';
	protected $_transformers = array();
	protected $_value = '';
	
	public function __construct() {
	}
	
	/**
	 * get the tca field label
	 *
	 * @return string the label
	 */
	public function getLabel() {
		return $this->_getLllValue($this->_configuration['label']);
	}
	
	public function getValue($format = null) {
		switch ($format) {
			case self::PARSE_DATABASE:
				$value = $this->_prepareForDatabase();
				$value = $this->_processTransformers($value, tx_feuserregister_AbstractTransformer::TYPE_DATABASE);
			break;
			case self::PARSE_HTML:
				$value = $this->_prepareForHtml();
				$value = $this->_processTransformers($value, tx_feuserregister_AbstractTransformer::TYPE_HTML);
			break;
			default:
				return $this->_value;
			break;
		}
		return $value;
	}
	
	/**
	 * set array of config the tca field
	 *
	 * @param array $configuration
	 */
	public function setConfiguration(array $configuration) {
		$this->_configuration = $configuration;
	}
	
	/**
	 * set array of field config
	 *
	 * @param array $fieldConfiguration
	 */
	public function setFieldConfiguration(array $fieldConfiguration) {
		$this->_fieldConfiguration = $fieldConfiguration;
		if (strlen($fieldConfiguration['additionalAttributes']) > 0) {
			$this->_attributes['__STRING__'] = $fieldConfiguration['additionalAttributes'];
		}
	}
	
	/**
	 * set array of transformers
	 *
	 * @param array $transformers
	 * @param string type of transformer
	 */
	public function setTransformers(array $transformers, $type) {
		$this->_transformers[$type] = $transformers;
	}
	
	/**
	 * set fieldname
	 *
	 * @param string $fieldName
	 */
	public function setFieldname($fieldName) {
		$this->_fieldName = $fieldName;
		$this->_attributes['name'] = "tx_feuserregister[data][{$this->_fieldName}]";
		$this->_attributes['id'] = "tx-feuserregister-{$this->_fieldName}";
	}
	
	/**
	 * set value
	 *
	 * @param string $value
	 */
	public function setValue($value) {
		$this->_value = $value;
	}
	
	protected function _getLllValue($key) {
		$controller = tx_feuserregister_Registry::get('tx_feuserregister_controller');
		$keyParts = explode(':', $key);
		if ($keyParts[0] == 'LLL' && $keyParts[1] == 'EXT') {
			$value = $controller->pi_getLL($keyParts[3]);
			if (strlen($value) > 0) {
				return $value;
			} else {
				return $GLOBALS['TSFE']->sL($key);
			}
		}
		return $key;
	}
	
	protected function _getAttributesString() {
		$attributeString = '';
		foreach ($this->_attributes as $attribute => $value) {
			if ($attribute == '__STRING__') {
				$attributeString .= ' '.$value;
			} else {
				$attributeString .= " {$attribute}=\"{$value}\"";
			}
		}
		return $attributeString;
	}
	
	protected function _processTransformers($value, $type) {
		if (is_array($this->_transformers[$type])) {
			if (count($this->_transformers[$type])) {
				foreach ($this->_transformers[$type] as $transformer) {
					$transformer->setValue($value);
					$value = $transformer->transform();
				}
			}
		}
		return $value;
	}
	
	/**
	 * abstract method for validation, you have to implement this function in your own class
	 * @abstract 
	 * @return string the html field
	 */
	abstract public function getHtmlField();

	/**
	 * abstract method for preapring $this->_value for the database
	 * @abstract 
	 * @return mixed the database value
	 */
	abstract protected function _prepareForDatabase();

	/**
	 * abstract method for preapring $this->_value for the html output
	 * @abstract 
	 * @return mixed the for html output prepared value
	 */
	abstract protected function _prepareForHtml();
}

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/feuserregister/classes/tca/class.tx_feuserregister_abstracttcafield.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/feuserregister/classes/tca/class.tx_feuserregister_abstracttcafield.php']);
}

?>