#
# Table structure for table 'tx_personiojobs_domain_model_job'
#
CREATE TABLE tx_personiojobs_domain_model_job(
	name text,
	keywords text,

	key personio_id(personio_id),
);

#
# Table structure for table 'tx_personiojobs_domain_model_job_description'
#
CREATE TABLE tx_personiojobs_domain_model_job_description(
	header text,
);
