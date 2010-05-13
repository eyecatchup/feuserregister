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

require_once(PATH_feuserregister . 'model/class.tx_feuserregister_model_abstractstep.php');

class tx_feuserregister_model_Step extends tx_feuserregister_model_AbstractStep {

	public function render() {
		if ($this->_validationActive && strlen($this->_request->get('action')) > 0) {
			$this->_validate();
		}
		$fieldMarker	= $this->_getFieldMarker();
		$labelMarker	= $this->_getLabelmarker();
		$valueMarker	= $this->_getValueMarker();
		$requiredMarker	= $this->_getRequiredMarker();
		$errorMarker	= $this->_getErrorMarker();
		$globalMarker	= $this->_getGlobalMarker();
		$lllMarker		= $this->_getLllMarker();
	
		$marker = array_merge($fieldMarker, $labelMarker, $valueMarker, $requiredMarker, $errorMarker, $globalMarker, $lllMarker);
	
		$this->_controller->notifyObservers('renderStepAdditionalMarker', array('marker' => &$marker));
	
		return t3lib_parsehtml::substituteMarkerArray($this->_templateContent, $marker, '', 0, 1);
	}
}

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/feuserregister/model/class.tx_feuserregister_model_step.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/feuserregister/model/class.tx_feuserregister_model_step.php']);
}

?>