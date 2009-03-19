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

require_once(PATH_feuserregister . 'classes/transformer/class.tx_feuserregister_abstracttransformer.php');

class tx_feuserregister_transformer_Timestamp extends tx_feuserregister_AbstractTransformer {
	protected $_name = 'timestamp';
	protected $_type = tx_feuserregister_AbstractTransformer::TYPE_DATABASE;

	/**
	 * @see tx_feuserregister_AbstractTransformer::transform()
	 *
	 * @return mixed
	 */
	public function transform() {
		if ($this->_value && $this->_options['inFormat']) {
			$dateParts = strptime($this->_value, $this->_options['inFormat']);
			if ($dateParts === false) {
				return $this->_value;
			}
			$timestamp = mktime($dateParts['tm_hour'], $dateParts['tm_min'], $dateParts['tm_sec'], $dateParts['tm_mon']+1, $dateParts['tm_mday'], 1900+$dateParts['tm_year']);
			if ($timestamp === false) {
				return $this->_value;
			}
			$compareDateString = strftime($this->_options['inFormat'], $timestamp);

			$dateString = (string) $this->_value;
			if ($compareDateString === $dateString) {
				return $timestamp;
			}
		}
		return $this->_value;
	}
}

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/feuserregister/classes/transformer/class.tx_feuserregister_transformer_timestamp.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/feuserregister/classes/transformer/class.tx_feuserregister_transformer_timestamp.php']);
}

?>