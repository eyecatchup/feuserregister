<?php

/**
 * This Class does translations for frontend plugins.
 *
 * PHP versions 4 and 5
 *
 * Copyright (c) 2008 Sebastian Boettger
 *
 * LICENSE:
 *
 * This library is free software; you can redistribute it and/or
 * modify it under the terms of the GNU Lesser General Public
 * License as published by the Free Software Foundation; either
 * version 2.1 of the License, or (at your option) any later version.
 *
 * This library is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU
 * Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public
 * License along with this library; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA 02111-1307 USA
 *
 * @package    TYPO3
 * @subpackage feuserregister
 * @author     Sebastian Boettger <dev@cross-content.com>
 * @copyright  2008 Sebastian Boettger
 * @license    http://www.opensource.org/licenses/lgpl-license.php LGPL
 * @version    SVN: $Id: class.tx_feuserregister_translator.php 7333 2008-03-30 12:21:48Z crosscontent $
 * @since      0.1
 */

/**
 * This Class does translations for frontend plugins.
 *
 * Usage:
 *
 * <code>
 *  $translatorClassName = tx_div::makeInstanceClassName('tx_feuserregister_translator');
 *  $translator = new $translatorClassName($controller);
 *  $translator->setExtensionKey('myExtensionKey');
 *  return $translator->translate($out);
 * </code>
 *
 * The markers in the text to translate have the format '%%%keyToTranslation%%%'.
 * They are extracted by preg_replace() with the default pattern '/%%%([^%])%%%/'.
 * You may use other markers by setting another pattern:
 *
 * <code>
 *  $translator->setTranslationPattern('/§§§([^§])§§§/');
 * </code>
 *
 * The code is extracted mainly from tslib_pibase with few adaptions.
 * That is the reason why it is not done in the typical lib/div style.
 * That doesn't matter so much in this case as the target of this class is direct use
 * not inheritance. The API itself is done in typical lib/div style.
 *
 * Depends on: tx_div, tx_lib	<br>
 * Used by: tx_feuserregister_controller
 *
 * @author     Sebastian Boettger <dev@cross-content.com>
 * @package    TYPO3
 * @subpackage feuserregister
 */
class tx_feuserregister_translator extends tx_lib_translator {
	var $controller;
	var $conf;
	
	function tx_feuserregister_translator($controller){
		global $TSFE;
		parent::tx_lib_objectBase();
		$this->controller = &$controller;
		//debug($this->controller,'controller', __FILE__ , __LINE__);
		$this->conf = $GLOBALS['TSFE']->tmpl->setup['plugin.']['tx_feuserregister.'];
		//debug($this->conf,'conf', __FILE__ , __LINE__);
	}
	
	function getTranslationByLLString($LLString){
		global $TSFE;
		//debug($LLString,'LLString', __FILE__ , __LINE__);
		return $TSFE->sL($LLString);
	}
	
	// start from sr_feuser_register
		function getLLFromString($string, $bForce=true) {
		global $LOCAL_LANG, $TSFE;
		
		$rc = '';
		$arr = explode(':',$string);
		if($arr[0] == 'LLL' && $arr[1] == 'EXT') {
			$temp = $this->translate('%%%'.$arr[3].'%%%');
			if ($temp || !$bForce) {
				return $temp;
			} else {
				return $TSFE->sL($string);
			}
		} else {
			$rc = $string;
		}

		return $rc;
	}	// getLLFromString


