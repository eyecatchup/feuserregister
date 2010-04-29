<?php
/***************************************************************
 *  Copyright notice
 *
 *  (c) 2010 Oliver Hader <oliver@typo3.org>
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

require_once(PATH_feuserregister . 'classes/transformer/class.tx_feuserregister_abstracttransformer.php');

class tx_feuserregister_transformer_Image extends tx_feuserregister_AbstractTransformer {
	protected $_name = 'image';

	/**
	 * @see tx_feuserregister_AbstractTransformer::transform()
	 *
	 * @return mixed
	 */
	public function transform() {
		/* @var $controller tx_feuserregister_controller_UserRegistration */
		$controller = tx_feuserregister_Registry::get('tx_feuserregister_controller');

		$configuration = array(
			'file' => rtrim($this->_options['uploadFolder'], '/') . '/' . $this->_value,
			'file.' => $this->_options['file.'],
		);

		return $controller->cObj->IMAGE($configuration);
	}
}

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/feuserregister/classes/transformer/class.tx_feuserregister_transformer_htmlspecialchars.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/feuserregister/classes/transformer/class.tx_feuserregister_transformer_htmlspecialchars.php']);
}

?>