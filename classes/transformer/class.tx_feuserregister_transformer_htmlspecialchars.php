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
 * $Id: class.tx_feuserregister_transformer_timestamp.php 18089 2009-03-19 23:03:02Z neoblack $
 */

require_once(PATH_feuserregister . 'classes/transformer/class.tx_feuserregister_abstracttransformer.php');

class tx_feuserregister_transformer_Htmlspecialchars extends tx_feuserregister_AbstractTransformer {
	protected $_name = 'htmlspecialchars';

	/**
	 * @see tx_feuserregister_AbstractTransformer::transform()
	 *
	 * @return mixed
	 */
	public function transform() {
		return htmlspecialchars($this->_value);
	}
}

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/feuserregister/classes/transformer/class.tx_feuserregister_transformer_htmlspecialchars.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/feuserregister/classes/transformer/class.tx_feuserregister_transformer_htmlspecialchars.php']);
}

?>