	/**
	* Get the item array for a select if configured via TypoScript
	* @param	string	name of the field
	* @ return	array	array of selectable items
	*/
	function getItemsLL($textSchema, $bAll = true, $valuesArray = array()) {
		//debug($textSchema,'textSchema', __FILE__ , __LINE__);
		//debug($bAll,'bAll', __FILE__ , __LINE__);
		//debug($valuesArray,'valuesArray', __FILE__ , __LINE__);
		$rc = array();
		$end='.I.';
		$textSchema = str_replace('.', '_', $textSchema);
#		$textSchema2 = $textSchema . $endNoPoints;
#		$textSchema .= $end;

		# debug($textSchema);
		# debug($textSchema2);
#		debug($valuesArray);
		if ($bAll)	{
			for ($i = 0; $i < 50; ++$i)	{
				$text = $this->translate('%%%'.$textSchema.$i.'%%%');
				if ($text != '%%%'.$textSchema.$i.'%%%')	{
					$rc[] = array($text, $i);
				} else {
	#			  $text = $this->translate('%%%'.$textSchema2.$i.'%%%');
  #				if ($text != '%%%'.$textSchema2.$i.'%%%')	{
  #					$rc[] = array($text, $i);
  #				}
        }
			}
		} else {
			foreach ($valuesArray as $k => $i)	{
				$text = $this->translate('%%%'.$textSchema.$i.'%%%');
				if ($text != '%%%'.$textSchema.$i.'%%%')	{
					$rc[] = array($text, $i);
				} else {
		#		  $text = $this->translate('%%%'.$textSchema2.$i.'%%%');
  	#			if ($text != '%%%'.$textSchema2.$i.'%%%')	{
  	#				$rc[] = array($text, $i);
  	#			}
        }
			}
		}
		# debug($rc);
		return $rc;
	}	// getItemsLL
	// end from sr_feuser_register

	/**
	 * Loads the language Files.
	 *
	 * @return	void
	 * @author	Kasper Skårhøj
	 * @author	Elmar Hinz <elmar.hinz@team-red.net>
	 * @access	private
	 */
	function _loadLocalLang() {
		if ($GLOBALS['TSFE']->config['config']['language']){
			$this->LLkey = $GLOBALS['TSFE']->config['config']['language'];
			if ($GLOBALS['TSFE']->config['config']['language_alt']) {
				$this->altLLkey = $GLOBALS['TSFE']->config['config']['language_alt'];
			}
		}
		$basePath = $this->getPathToLanguageFile();
		if (!is_readable($basePath))  {
			$this->_die('Please set a correct path for tx_lib_translator to the locallang file.' . chr(10) .
					'Example: $translator->setPathToLanguageFile(\'EXT:myextension/locallang.xml\');' . chr(10), __FILE__, __LINE__);
		}
		if(!$this->LOCAL_LANG_loaded){
			// php or xml as source: In any case the charset will be that of the system language.
			// However, this function guarantees only return output for default language plus the specified language (which is different from how 3.7.0 dealt with it)
			$this->LOCAL_LANG = t3lib_div::readLLfile($basePath,$this->LLkey);
			if ($this->altLLkey)    {
				$tempLOCAL_LANG = t3lib_div::readLLfile($basePath,$this->altLLkey);
				$this->LOCAL_LANG = array_merge(is_array($this->LOCAL_LANG) ? $this->LOCAL_LANG : array(),$tempLOCAL_LANG);
			}

			// Overlaying labels from TypoScript (including fictitious language keys for non-system languages!):
			if (is_array($this->conf['_LOCAL_LANG.']))      {
				reset($this->conf['_LOCAL_LANG.']);
				while(list($k,$lA)=each($this->conf['_LOCAL_LANG.']))   {
					if (is_array($lA))      {
						$k = substr($k,0,-1);
						foreach($lA as $llK => $llV)    {
							if (!is_array($llV))    {
								$this->LOCAL_LANG[$k][$llK] = $llV;
								//debug($k,'k', __FILE__ , __LINE__);
								//debug($llK,'llK', __FILE__ , __LINE__);
								//debug($llV,'llV', __FILE__ , __LINE__);
								if ($k != 'default')    {
									$this->LOCAL_LANG_charset[$k][$llK] = $GLOBALS['TYPO3_CONF_VARS']['BE']['forceCharset'];        // For labels coming from the TypoScript (database) the charset is assumed to be "forceCharset" and if that is not set, assumed to be that of the individual system languages (thus no conversion)
								}
							}
						}
					}
				}
			}	
			//debug($this->LOCAL_LANG_charset,'LOCAL_LANG_charset', __FILE__ , __LINE__);
			$this->LOCAL_LANG_loaded = 1;
		}
	} 	
	
}
?>