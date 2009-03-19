<?php
if (!defined ('TYPO3_MODE')) {
     die ('Access denied.');
}

t3lib_extMgm::addPItoST43($_EXTKEY, 'controller/class.tx_feuserregister_controller_userregistration.php', '_UserRegistration', 'list_type', 1);
?>