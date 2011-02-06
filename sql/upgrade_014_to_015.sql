
-- ============================
-- email templates based on reg type
-- ============================ 

alter table
	EmailTemplate
modify
	contactFieldId integer not null;

-- need to drop this before we can drop the unique index.
alter table
	EmailTemplate
drop foreign key
	emailTemplate_eventId_fk;

alter table
	EmailTemplate
drop index
	emailTemplate_eventId_uni;

-- now we can put this back in place.
alter table 
	EmailTemplate
add constraint
	emailTemplate_eventId_fk
foreign key
	(eventId)
references
	Event(id);

-- mapping table from reg type to email template.
create table if not exists `RegType_EmailTemplate` (
	`id`			integer 	not null auto_increment,
	`regTypeId`		integer,
	`emailTemplateId`	integer		not null,
	primary key(`id`)
) ENGINE=InnoDB default CHARSET=utf8;

alter table
	RegType_EmailTemplate
add constraint
	regType_emailTemplate_regTypeId_fk
foreign key
	(regTypeId)
references
	RegType(id);

alter table
	RegType_EmailTemplate
add constraint
	regType_emailTemplate_emailTempId_fk
foreign key
	(emailTemplateId)
references
	EmailTemplate(id);

alter table
	RegType_EmailTemplate
add constraint
	regType_emailTemplate_typeTemplate_uni
unique
	(regTypeId, emailTemplateId);

-- create mapping for existing email templates.
insert into
	RegType_EmailTemplate (emailTemplateId)
select 
	id 
from 
	EmailTemplate;

	



