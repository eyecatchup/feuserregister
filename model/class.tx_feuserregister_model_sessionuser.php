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

/**
 * tx_feuserregister_model_feuser
 *  
 * @author frank
 * @version 
 */

class tx_feuserregister_model_SessionUser {
	protected $_data = array();
	
	public function __construct() {
		$data = tx_feuserregister_SessionRegistry::get('tx_feuserregister_sessionuser');
		$this->_data = (is_array($data)) ? $data : array();
	}
	
	public function get($key) {
		return $this->_data[$key];
	}
		
	public function set($key, $value) {
		$this->_data[$key] = $value;
	}
	
	public function storeData() {
		tx_feuserregister_SessionRegistry::set('tx_feuserregister_sessionuser', $this->_data);
	}
}

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/feuserregister/model/class.tx_feuserregister_model_sessionuser.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/feuserregister/model/class.tx_feuserregister_model_sessionuser.php']);
}

?>