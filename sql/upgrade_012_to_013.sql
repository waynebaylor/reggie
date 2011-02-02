
-- ------------------------------
-- add date cancelled to reports
-- ------------------------------

alter table
	Report
add column
	showDateCancelled varchar(255) not null default 'false';
