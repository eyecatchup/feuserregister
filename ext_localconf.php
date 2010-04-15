<?php
if (!defined ('TYPO3_MODE')) {
     die ('Access denied.');
}

$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['t3lib/class.t3lib_userauth.php']['logoff_pre_processing'][] = 'EXT:feuserregister/hooks/class.tx_feuserregister_t3libauth_hook.php:&tx_feuserregister_t3libauth_hook->logoff_pre_processing';

?>