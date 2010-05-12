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

class tx_feuserregister_CommandResolver {
	protected $_path;
	protected $_request;
	
	public function __construct() {
		$this->_path			= PATH_feuserregister . 'classes/commands';
		$this->_request			= t3lib_div::makeInstance('tx_feuserregister_Request');
	}

	/**
	 * get command from request
	 *
	 * @return tx_feuserregister_interface_Command
	 */
	public function getCommand() {
		switch ($this->_request->get('cmd')) {
			case 'register':
				$cmdName = 'register';
			break;
			case 'confirm':
				$cmdName = 'confirm';
			break;
			case 'edit':
				$cmdName = 'edit';
			break;
			default:
				$cmdName = '';
			break;
		}
		
		if (strlen($cmdName) == 0) {
			$flexform = tx_feuserregister_Registry::get('tx_feuserregister_flexform');
			switch ($flexform['modus']) {
				case '0':
					$cmdName = 'register';
				break;
				case '1':
					$cmdName = 'confirm';
				break;
				case '2':
					$cmdName = 'edit';
				break;
			}
		}
		$command = $this->loadCommand($cmdName);
		return $command;
	}

	/**
	 * laod command by given name
	 *
	 * @param string $cmdName
	 * @return tx_feuserregister_interface_Command
	 */
	protected function loadCommand($cmdName) {
		$cmdName = ucfirst($cmdName);
		$class = "tx_feuserregister_command_{$cmdName}";
		$command = t3lib_div::makeInstance($class);

		// @todo: Throw exception if object does not implement the interface

		return $command;
	}
}

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/feuserregister/classes/class.tx_feuserregister_commandresolver.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/feuserregister/classes/class.tx_feuserregister_commandresolver.php']);
}

?>