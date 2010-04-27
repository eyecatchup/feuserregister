<?php

########################################################################
# Extension Manager/Repository config file for ext: "feuserregister"
#
# Auto generated 12-05-2009 09:18
#
# Manual updates:
# Only the data in the array - anything else is removed by next write.
# "version" and "dependencies" must not be touched!
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
	'uploadfolder' => 1,
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
			't3sec_saltedpw' => '0.2.5-0.0.0',
		),
	),
	'_md5_values_when_last_written' => 'a:92:{s:9:"ChangeLog";s:4:"f611";s:10:"README.txt";s:4:"2544";s:52:"class.tx_feuserregister_userregistration_wizicon.php";s:4:"7766";s:12:"ext_icon.gif";s:4:"2d48";s:17:"ext_localconf.php";s:4:"1fa1";s:14:"ext_tables.php";s:4:"9777";s:14:"ext_tables.sql";s:4:"cff1";s:23:"feuserregister_form.gif";s:4:"0ab4";s:28:"flexforms/feuserregister.xml";s:4:"497d";s:43:"view/class.tx_feuserregister_view_error.php";s:4:"904a";s:66:"controller/class.tx_feuserregister_controller_userregistration.php";s:4:"1b32";s:24:"controller/locallang.xml";s:4:"4ed0";s:51:"classes/class.tx_feuserregister_commandresolver.php";s:4:"463e";s:55:"classes/class.tx_feuserregister_localizationmanager.php";s:4:"61a2";s:42:"classes/class.tx_feuserregister_mailer.php";s:4:"204e";s:44:"classes/class.tx_feuserregister_registry.php";s:4:"5fb2";s:43:"classes/class.tx_feuserregister_request.php";s:4:"d872";s:51:"classes/class.tx_feuserregister_sessionregistry.php";s:4:"8bb8";s:51:"classes/class.tx_feuserregister_tcafieldfactory.php";s:4:"da8c";s:54:"classes/class.tx_feuserregister_transformerfactory.php";s:4:"8af4";s:52:"classes/class.tx_feuserregister_validatorfactory.php";s:4:"3250";s:60:"classes/commands/class.tx_feuserregister_command_confirm.php";s:4:"8f80";s:57:"classes/commands/class.tx_feuserregister_command_edit.php";s:4:"20ce";s:61:"classes/commands/class.tx_feuserregister_command_register.php";s:4:"d4e8";s:64:"classes/validators/class.tx_feuserregister_abstractvalidator.php";s:4:"4d61";s:60:"classes/validators/class.tx_feuserregister_validator_age.php";s:4:"22dd";s:64:"classes/validators/class.tx_feuserregister_validator_between.php";s:4:"0680";s:66:"classes/validators/class.tx_feuserregister_validator_blacklist.php";s:4:"70bd";s:64:"classes/validators/class.tx_feuserregister_validator_boolean.php";s:4:"bc33";s:67:"classes/validators/class.tx_feuserregister_validator_dateformat.php";s:4:"663c";s:62:"classes/validators/class.tx_feuserregister_validator_email.php";s:4:"6a8f";s:67:"classes/validators/class.tx_feuserregister_validator_equalfield.php";s:4:"def3";s:67:"classes/validators/class.tx_feuserregister_validator_equalvalue.php";s:4:"508d";s:62:"classes/validators/class.tx_feuserregister_validator_float.php";s:4:"2055";s:60:"classes/validators/class.tx_feuserregister_validator_int.php";s:4:"90c3";s:59:"classes/validators/class.tx_feuserregister_validator_ip.php";s:4:"bd7e";s:61:"classes/validators/class.tx_feuserregister_validator_ipv4.php";s:4:"3ad5";s:61:"classes/validators/class.tx_feuserregister_validator_ipv6.php";s:4:"ab3f";s:63:"classes/validators/class.tx_feuserregister_validator_length.php";s:4:"d67f";s:63:"classes/validators/class.tx_feuserregister_validator_regexp.php";s:4:"9c21";s:65:"classes/validators/class.tx_feuserregister_validator_required.php";s:4:"c48c";s:67:"classes/validators/class.tx_feuserregister_validator_uniqueindb.php";s:4:"74ca";s:68:"classes/validators/class.tx_feuserregister_validator_uniqueinpid.php";s:4:"b94c";s:60:"classes/validators/class.tx_feuserregister_validator_url.php";s:4:"7a11";s:56:"classes/tca/class.tx_feuserregister_abstracttcafield.php";s:4:"47cc";s:57:"classes/tca/class.tx_feuserregister_tca_checkboxfield.php";s:4:"5e06";s:54:"classes/tca/class.tx_feuserregister_tca_inputfield.php";s:4:"8195";s:54:"classes/tca/class.tx_feuserregister_tca_radiofield.php";s:4:"7c88";s:55:"classes/tca/class.tx_feuserregister_tca_selectfield.php";s:4:"f36c";s:53:"classes/tca/class.tx_feuserregister_tca_textfield.php";s:4:"3c9c";s:67:"classes/transformer/class.tx_feuserregister_abstracttransformer.php";s:4:"7996";s:65:"classes/transformer/class.tx_feuserregister_transformer_br2nl.php";s:4:"1947";s:64:"classes/transformer/class.tx_feuserregister_transformer_date.php";s:4:"ea4a";s:76:"classes/transformer/class.tx_feuserregister_transformer_htmlspecialchars.php";s:4:"a3b7";s:63:"classes/transformer/class.tx_feuserregister_transformer_md5.php";s:4:"e9cb";s:65:"classes/transformer/class.tx_feuserregister_transformer_nl2br.php";s:4:"6332";s:69:"classes/transformer/class.tx_feuserregister_transformer_striptags.php";s:4:"cee6";s:74:"classes/transformer/class.tx_feuserregister_transformer_t3sec_saltedpw.php";s:4:"1759";s:69:"classes/transformer/class.tx_feuserregister_transformer_timestamp.php";s:4:"488f";s:60:"interfaces/interface.tx_feuserregister_interface_command.php";s:4:"ff94";s:63:"interfaces/interface.tx_feuserregister_interface_observable.php";s:4:"eb15";s:61:"interfaces/interface.tx_feuserregister_interface_observer.php";s:4:"2231";s:48:"hooks/class.tx_feuserregister_t3libauth_hook.php";s:4:"2b04";s:23:"resources/blacklist.txt";s:4:"6f4a";s:31:"resources/template_confirm.html";s:4:"f7d7";s:28:"resources/template_edit.html";s:4:"ab01";s:29:"resources/template_error.html";s:4:"73ba";s:29:"resources/template_mails.html";s:4:"e87c";s:32:"resources/template_register.html";s:4:"c2ed";s:23:"resources/whitelist.txt";s:4:"d41d";s:51:"resources/icons/icon_tx_feuserregister_fe_users.gif";s:4:"2356";s:35:"static/feuserregister/constants.txt";s:4:"9a1b";s:31:"static/feuserregister/setup.txt";s:4:"2dcb";s:21:"lang/locallang_db.xml";s:4:"3688";s:25:"lang/locallang_emails.xml";s:4:"0f30";s:25:"lang/locallang_fields.xml";s:4:"fb99";s:29:"lang/locallang_validators.xml";s:4:"0f87";s:52:"model/class.tx_feuserregister_model_abstractstep.php";s:4:"a81d";s:47:"model/class.tx_feuserregister_model_dbtable.php";s:4:"46ca";s:46:"model/class.tx_feuserregister_model_feuser.php";s:4:"9a8c";s:45:"model/class.tx_feuserregister_model_field.php";s:4:"8f35";s:47:"model/class.tx_feuserregister_model_preview.php";s:4:"ed9a";s:51:"model/class.tx_feuserregister_model_sessionuser.php";s:4:"71fa";s:44:"model/class.tx_feuserregister_model_step.php";s:4:"fdb0";s:51:"model/class.tx_feuserregister_model_stepmanager.php";s:4:"8b42";s:47:"model/class.tx_feuserregister_model_success.php";s:4:"44cb";s:56:"exceptions/class.tx_feuserregister_exception_confirm.php";s:4:"63bd";s:57:"exceptions/class.tx_feuserregister_exception_database.php";s:4:"9db8";s:54:"exceptions/class.tx_feuserregister_exception_field.php";s:4:"ccfa";s:60:"exceptions/class.tx_feuserregister_exception_stepmanager.php";s:4:"6e9d";s:52:"exceptions/class.tx_feuserregister_exception_tca.php";s:4:"2445";s:60:"exceptions/class.tx_feuserregister_exception_transformer.php";s:4:"57ca";}',
	'suggests' => array(
	),
);

?>
