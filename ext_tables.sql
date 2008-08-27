CREATE TABLE fe_users (
  static_info_country char(3) DEFAULT '' NOT NULL,
  zone varchar(45) DEFAULT '' NOT NULL,
  language char(2) DEFAULT '' NOT NULL,
  gender int(11) unsigned DEFAULT '0' NOT NULL,
  name varchar(100) DEFAULT '' NOT NULL,
  first_name varchar(50) DEFAULT '' NOT NULL,
  last_name varchar(50) DEFAULT '' NOT NULL,
  status int(11) unsigned DEFAULT '0' NOT NULL,
  country varchar(60) DEFAULT '' NOT NULL,
  zip varchar(20) DEFAULT '' NOT NULL,
  date_of_birth int(11) DEFAULT '0' NOT NULL,
  comments text NOT NULL,
  by_invitation tinyint(4) unsigned DEFAULT '0' NOT NULL,
  doubleoptin_code varchar(150) DEFAULT '' NOT NULL,
  doubleoptin_code_user varchar(150) DEFAULT '' NOT NULL,
  doubleoptin_code_admin varchar(150) DEFAULT '' NOT NULL,
  doubleoptin_confirmed_user varchar(1) DEFAULT '0' NOT NULL,
  doubleoptin_confirmed_admin varchar(1) DEFAULT '0' NOT NULL,
  doubleoptin_code_confirmed_user varchar(1) DEFAULT '0' NOT NULL,
  doubleoptin_code_confirmed_admin varchar(1) DEFAULT '0' NOT NULL,
module_sys_dmail_html tinyint(3) unsigned DEFAULT '0' NOT NULL
);
