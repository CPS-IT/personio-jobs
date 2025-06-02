# @todo Remove auto-created DB tables once support for TYPO3 v12 is dropped,
#	      see https://docs.typo3.org/c/typo3/cms-core/main/en-us/Changelog/13.0/Feature-101553-Auto-createDBFieldsFromTCAColumns.html

#
# Table structure for table 'tx_personiojobs_domain_model_job'
#
CREATE TABLE tx_personiojobs_domain_model_job(
	personio_id int(11) unsigned default '0' not null,
	subcompany varchar(255) default '' not null,
	office varchar(255) default '' not null,
	department varchar(255) default '' not null,
	recruiting_category varchar(255) default '' not null,
	name text,
	slug varchar(2048) default '' not null,
	job_descriptions varchar(255) default '' not null,
	employment_type varchar(255) default '' not null,
	seniority varchar(255) default '' not null,
	schedule varchar(255) default '' not null,
	years_of_experience varchar(255) default '' not null,
	keywords text,
	occupation varchar(255) default '' not null,
	occupation_category varchar(255) default '' not null,
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
