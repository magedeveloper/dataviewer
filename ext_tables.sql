#
# Table structure for table 'tx_dataviewer_domain_model_datatype'
#
CREATE TABLE tx_dataviewer_domain_model_datatype (

	uid int(11) NOT NULL auto_increment,
	pid int(11) DEFAULT '0' NOT NULL,

	name varchar(255) DEFAULT '' NOT NULL,
	description text NOT NULL,
	icon varchar(255) DEFAULT '' NOT NULL,
	fields int(11) unsigned DEFAULT '0' NOT NULL,
    tab_config text,
	color varchar(255) DEFAULT '' NOT NULL,
	hide_records tinyint(1) unsigned DEFAULT '0' NOT NULL,
	hide_add tinyint(1) unsigned DEFAULT '0' NOT NULL,
	title_divider varchar(100) DEFAULT ' ' NOT NULL,
	tablename varchar(255) DEFAULT '' NOT NULL,
	disable_general_tab tinyint(1) unsigned DEFAULT '0' NOT NULL,
    thumbnail_field int(11) unsigned DEFAULT '0' NOT NULL,
    enable_seo tinyint(1) unsigned DEFAULT '1' NOT NULL,
    cache_tca tinyint(1) unsigned DEFAULT '1' NOT NULL,

	tstamp int(11) unsigned DEFAULT '0' NOT NULL,
	crdate int(11) unsigned DEFAULT '0' NOT NULL,
	cruser_id int(11) unsigned DEFAULT '0' NOT NULL,
	deleted tinyint(4) unsigned DEFAULT '0' NOT NULL,
	hidden tinyint(4) unsigned DEFAULT '0' NOT NULL,
	starttime int(11) unsigned DEFAULT '0' NOT NULL,
	endtime int(11) unsigned DEFAULT '0' NOT NULL,

	t3ver_oid int(11) DEFAULT '0' NOT NULL,
	t3ver_id int(11) DEFAULT '0' NOT NULL,
	t3ver_wsid int(11) DEFAULT '0' NOT NULL,
	t3ver_label varchar(255) DEFAULT '' NOT NULL,
	t3ver_state tinyint(4) DEFAULT '0' NOT NULL,
	t3ver_stage int(11) DEFAULT '0' NOT NULL,
	t3ver_count int(11) DEFAULT '0' NOT NULL,
	t3ver_tstamp int(11) DEFAULT '0' NOT NULL,
	t3ver_move_id int(11) DEFAULT '0' NOT NULL,
	sorting int(11) DEFAULT '0' NOT NULL,

	sys_language_uid int(11) DEFAULT '0' NOT NULL,
	l10n_parent int(11) DEFAULT '0' NOT NULL,
	l10n_diffsource mediumblob,

	PRIMARY KEY (uid),
	KEY parent (pid),
	KEY t3ver_oid (t3ver_oid,t3ver_wsid),
    KEY language (l10n_parent,sys_language_uid)
);

