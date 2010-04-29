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

class tx_feuserregister_FileManager {
	/**
	 * @var t3lib_basicFileFunctions
	 */
	protected $basicFileFunctions;

	/**
	 * @var tx_feuserregister_Request
	 */
	protected $request;

	/**
	 * @param tx_feuserregister_Request $request
	 */
	public function __construct(tx_feuserregister_Request $request) {
		$this->setRequest($request);
		$this->setBasisFileFunctions(
			t3lib_div::makeInstance('t3lib_basicFileFunctions')
		);
	}

	/**
	 * @param tx_feuserregister_Request $request
	 */
	public function setRequest(tx_feuserregister_Request $request) {
		$this->request = $request;
	}

	/**
	 * @param t3lib_basicFileFunctions $basicFileFunctions
	 */
	public function setBasisFileFunctions(t3lib_basicFileFunctions $basicFileFunctions) {
		$this->basicFileFunctions = $basicFileFunctions;
	}

	/**
	 * @param string $fieldName
	 * @param string $destinationFolder
	 * @return string
	 */
	public function processFormUpload($fieldName, $destinationFolder) {
		$fileName = NULL;
		$filesData = $this->request->files('data');

		if (isset($filesData[$fieldName]['name']) && $filesData[$fieldName]['name']) {
			$destinationFileName = $this->basicFileFunctions->getUniqueName(
				$this->basicFileFunctions->cleanFileName($filesData[$fieldName]['name']),
				$destinationFolder
			);

			if ($destinationFileName) {
				t3lib_div::upload_copy_move($filesData[$fieldName]['tmp_name'], $destinationFileName);
				$fileInformation = t3lib_div::split_fileref($destinationFileName);
				$fileName = $fileInformation['file'];
			}
		}

		return $fileName;
	}
}

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/feuserregister/classes/class.tx_feuserregister_filemanager.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/feuserregister/classes/class.tx_feuserregister_filemanager.php']);
}

?>