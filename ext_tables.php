<?php
if (!defined ('TYPO3_MODE')) {
	die ('Access denied.');
}

/**
 * $Id$
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
	if (t3lib_div::compat_version('4.4')) {
		t3lib_SpriteManager::addTcaTypeIcon('pages', 'contains-fe_users', t3lib_extMgm::extRelPath($_EXTKEY).'resources/icons/icon_tx_feuserregister_fe_users.gif');
	} else {
		$ICON_TYPES['fe_users'] = array('icon' => t3lib_extMgm::extRelPath($_EXTKEY).'resources/icons/icon_tx_feuserregister_fe_users.gif');
	}
}

t3lib_extMgm::addStaticFile($_EXTKEY,'static/feuserregister/', 'feuserregister');

$TCA['tt_content']['types']['list']['subtypes_excludelist'][$_EXTKEY.'_UserRegistration'] = 'layout,select_key,pages,recusive';
$TCA['tt_content']['types']['list']['subtypes_addlist'][$_EXTKEY.'_UserRegistration']='pi_flexform';

t3lib_extMgm::addPiFlexFormValue($_EXTKEY.'_UserRegistration', 'FILE:EXT:feuserregister/flexforms/feuserregister.xml');

t3lib_extMgm::addPlugin(
	array(
		'LLL:EXT:feuserregister/lang/locallang_db.xml:tt_content.list_type_userRegistration',
		$_EXTKEY.'_UserRegistration'
	),
	'list_type'
);

if (TYPO3_MODE == 'BE') {
	$TBE_MODULES_EXT['xMOD_db_new_content_el']['addElClasses']['tx_feuserregister_userregistration_wizicon'] = t3lib_extMgm::extPath($_EXTKEY).'class.tx_feuserregister_userregistration_wizicon.php';
}


?>