#
# Table structure for table 'tx_dataviewer_domain_model_field'
#
CREATE TABLE tx_dataviewer_domain_model_field (

	uid int(11) NOT NULL auto_increment,
	pid int(11) DEFAULT '0' NOT NULL,

	id varchar(255) DEFAULT '' NOT NULL,
	type varchar(255) DEFAULT 'input' NOT NULL,
    field_conf text,
	description text,
	frontend_label varchar(255) DEFAULT '' NOT NULL,
	frontend_type varchar(255) DEFAULT '' NOT NULL,
    backend_searchable tinyint(1) unsigned DEFAULT '0' NOT NULL,
    is_object_storage tinyint(1) unsigned DEFAULT '0' NOT NULL,
	variable_name varchar(255) DEFAULT '' NOT NULL,
	css_class varchar(255) DEFAULT '' NOT NULL,
	show_title tinyint(1) unsigned DEFAULT '1' NOT NULL,
	field_values int(11) unsigned DEFAULT '0' NOT NULL,
    is_record_title tinyint(1) unsigned DEFAULT '0' NOT NULL,
    use_as_path_segment tinyint(1) unsigned DEFAULT '0' NOT NULL,
 	validation text,
 	display_cond text,
	request_update tinyint(1) unsigned DEFAULT '0' NOT NULL,
	is_active tinyint(1) unsigned DEFAULT '0' NOT NULL,
	exclude tinyint(1) unsigned DEFAULT '0' NOT NULL,
	l10n_exclude tinyint(1) unsigned DEFAULT '0' NOT NULL,
	is_index tinyint(1) unsigned DEFAULT '0' NOT NULL,
	database_type varchar(255) DEFAULT '' NOT NULL,
    palette varchar(255) DEFAULT '' NOT NULL,
    cache_tca tinyint(1) unsigned DEFAULT '0' NOT NULL,

	tstamp int(11) unsigned DEFAULT '0' NOT NULL,
	crdate int(11) unsigned DEFAULT '0' NOT NULL,
	cruser_id int(11) unsigned DEFAULT '0' NOT NULL,
	deleted tinyint(4) unsigned DEFAULT '0' NOT NULL,
	hidden tinyint(4) unsigned DEFAULT '0' NOT NULL,
	starttime int(11) unsigned DEFAULT '0' NOT NULL,
	endtime int(11) unsigned DEFAULT '0' NOT NULL,

	t3ver_oid int(11) DEFAULT '0' NOT NULL,
	t3ver_id int(11) DEFAULT '0' NOT NULL,
	t3ver_wsid int(11) DEFAULT '0' NOT NULL,
	t3ver_label varchar(255) DEFAULT '' NOT NULL,
	t3ver_state tinyint(4) DEFAULT '0' NOT NULL,
	t3ver_stage int(11) DEFAULT '0' NOT NULL,
	t3ver_count int(11) DEFAULT '0' NOT NULL,
	t3ver_tstamp int(11) DEFAULT '0' NOT NULL,
	t3ver_move_id int(11) DEFAULT '0' NOT NULL,
	sorting int(11) DEFAULT '0' NOT NULL,

	sys_language_uid int(11) DEFAULT '0' NOT NULL,
	l10n_parent int(11) DEFAULT '0' NOT NULL,
	l10n_diffsource mediumblob,

	PRIMARY KEY (uid),
	KEY parent (pid),
	KEY t3ver_oid (t3ver_oid,t3ver_wsid),
    KEY language (l10n_parent,sys_language_uid)

);

#
# Table structure for table 'tx_dataviewer_domain_model_fieldvalue'
#
CREATE TABLE tx_dataviewer_domain_model_fieldvalue (

	uid int(11) NOT NULL auto_increment,
	pid int(11) DEFAULT '0' NOT NULL,

	field int(11) unsigned DEFAULT '0' NOT NULL,

	type varchar(30) DEFAULT '0' NOT NULL,
	value_content text,
	field_content int(11) unsigned DEFAULT '0',
	table_content varchar(255) DEFAULT '' NOT NULL,
	column_name varchar(255) DEFAULT '' NOT NULL,
	where_clause text,
	pass_to_fe tinyint(1) unsigned DEFAULT '0' NOT NULL,
	is_readonly tinyint(1) unsigned DEFAULT '0' NOT NULL,
	is_default tinyint(1) unsigned DEFAULT '0' NOT NULL,
	pretends_empty tinyint(1) unsigned DEFAULT '0' NOT NULL,

	tstamp int(11) unsigned DEFAULT '0' NOT NULL,
	crdate int(11) unsigned DEFAULT '0' NOT NULL,
	cruser_id int(11) unsigned DEFAULT '0' NOT NULL,
	deleted tinyint(4) unsigned DEFAULT '0' NOT NULL,
	hidden tinyint(4) unsigned DEFAULT '0' NOT NULL,
	starttime int(11) unsigned DEFAULT '0' NOT NULL,
	endtime int(11) unsigned DEFAULT '0' NOT NULL,

	t3ver_oid int(11) DEFAULT '0' NOT NULL,
	t3ver_id int(11) DEFAULT '0' NOT NULL,
	t3ver_wsid int(11) DEFAULT '0' NOT NULL,
	t3ver_label varchar(255) DEFAULT '' NOT NULL,
	t3ver_state tinyint(4) DEFAULT '0' NOT NULL,
	t3ver_stage int(11) DEFAULT '0' NOT NULL,
	t3ver_count int(11) DEFAULT '0' NOT NULL,
	t3ver_tstamp int(11) DEFAULT '0' NOT NULL,
	t3ver_move_id int(11) DEFAULT '0' NOT NULL,
	sorting int(11) DEFAULT '0' NOT NULL,

	sys_language_uid int(11) DEFAULT '0' NOT NULL,
	l10n_parent int(11) DEFAULT '0' NOT NULL,
	l10n_diffsource mediumblob,

	PRIMARY KEY (uid),
	KEY parent (pid),
	KEY field (field),
	KEY t3ver_oid (t3ver_oid,t3ver_wsid),
    KEY language (l10n_parent,sys_language_uid)

);

