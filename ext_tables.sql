#
# Table structure for table 'tx_plainfaq_domain_model_faq'
#
CREATE TABLE tx_plainfaq_domain_model_faq (
	question varchar(255) DEFAULT '' NOT NULL,
	answer text,
	keywords text,
	images int(11) unsigned DEFAULT '0' NOT NULL,
	files int(11) unsigned DEFAULT '0' NOT NULL,
	categories int(11) unsigned DEFAULT '0' NOT NULL,
	related int(11) DEFAULT '0' NOT NULL,
	slug varchar(2048),
);
