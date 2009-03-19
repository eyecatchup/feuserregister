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
 * $Id$
 */ 

class tx_feuserregister_view_Error {
	protected $_configuration = null;
	protected $_controller = null;
	protected $_marker = array();
	protected $_model = null;
	protected $_templateContent = '';
	
	public function __construct() {
		$this->_configuration	= tx_feuserregister_Registry::get('tx_feuserregister_configuration');
		$this->_controller		= tx_feuserregister_Registry::get('tx_feuserregister_controller');
		$this->_templateContent = $this->_controller->cObj->fileResource($this->_configuration['templates.']['error']);
	}
	
	public function setModel(Exception $model) {
		$this->_model = $model;
		$exceptionCode = $this->_model->getCode();
		$subpart = t3lib_parsehtml::getSubpart($this->_templateContent, "###TEMPLATE_ERROR_{$exceptionCode}###");
		if (strlen($subpart) > 0) {
			$this->_templateContent = $subpart;
		} else {
			$this->_templateContent = t3lib_parsehtml::getSubpart($this->_templateContent, "###TEMPLATE_ERROR###");
		}
	}
	
	public function render() {
		$this->_createMarker();
		return t3lib_parsehtml::substituteMarkerArray($this->_templateContent, $this->_marker, '###|###', 1, 1);
	}
	
	protected function _createMarker() {
		$this->_marker = array();
		if ($this->_model instanceof Exception) {
			$this->_marker['exception_message']		= $this->_model->getMessage();
			$this->_marker['exception_code']		= $this->_model->getCode();
			$this->_marker['exception_file']		= $this->_model->getFile();
			$this->_marker['exception_line']		= $this->_model->getLine();
			$this->_marker['exception_trace']		= $this->_model->getTrace();
			$this->_marker['exception_tracestring']	= $this->_model->getTraceAsString();
		}
	}
}

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/feuserregister/view/class.tx_feuserregister_view_error.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/feuserregister/view/class.tx_feuserregister_view_error.php']);
}

?>