#
# Table structure for table 'tx_dataviewer_domain_model_variable'
#
CREATE TABLE tx_dataviewer_domain_model_variable (

	uid int(11) NOT NULL auto_increment,
	pid int(11) DEFAULT '0' NOT NULL,

	type int(11) DEFAULT '0' NOT NULL,
	variable_name varchar(255) DEFAULT 'templateVariable' NOT NULL,
    parameter_name varchar(255) DEFAULT '' NOT NULL,
	session_key varchar(255) DEFAULT '' NOT NULL,
	variable_value text,
	table_content varchar(255) DEFAULT '' NOT NULL,
	column_name varchar(255) DEFAULT '' NOT NULL,
	where_clause text,
    server varchar(255) DEFAULT '' NOT NULL,
    page int(11) DEFAULT '0' NOT NULL,
    user_func varchar(255) DEFAULT '' NOT NULL,
    type_cast int(11) DEFAULT '0' NOT NULL,
    allowed_values text,
    regex text,
    value_switch text,
    ext_conf varchar(255) DEFAULT '' NOT NULL,
    typoscript_path varchar(255) DEFAULT '' NOT NULL,
    datatype int(11) unsigned DEFAULT '0' NOT NULL,

	tstamp int(11) unsigned DEFAULT '0' NOT NULL,
	crdate int(11) unsigned DEFAULT '0' NOT NULL,
	cruser_id int(11) unsigned DEFAULT '0' NOT NULL,
	deleted tinyint(4) unsigned DEFAULT '0' NOT NULL,
	hidden tinyint(4) unsigned DEFAULT '0' NOT NULL,
	starttime int(11) unsigned DEFAULT '0' NOT NULL,
	endtime int(11) unsigned DEFAULT '0' NOT NULL,

	t3ver_oid int(11) DEFAULT '0' NOT NULL,
	t3ver_id int(11) DEFAULT '0' NOT NULL,
	t3ver_wsid int(11) DEFAULT '0' NOT NULL,
	t3ver_label varchar(255) DEFAULT '' NOT NULL,
	t3ver_state tinyint(4) DEFAULT '0' NOT NULL,
	t3ver_stage int(11) DEFAULT '0' NOT NULL,
	t3ver_count int(11) DEFAULT '0' NOT NULL,
	t3ver_tstamp int(11) DEFAULT '0' NOT NULL,
	t3ver_move_id int(11) DEFAULT '0' NOT NULL,

	sys_language_uid int(11) DEFAULT '0' NOT NULL,
	l10n_parent int(11) DEFAULT '0' NOT NULL,
	l10n_diffsource mediumblob,

	PRIMARY KEY (uid),
	KEY parent (pid),
	KEY t3ver_oid (t3ver_oid,t3ver_wsid),
	KEY language (l10n_parent,sys_language_uid)

);

#
# Table structure for table 'tx_dataviewer_datatype_field_mm'
#
CREATE TABLE tx_dataviewer_datatype_field_mm (

	uid_local int(11) unsigned DEFAULT '0' NOT NULL,
	uid_foreign int(11) unsigned DEFAULT '0' NOT NULL,
	sorting int(11) unsigned DEFAULT '0' NOT NULL,
	sorting_foreign int(11) unsigned DEFAULT '0' NOT NULL,

	KEY uid_local (uid_local),
	KEY uid_foreign (uid_foreign)
);