
create table if not exists `Feedback` (
    `id`                integer         not null auto_increment,
    `feedback`          text            not null,
    `type`              varchar(255)    not null,
    `status`            varchar(255)    not null,
    primary key(`id`)
) ENGINE=InnoDB default CHARSET=utf8;

-- ----------------------------------------------------------------------

create table if not exists `Report_SpecialField` (
    `id`                integer         not null auto_increment,
    `reportId`          integer         not null,
    `name`              varchar(255)    not null,
    `displayName`       varchar(255)    not null,
    primary key(`id`)
) ENGINE=InnoDB default CHARSET=utf8;


alter table Report_SpecialField
    add constraint rpt_spclFld_rptId_fk
    foreign key (reportId) references Report(id);
    
alter table Report_SpecialField
    add constraint rpt_sclFld_rpt_name_uni
    unique (reportId, name);    
    
    

insert into Report_SpecialField (reportId, name, displayName)
    select id as reportId, 'DATE_REGISTERED' as name, 'Date Registered' as displayName
    from Report 
    where showDateRegistered = 'T';
    
insert into Report_SpecialField (reportId, name, displayName)
    select id as reportId, 'DATE_CANCELLED' as name, 'Date Cancelled' as displayName
    from Report 
    where showDateCancelled = 'T';
    
insert into Report_SpecialField (reportId, name, displayName)
    select id as reportId, 'CATEGORY' as name, 'Category' as displayName
    from Report 
    where showCategory = 'T';
    
insert into Report_SpecialField (reportId, name, displayName)
    select id as reportId, 'REGISTRATION_TYPE' as name, 'Registration Type' as displayName
    from Report 
    where showRegType = 'T';
    
insert into Report_SpecialField (reportId, name, displayName)
    select id as reportId, 'LEAD_NUMBER' as name, 'Lead Number' as displayName
    from Report 
    where showLeadNumber = 'T';
    
insert into Report_SpecialField (reportId, name, displayName)
    select id as reportId, 'TOTAL_COST' as name, 'Total Cost' as displayName
    from Report 
    where showTotalCost = 'T';
    
insert into Report_SpecialField (reportId, name, displayName)
    select id as reportId, 'TOTAL_PAID' as name, 'Total Paid' as displayName
    from Report 
    where showTotalPaid = 'T';
    
insert into Report_SpecialField (reportId, name, displayName)
    select id as reportId, 'REMAINING_BALANCE' as name, 'Remaining Balance' as displayName
    from Report 
    where showRemainingBalance = 'T';


alter table Report
drop column showDateRegistered;

alter table Report
drop column showDateCancelled;

alter table Report
drop column showCategory;

alter table Report
drop column showRegType;

alter table Report
drop column showLeadNumber;

alter table Report
drop column showTotalCost;

alter table Report
drop column showTotalPaid;

alter table Report
drop column showRemainingBalance;





-- ------------------------------------------------------------

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



