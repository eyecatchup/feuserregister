<?php

########################################################################
# Extension Manager/Repository config file for ext: "feuserregister"
#
# Auto generated 20-01-2009 11:49
#
# Manual updates:
# Only the data in the array - anything else is removed by next write.
# "version" and "dependencies" must not be touched!
# $Id$
########################################################################

$EM_CONF[$_EXTKEY] = array(
	'title' => 'Frontend User Registration',
	'description' => 'the complete new frontend user registration with clean code by MVC and design patterns. simple to configure by TypoScript',
	'category' => 'plugin',
	'author' => 'Frank Naegler',
	'author_email' => 'typo3@naegler.net',
	'shy' => '',
	'dependencies' => '',
	'conflicts' => '',
	'priority' => '',
	'module' => '',
	'state' => 'stable',
	'internal' => '',
	'uploadfolder' => 0,
	'createDirs' => '',
	'modify_tables' => 'fe_users',
	'clearCacheOnLoad' => 1,
	'lockType' => '',
	'author_company' => 'TYPO3Weblog.de',
	'version' => '0.0.99',
	'constraints' => array(
		'depends' => array(
			'php' => '5.2.0-0.0.0',
			'typo3' => '4.2.0-4.2.99',
		),
		'conflicts' => array(
		),
		'suggests' => array(
			't3sec_saltedpw' => '0.2.5-0.0.0'
		),
	),
	'_md5_values_when_last_written' => 'a:8:{s:9:"ChangeLog";s:4:"1cad";s:10:"README.txt";s:4:"ee2d";s:12:"ext_icon.gif";s:4:"1bdc";s:14:"ext_tables.php";s:4:"af2f";s:19:"doc/wizard_form.dat";s:4:"22e3";s:20:"doc/wizard_form.html";s:4:"8138";s:28:"static/feuserregister/constants.txt";s:4:"d41d";s:24:"static/feuserregister/setup.txt";s:4:"d41d";}',
);

?>