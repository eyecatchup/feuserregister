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
 * A manager to manage localizations
 *
 * @author	Frank Naegler <typo3@naegler.net>
 * @package TYPO3
 * @subpackage feuserregister
 */
class tx_feuserregister_LocalizationManager {

	/**
	 * @var tx_feuserregister_LocalizationManager
	 */
	static private $instances              = array();

	protected $configuration               = array();

		// the LL-File
	protected $localLanguageFile;

		// Pointer to the language to use.
	protected $localLanguageKey            = 'default';

		// Pointer to alternative fall-back language to use.
	protected $alternativeLocalLanguageKey = '';

		// Local Language content
	protected $localLanguageLabels         = array();

		// Local Language content charset for individual labels (overriding)
	protected $localLanguageLabelsCharset  = array();

		// You can set this during development to some value that makes it easy for you to spot all labels that ARe delivered by the getLL function.
	protected $localLanguageTestPrefix            = '';

		// Save as LLtestPrefix, but additional prefix for the alternative value in getLL() function calls
	protected $alternativeLocalLanguageTestPrefix = '';

	/**
	 * constructor for class tx_feuserregister_LocalizationManager
	 */
	public function __construct($localLanguageFile, array $configuration) {
		$this->localLanguageFile = $localLanguageFile;
		$this->configuration     = $configuration;

		if ($GLOBALS['TSFE']->config['config']['language']) {
			$this->localLanguageKey = $GLOBALS['TSFE']->config['config']['language'];

			if ($GLOBALS['TSFE']->config['config']['language_alt']) {
				$this->alternativeLocalLanguageKey = $GLOBALS['TSFE']->config['config']['language_alt'];
			}
		}

		$this->loadLL();
	}

	private function __clone() {}

	/**
	 * Returns a LocalizationManager instance
	 * This method cannot be overridden!
	 *
	 * @param	string	local language file path
	 * @param	array	TypoScript configuration
	 * @return	tx_feuserregister_LocalizationManager	instance of the localization manager for the given LLL file
	 */
	final public static function getInstance($localLanguageFile, array $configuration) {
		if (!isset(self::$instances[$localLanguageFile])) {
			self::$instances[$localLanguageFile] = t3lib_div::makeInstance(
				'tx_feuserregister_LocalizationManager',
				$localLanguageFile,
				$configuration
			);
		}

		return self::$instances[$localLanguageFile];
	}

	/**
	 * loads the labels from a local language file
	 *
	 * @author	Ingo Renner <ingo@typo3.org>
	 * @author	Frank Naegler <typo3@naegler.net>
	 */
	protected function loadLL() {
		$this->loadAdditionalLocalLanguageFile($this->localLanguageFile);

		if (isset($this->configuration['additionalLocalLanguageFiles.'])) {
			foreach ($this->configuration['additionalLocalLanguageFiles.'] as $additionalLocalLanguageFile) {
				if (!empty($additionalLocalLanguageFile)) {
					$this->loadAdditionalLocalLanguageFile($additionalLocalLanguageFile);
				}
			}
		}

		$this->processLabelOverlays();
	}

	/**
	 * Loads labels from an addition local language file
	 *
	 * @param string $fileName Local language file to be loaded
	 * @return void
	 */
	protected function loadAdditionalLocalLanguageFile($fileName) {
		$localLanguageLabels = t3lib_div::readLLfile(
			$fileName,
			$this->localLanguageKey,
			$GLOBALS['TSFE']->renderCharset
		);

		if ($this->alternativeLocalLanguageKey) {
			$tempLocalLangueLabels = t3lib_div::readLLfile(
				$fileName,
				$this->alternativeLocalLanguageKey
			);

			$localLanguageLabels = array_merge(
				(array) $localLanguageLabels,
				$tempLocalLangueLabels
			);
		}

		$this->localLanguageLabels = t3lib_div::array_merge_recursive_overrule(
			(array) $this->localLanguageLabels,
			$localLanguageLabels
		);
	}

	/**
	 * Processes the label overlays defined in TypoScript.
	 *
	 * @return void
	 */
	protected function processLabelOverlays() {
			// Overlaying labels from TypoScript (including fictitious language keys for non-system languages!):
		if (is_array($this->configuration['_LOCAL_LANG.'])) {

			foreach ($this->configuration['_LOCAL_LANG.'] as $language => $overideLabels) {
				$language = substr($language, 0, -1);
				if (is_array($overideLabels)) {
					foreach ($overideLabels as $labelKey => $overideLabel) {
						if (!is_array($overideLabel)) {
							$this->localLanguageLabels[$language][$labelKey] = $overideLabel;

								// For labels coming from the TypoScript (database) the charset is assumed
								// to be "forceCharset" and if that is not set, assumed to be that of the
								// individual system languages
							$this->localLanguageLabelsCharset[$language][$labelKey] = $GLOBALS['TYPO3_CONF_VARS']['BE']['forceCharset'] ?
								$GLOBALS['TYPO3_CONF_VARS']['BE']['forceCharset'] :
								$GLOBALS['TSFE']->csConvObj->charSetArray[$language];
						}
					}
				}
			}
		}
	}

	public function getLL($labelKey, $alternativeLabel = '', $hsc = false) {
			// The "from" charset of csConv() is only set for strings from TypoScript via _LOCAL_LANG
		if (isset($this->localLanguageLabels[$this->localLanguageKey][$labelKey])) {
			$word = $GLOBALS['TSFE']->csConv(
				$this->localLanguageLabels[$this->localLanguageKey][$labelKey],
				$this->localLanguageLabelsCharset[$this->localLanguageKey][$labelKey]
			);
		} elseif ($this->alternativeLocalLanguageKey && isset($this->localLanguageLabels[$this->alternativeLocalLanguageKey][$labelKey])) {
			$word = $GLOBALS['TSFE']->csConv(
				$this->localLanguageLabels[$this->alternativeLocalLanguageKey][$labelKey],
				$this->localLanguageLabelsCharset[$this->alternativeLocalLanguageKey][$labelKey]
			);
		} elseif (isset($this->localLanguageLabels['default'][$labelKey])) {
				// No charset conversion because default is english and thereby ASCII
			$word = $this->localLanguageLabels['default'][$labelKey];
		} else {
			$word = $this->alternativeLocalLanguageTestPrefix . $alternativeLabel;
		}
	
		$output = $this->localLanguageTestPrefix . $word;

		if ($hsc) {
			$output = htmlspecialchars($output);
		}
		return $output;
	}
	
	public function getAllAsMarkerArray() {
		$marker = array();
		$languages = $GLOBALS['TSFE']->tmpl->setup['plugin.']['tx_feuserregister.']['_LOCAL_LANG.'];
		foreach ($languages as $language) {
			foreach ($language as $key => $value) {
				$keys[$key] = $value;
			}
		}
		foreach ($keys as $key => $value) {
			$marker["###LLL_{$key}###"] = $this->getLL($key);
		}
		return $marker;
	}
}


if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/feuserregister/classes/class.tx_feuserregister_localizationmanager.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/feuserregister/classes/class.tx_feuserregister_localizationmanager.php']);
}

?>