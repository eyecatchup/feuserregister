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
 * $Id: class.tx_feuserregister_transformer_timestamp.php 293 2009-02-26 23:24:36Z franae $
 */

require_once(PATH_feuserregister . 'classes/transformer/class.tx_feuserregister_abstracttransformer.php');

class tx_feuserregister_transformer_T3sec_saltedpw extends tx_feuserregister_AbstractTransformer {
	protected $_name = 't3sec_saltedpw';
	protected $_type = tx_feuserregister_AbstractTransformer::TYPE_DATABASE;

	/**
	 * @see tx_feuserregister_AbstractTransformer::transform()
	 *
	 * @return mixed
	 */
	public function transform() {
		if (t3lib_extMgm::isLoaded('t3sec_saltedpw')) { 
			require_once t3lib_extMgm::extPath('t3sec_saltedpw').'res/staticlib/class.tx_t3secsaltedpw_div.php'; 
			if (tx_t3secsaltedpw_div::isUsageEnabled()) { 
			require_once t3lib_extMgm::extPath('t3sec_saltedpw').'res/lib/class.tx_t3secsaltedpw_phpass.php'; 
				$objPHPass = t3lib_div::makeInstance('tx_t3secsaltedpw_phpass'); 
				$this->_value = $objPHPass->getHashedPassword($this->_value); 
			} 
		} 
		return $this->_value;
	}
}

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/feuserregister/classes/transformer/class.tx_feuserregister_transformer_timestamp.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/feuserregister/classes/transformer/class.tx_feuserregister_transformer_timestamp.php']);
}

?>