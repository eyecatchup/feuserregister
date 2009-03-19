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
 * $Id: class.tx_feuserregister_mailer.php 352 2009-03-19 12:31:58Z franae $
 */


/**
 * A mailer wrapper class
 *
 * @author	Frank Naegler <typo3@naegler.net>
 * @package TYPO3
 * @subpackage feuserregister
 */
class tx_feuserregister_Mailer {
	
	static function send($to, $event, array $marker) {
		$controller = tx_feuserregister_Registry::get('tx_feuserregister_controller');
		$controller->notifyObservers('onSendMail', array(
			'to'		=> &$to,
			'event'		=> &$event,
			'marker'	=> &$marker
		));
		
		$className = t3lib_div::makeInstanceClassName('tx_feuserregister_LocalizationManager');
		$localizationManager = call_user_func(array($className, 'getInstance'),
			'EXT:feuserregister/lang/locallang_emails.xml', 
			$GLOBALS['TSFE']->tmpl->setup['plugin.']['tx_feuserregister.']
		);
		$subject	= $localizationManager->getLL("email_{$to}_{$event}_subject");
		
		$bodytext = $controller->cObj->fileResource($GLOBALS['TSFE']->tmpl->setup['plugin.']['tx_feuserregister.']['templates.']['mails']);
		
		$templatePrefix = strtoupper("TEMPLATE_{$to}_{$event}");
		$bodytextHtml  = t3lib_parsehtml::getSubpart($bodytext, "###{$templatePrefix}_HTML###");
		$bodytextPlain = t3lib_parsehtml::getSubpart($bodytext, "###{$templatePrefix}_PLAIN###");
		
		$subject		= t3lib_parsehtml::substituteMarkerArray($subject, $marker, '', 0, 1);
		$bodytextHtml	= t3lib_parsehtml::substituteMarkerArray($bodytextHtml, $marker, '', 0, 1);
		$bodytextPlain	= t3lib_parsehtml::substituteMarkerArray($bodytextPlain, $marker, '', 0, 1);
		$configuration = tx_feuserregister_Registry::get('tx_feuserregister_configuration');
		$mailConfiguration = $configuration['global.'][$to.'Email.'];

		$toEmail	= t3lib_parsehtml::substituteMarkerArray($mailConfiguration['email'], $marker, '', 0, 1);

		$fromName	= $mailConfiguration['sender.']['name'];
		$fromEmail	= $mailConfiguration['sender.']['email'];

		$mail = t3lib_div::makeInstance('t3lib_htmlmail');
		
		$mail->start();
		$mail->mailer = 'TYPO3 Mailer :: feuserregister';
		$mail->subject = $subject;
		$mail->from_email = $fromEmail;
		$mail->returnPath = $fromEmail;
		$mail->from_name = $fromName;
		$mail->replyto_email = $fromEmail;
		$mail->replyto_name = $fromName;
		$mail->priority = 3;
		
		if (trim($bodytextHtml)) {
			$mail->theParts['html']['content'] = $bodytextHtml;
			$mail->theParts['html']['path'] = '';
			$mail->extractMediaLinks();
			$mail->extractHyperLinks();
			$mail->fetchHTMLMedia();
			$mail->substMediaNamesInHTML(0); // 0 = relative
			$mail->substHREFsInHTML();
			$mail->setHTML($mail->encodeMsg($mail->theParts['html']['content']));
		}

		$mail->addPlain($bodytextPlain);
		$mail->setHeaders();
		$mail->setContent();
		$mail->setRecipient($toEmail);
		$mail->sendtheMail();
	}
}

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/feuserregister/classes/class.tx_feuserregister_mailer.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/feuserregister/classes/class.tx_feuserregister_mailer.php']);
}

?>