#
# $Id: ext_tables.sql 18083 2009-03-19 21:01:59Z neoblack $
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