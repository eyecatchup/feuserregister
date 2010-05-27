<?php
if (!defined ('TYPO3_MODE')) {
	die ('Access denied.');
}

/**
 * $Id: ext_tables.php 33161 2010-05-13 10:27:14Z ohader $
 */

$tempColumns = array (
     'tx_feuserregister_birthday' => array (       
        'exclude' => 0,       
        'label' => 'LLL:EXT:feuserregister/lang/locallang_db.xml:fe_users_tx_feuserregister_birthday',       
        'config' => array (
            'type'     => 'input',
            'size'     => '8',
            'max'      => '20',
            'eval'     => 'date',
            'checkbox' => '0',
            'default'  => '0'
        )
    ),
    'tx_feuserregister_gender' => array (       
        'exclude' => 1,       
        'label' => 'LLL:EXT:feuserregister/lang/locallang_db.xml:fe_users_tx_feuserregister_gender',       
        'config' => array (
            'type' => 'select',
            'items' => array (
                array('LLL:EXT:feuserregister/lang/locallang_db.xml:fe_users_tx_feuserregister_gender_I_0', ''),
				array('LLL:EXT:feuserregister/lang/locallang_db.xml:fe_users_tx_feuserregister_gender_I_1', '0'),
                array('LLL:EXT:feuserregister/lang/locallang_db.xml:fe_users_tx_feuserregister_gender_I_2', '1'),
            ),
            'size' => 1,   
            'maxitems' => 1,
        )
    ),
    'tx_feuserregister_firstname' => array (       
        'exclude' => 1,       
        'label' => 'LLL:EXT:feuserregister/lang/locallang_db.xml:fe_users_tx_feuserregister_firstname',       
        'config' => array (
            'type' => 'input',   
            'size' => '30',   
            'eval' => 'trim',
        )
    ),
    'tx_feuserregister_lastname' => array (       
        'exclude' => 1,       
        'label' => 'LLL:EXT:feuserregister/lang/locallang_db.xml:fe_users_tx_feuserregister_lastname',       
        'config' => array (
            'type' => 'input',   
            'size' => '30',   
            'eval' => 'trim',
        )
    ),
);


t3lib_div::loadTCA('fe_users');
t3lib_extMgm::addTCAcolumns('fe_users',$tempColumns,1);
t3lib_extMgm::addToAllTCAtypes('fe_users','tx_feuserregister_gender;;;;1-1-1, tx_feuserregister_firstname, tx_feuserregister_lastname, tx_feuserregister_birthday');

//adding sysfolder icon
t3lib_div::loadTCA('pages');
// add TCA label
$TCA['pages']['columns']['module']['config']['items']['fe_users']['0'] = 'LLL:EXT:'.$_EXTKEY.'/lang/locallang_db.xml:pages.module.I.25';
// add TCA value
$TCA['pages']['columns']['module']['config']['items']['fe_users']['1'] = 'fe_users';

if (TYPO3_MODE=="BE")   {
        // add icon
        $ICON_TYPES['fe_users'] = array('icon' => t3lib_extMgm::extRelPath($_EXTKEY).'resources/icons/icon_tx_feuserregister_fe_users.gif');
}

t3lib_extMgm::addStaticFile($_EXTKEY,'Configuration/TypoScript', 'Frontend User Registration');

/** extbase setup **/

Tx_Extbase_Utility_Extension::registerPlugin(
	$_EXTKEY,
	'Register',
	'Frontend User Registration'
);

?>