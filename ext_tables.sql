#
# $Id: ext_tables.sql 343 2009-03-18 21:15:57Z franae $
#

#
# Table structure for table 'fe_users'
#
CREATE TABLE fe_users (
	tx_feuserregister_birthday int(11) DEFAULT '0' NOT NULL,
	tx_feuserregister_gender char(1) DEFAULT '' NOT NULL,
	tx_feuserregister_firstname varchar(255) DEFAULT '' NOT NULL,
	tx_feuserregister_lastname varchar(255) DEFAULT '' NOT NULL,
	tx_feuserregister_temporarydata text DEFAULT ''
);