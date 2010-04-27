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
 * $Id: class.tx_feuserregister_registry.php 18083 2009-03-19 21:01:59Z neoblack $
 */

class tx_feuserregister_Request {
	protected $_request = array();
	protected $_files = array();
	
	public function __construct() {
		$this->_request = t3lib_div::GParrayMerged('tx_feuserregister');
		$this->_files = isset($_FILES['tx_feuserregister']) ? $_FILES['tx_feuserregister'] : array();
	}
	
	/**
	 * getter method.
	 *
	 * @param string $index - get the value associated with $index
	 * @return mixed
	 */
	public function get($index) {
		if (array_key_exists($index, $this->_request)) {
			if (is_array($this->_request[$index])) {
				$tmpData = array();
				foreach ($this->_request[$index] as $key => $value) {
					if (!is_array($value)) {
						$tmpData[$key] = t3lib_div::removeXSS($value);
					}
				}
				return $tmpData;
			} else {
				return t3lib_div::removeXSS($this->_request[$index]);
			}
		}
		return null;
	}

	/**
	 * @param string $index
	 * @return array
	 */
	public function files($index) {
		$files = NULL;

		if (isset($this->_files['name'][$index])) {
			$files = array();
			$keys = array_keys($this->_files);
			$fields = array_keys($this->_files['name'][$index]);

			foreach ($fields as $field) {
				$fieldArray = array();
				foreach ($keys as $key) {
					$fieldArray[$key] = $this->_files[$key][$index][$field];
				}
				$files[$field] = $fieldArray;
			}
		}

		return $files;
	}
}

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/feuserregister/classes/class.tx_feuserregister_request.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/feuserregister/classes/class.tx_feuserregister_request.php']);
}

?>
