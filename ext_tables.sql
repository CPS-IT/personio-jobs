#
# Table structure for table 'tx_personiojobs_domain_model_job'
#
CREATE TABLE tx_personiojobs_domain_model_job(
	personio_id int(11) unsigned default '0' not null,
	name text,
	job_descriptions varchar(255) default '' not null,
	recruiting_category varchar(255) default '' not null,
	keywords text,
	create_date int(11) default '0' not null,
	content_hash varchar(255) default '' not null,

	key personio_id(personio_id),
);

#
# Table structure for table 'tx_personiojobs_domain_model_job_description'
#
CREATE TABLE tx_personiojobs_domain_model_job_description(
	header text,
	bodytext mediumtext,
	job int(11) unsigned default '0' not null,
);
