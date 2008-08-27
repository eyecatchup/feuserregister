<?php

########################################################################
# Extension Manager/Repository config file for ext: "feuserregister"
#
# Auto generated 19-08-2008 16:16
#
# Manual updates:
# Only the data in the array - anything else is removed by next write.
# "version" and "dependencies" must not be touched!
########################################################################

$EM_CONF[$_EXTKEY] = array(
	'title' => 'Frontend user registration',
	'description' => 'New frontend user registrationmodule using lib/div',
	'category' => 'plugin',
	'author' => 'Cross Content Media / e-netconsulting KG',
	'author_email' => 'dev@cross-content.com,team@e-netconsulting.de',
	'shy' => '',
	'dependencies' => 'cms,fn_lib,lib,rtp_smarty,div',
	'conflicts' => '',
	'priority' => '',
	'module' => '',
	'state' => 'beta',
	'internal' => '',
	'uploadfolder' => 0,
	'createDirs' => '',
	'modify_tables' => '',
	'clearCacheOnLoad' => 0,
	'lockType' => '',
	'author_company' => '',
	'version' => '0.10.22',
	'constraints' => array(
		'depends' => array(
			'cms' => '',
			'fn_lib' => '',
			'lib' => '',
			'rtp_smarty' => '',
			'div' => '',
		),
		'conflicts' => array(
		),
		'suggests' => array(
		),
	),
	'_md5_values_when_last_written' => 'a:64:{s:9:"ChangeLog";s:4:"32c6";s:10:"README.txt";s:4:"9fa9";s:10:"ce_wiz.gif";s:4:"02b6";s:12:"ext_icon.gif";s:4:"1bdc";s:17:"ext_localconf.php";s:4:"63f1";s:14:"ext_tables.php";s:4:"35ed";s:14:"ext_tables.sql";s:4:"c707";s:13:"locallang.xml";s:4:"c552";s:16:"locallang_db.xml";s:4:"f08a";s:16:"static/setup.txt";s:4:"d41d";s:19:"doc/wizard_form.dat";s:4:"b859";s:20:"doc/wizard_form.html";s:4:"6cc7";s:66:"templates/feuserregister_after_resend_confirmation_emailgiven.tmpl";s:4:"6649";s:69:"templates/feuserregister_after_resend_confirmation_usernamegiven.tmpl";s:4:"2b4f";s:40:"templates/feuserregister_after_save.tmpl";s:4:"bbb3";s:58:"templates/feuserregister_confirmresend_mail_user_html.tmpl";s:4:"b200";s:58:"templates/feuserregister_confirmresend_mail_user_text.tmpl";s:4:"2f06";s:35:"templates/feuserregister_create.php";s:4:"418c";s:36:"templates/feuserregister_create.tmpl";s:4:"805e";s:43:"templates/feuserregister_create_step_1.tmpl";s:4:"73af";s:43:"templates/feuserregister_create_step_2.tmpl";s:4:"dcd7";s:43:"templates/feuserregister_create_step_3.tmpl";s:4:"9385";s:33:"templates/feuserregister_edit.php";s:4:"6695";s:34:"templates/feuserregister_edit.tmpl";s:4:"766d";s:45:"templates/feuserregister_edit_after_save.tmpl";s:4:"da07";s:41:"templates/feuserregister_email_admin.tmpl";s:4:"1c1e";s:47:"templates/feuserregister_error_user_exists.tmpl";s:4:"6727";s:60:"templates/feuserregister_error_user_exists_reload_error.tmpl";s:4:"1eb0";s:45:"templates/feuserregister_mail_admin_html.tmpl";s:4:"c178";s:46:"templates/feuserregister_mail_admin_plain.tmpl";s:4:"a540";s:59:"templates/feuserregister_mail_user_confirmrequest_html.tmpl";s:4:"8167";s:59:"templates/feuserregister_mail_user_confirmrequest_text.tmpl";s:4:"2f06";s:36:"templates/feuserregister_preview.php";s:4:"6695";s:37:"templates/feuserregister_preview.tmpl";s:4:"805e";s:44:"templates/feuserregister_preview_step_1.tmpl";s:4:"73af";s:44:"templates/feuserregister_preview_step_2.tmpl";s:4:"dcd7";s:44:"templates/feuserregister_preview_step_3.tmpl";s:4:"9385";s:49:"templates/feuserregister_resend_confirmation.tmpl";s:4:"74e8";s:56:"configurations/class.tx_feuserregister_configuration.php";s:4:"0e1d";s:60:"configurations/mvc1/class.tx_feuserregister_mvc1_wizicon.php";s:4:"914f";s:32:"configurations/mvc1/flexform.xml";s:4:"4817";s:29:"configurations/mvc1/setup.txt";s:4:"523c";s:50:"translators/class.tx_feuserregister_translator.php";s:4:"1629";s:41:"lib/class.tx_feuserregister_functions.php";s:4:"a938";s:19:"lib/validatorlib.js";s:4:"f683";s:29:"lib/validation/fabtabulous.js";s:4:"d262";s:25:"lib/validation/index.html";s:4:"ed35";s:24:"lib/validation/style.css";s:4:"e317";s:28:"lib/validation/validation.js";s:4:"ca86";s:38:"lib/validation/scriptaculous/CHANGELOG";s:4:"ace0";s:40:"lib/validation/scriptaculous/MIT-LICENSE";s:4:"6444";s:35:"lib/validation/scriptaculous/README";s:4:"5c5d";s:43:"lib/validation/scriptaculous/src/builder.js";s:4:"5d00";s:44:"lib/validation/scriptaculous/src/controls.js";s:4:"dfd8";s:44:"lib/validation/scriptaculous/src/dragdrop.js";s:4:"8c2c";s:43:"lib/validation/scriptaculous/src/effects.js";s:4:"0d8f";s:49:"lib/validation/scriptaculous/src/scriptaculous.js";s:4:"b4e1";s:42:"lib/validation/scriptaculous/src/slider.js";s:4:"7df7";s:44:"lib/validation/scriptaculous/src/unittest.js";s:4:"cbdd";s:45:"lib/validation/scriptaculous/lib/prototype.js";s:4:"a553";s:53:"views/class.tx_feuserregister_view_feuserregister.php";s:4:"3663";s:48:"models/class.tx_feuserregister_model_fe_user.php";s:4:"b997";s:65:"models/class.tx_feuserregister_model_frontenduserregistration.php";s:4:"aa6f";s:75:"controllers/class.tx_feuserregister_controller_frontenduserregistration.php";s:4:"49ce";}',
	'suggests' => array(
	),
);

?>