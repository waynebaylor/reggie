

alter table
	Report
add column
	`type` varchar(255) not null default 'STANDARD';
	
update Report
set `type` = 'PAYMENTS_TO_DATE'
where isPaymentsToDate = 'T';
   
update Report
set `type` = 'ALL_REG_TO_DATE'
where isAllRegToDate = 'T'; 

update Report
set `type` = 'OPTION_COUNTS'
where isOptionCount = 'T'; 

update Report
set `type` = 'REG_TYPE_BREAKDOWN'
where isRegTypeBreakdown = 'T'; 


alter table Report
drop column isPaymentsToDate;

alter table Report
drop column isAllRegToDate;

alter table Report
drop column isOptionCount;

alter table Report
drop column isRegTypeBreakdown;



