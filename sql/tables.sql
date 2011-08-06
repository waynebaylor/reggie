
set foreign_key_checks = 0;

drop table if exists User_Role;
drop table if exists Role;
drop table if exists BadgeBarcodeField;
drop table if exists BadgeCell_TextContent;
drop table if exists BadgeCell;
drop table if exists BadgeTemplate_RegType;
drop table if exists BadgeTemplate;
drop table if exists StaticPage;
drop table if exists RegType_EmailTemplate;
drop table if exists GroupRegistration_ContactField;
drop table if exists GroupRegistration;
drop table if exists `User`;
drop table if exists Report_ContactField;
drop table if exists Report;
drop table if exists Payment;
drop table if exists AuthorizeNetDirections;
drop table if exists CheckDirections;
drop table if exists PurchaseOrderDirections;
drop table if exists PaymentType;
drop table if exists Registration_VariableQuantityOption;
drop table if exists Registration_RegOption;
drop table if exists Registration_Information;
drop table if exists Registration;
drop table if exists RegistrationGroup;
drop table if exists Appearance;
drop table if exists EmailTemplate;
drop table if exists VariableQuantityOption_RegOptionPrice;
drop table if exists VariableQuantityOption;
drop table if exists RegType_RegOptionPrice;
drop table if exists ContactFieldOption;
drop table if exists Category_Page;
drop table if exists FormInputAttribute;
drop table if exists FormInputValidation;
drop table if exists ContactFieldValidation;
drop table if exists Validation;
drop table if exists ContactFieldAttribute;
drop table if exists Attribute;
drop table if exists CategoryRegType;
drop table if exists RegTypeContactField;
drop table if exists ContactField;
drop table if exists FormInput;
drop table if exists Category;
drop table if exists RegOption_RegOptionPrice;
drop table if exists RegOptionPrice;
drop table if exists RegType;
drop table if exists RegOption;
drop table if exists RegOptionGroup;
drop table if exists Section;
drop table if exists Page;
drop table if exists Event;
drop table if exists ContentType;

set foreign_key_checks = 1;

-- --------------------------------------------------
-- CREATE TABLES
-- --------------------------------------------------

create table if not exists `ContentType` (
	`id` 	integer 	not null,
	`name` 	varchar(255) 	not null,
	primary key (`id`)
) ENGINE=InnoDB default CHARSET=utf8;

-- --------------------------------------------------
  
CREATE TABLE IF NOT EXISTS `Event` (
	`id` 			integer 	not null auto_increment,
	`code` 			varchar(255) 	not null COMMENT 'short way to identify the event; used in the registration URLs',
	`displayName` 		varchar(255) 	not null COMMENT 'human readable name for the event',
	`regOpen` 		datetime 	not null,
	`regClosed` 		datetime 	not null,
	`capacity`		integer		not null,
	`confirmationText`	text		not null,
	`cancellationPolicy` 	text 		not null,
	`regClosedText` 	text 		not null,
	`paymentInstructions`	text		not null,
	PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ;
	
-- --------------------------------------------------------------------------
	
create table if not exists `Page` (
	`id` 		integer 	not null auto_increment,
	`eventId` 	integer 	not null,
	`title` 	varchar(255) 	not null,
	`displayOrder` 	integer 	not null,
	primary key (`id`)
) ENGINE=InnoDB default CHARSET=utf8;

-- --------------------------------------------------------------------------
	
create table if not exists `Section` (
	`id` 		integer 	not null auto_increment,
	`eventId`	integer		not null,
	`pageId` 	integer 	not null,
	`name`	 	varchar(255)	not null,
	`text`		text,
	`numbered`	char(1)		not null default 'F',
	`contentTypeId` integer 	not null,
	`displayOrder` 	integer 	not null,
	primary key (`id`)
) ENGINE=InnoDB default CHARSET=utf8;

	
-- --------------------------------------------------------------------------

create table if not exists `RegOptionGroup` (
	`id` 		integer 	not null auto_increment,
	`eventId`	integer		not null,
	`sectionId`	integer 			comment 'either sectionId or regOptionId will be set',
	`regOptionId`	integer 			comment 'either sectionId or regOptionId will be set',
	`displayOrder`	integer		not null 	comment 'unique with reference to sectionId or regOptionId',
	`required` 	char(1) 	not null,
	`multiple` 	char(1) 	not null,
	`minimum`	integer		not null,
	`maximum`	integer		not null,
	primary key (`id`)
) ENGINE=InnoDB default CHARSET=utf8;

-- --------------------------------------------------------------------------

create table if not exists `RegOption` (
	`id` 			integer 	not null auto_increment,
	`eventId`		integer		not null,
	`parentGroupId` 	integer 	not null,
	`code` 			varchar(255) 	not null,
	`description`		varchar(255) 	not null,
	`capacity` 		integer 	not null,
	`defaultSelected`	char(1)		not null, 
	`showPrice`		char(1)		not null,
	`displayOrder` 		integer 	not null,
	primary key (`id`)
) ENGINE=InnoDB default CHARSET=utf8;

	
-- --------------------------------------------------------------------------

create table if not exists `RegType` (
	`id` 		integer 	not null auto_increment,
	`eventId` 	integer 	not null,
	`sectionId` 	integer 	not null,
	`code` 		varchar(255) 	not null,
	`description` 	varchar(255) 	not null,
	`displayOrder` 	integer 	not null,
	PRIMARY KEY (`id`)
) ENGINE=InnoDB default CHARSET=utf8;

-- --------------------------------------------------------------------------

create table if not exists `RegOptionPrice` (
	`id` 		integer 	not null auto_increment,
	`eventId`	integer		not null,
	`startDate` 	datetime 	not null,
	`endDate` 	datetime 	not null,
	`price` 	decimal(10,2) 	not null,
	`description` 	varchar(255) 	not null,  
	PRIMARY KEY (`id`)
) ENGINE=InnoDB default CHARSET=utf8;

-- --------------------------------------------------------------------------

create table if not exists `RegOption_RegOptionPrice` (
	`id`			integer		not null auto_increment,
	`regOptionId`		integer		not null,
	`regOptionPriceId`	integer		not null,
	primary key (`id`)
) ENGINE=InnoDB default CHARSET=utf8;

-- --------------------------------------------------------------------------

create table if not exists `Category` (
	`id` 		integer 	not null auto_increment,
	`displayName` 	varchar(255) 	not null,
	PRIMARY KEY (`id`)
) ENGINE=InnoDB default CHARSET=utf8;

-- --------------------------------------------------------------------------
	
create table if not exists `FormInput` (
	`id` 		integer 	not null,
	`name` 		varchar(255) 	not null,
	`displayName` 	varchar(255) 	not null,
	primary key (`id`)
) ENGINE=InnoDB default CHARSET=utf8;

-- --------------------------------------------------------------------------

create table if not exists `ContactField` (
	`id` 		integer 	not null auto_increment,
	`code` 		varchar(255) 	not null,
	`eventId`	integer		not null,
	`sectionId` 	integer 	not null,
	`formInputId` 	integer 	not null,
	`displayName` 	varchar(255) 	not null,
	`displayOrder` 	integer 	not null,
	PRIMARY KEY (`id`)
) ENGINE=InnoDB default CHARSET=utf8;

	
-- --------------------------------------------------------------------------

create table if not exists `RegTypeContactField` (
	`id` 			integer 	not null auto_increment,
	`regTypeId` 		integer				comment 'if regTeypId is NULL, then the contact field belongs to all event reg types',
	`contactFieldId` 	integer 	not null,
	primary key (`id`)
) ENGINE=InnoDB default CHARSET=utf8;

-- --------------------------------------------------------------------------
		
create table if not exists `CategoryRegType` (
	`id` 		integer 	not null auto_increment,
	`categoryId` 	integer 	not null,
	`regTypeId` 	integer 	not null,
	primary key (`id`)
) ENGINE=InnoDB default CHARSET=utf8;

-- --------------------------------------------------------------------------
	
create table if not exists `Attribute` (
	`id` 		integer 	not null,
	`name` 		varchar(255) 	not null,
	`displayName` 	varchar(255) 	not null,
	primary key (`id`)
) ENGINE=InnoDB default CHARSET=utf8;

	
-- --------------------------------------------------------------------------
	
create table if not exists `ContactFieldAttribute` (
	`id` 			integer 	not null auto_increment,
	`contactFieldId` 	integer 	not null,
	`attributeId` 		integer 	not null,
	`attrValue` 		varchar(255) 	not null,
	primary key (`id`)
) ENGINE=InnoDB default CHARSET=utf8;

-- --------------------------------------------------------------------------
	
create table if not exists `Validation` (
	`id` 		integer 	not null,
	`name` 		varchar(255) 	not null,
	`displayName` 	varchar(255) 	not null,
	primary key (`id`)
) ENGINE=InnoDB default CHARSET=utf8;

-- --------------------------------------------------------------------------
	
create table if not exists `ContactFieldValidation` (
	`id` 			integer 	not null auto_increment,
	`contactFieldId` 	integer 	not null,
	`validationId` 		integer 	not null,
	`validationValue` 	varchar(255) 	not null,
	primary key (`id`)
) ENGINE=InnoDB default CHARSET=utf8;

		
-- --------------------------------------------------------------------------
	
create table if not exists `FormInputValidation` (
	`id` 		integer 	not null auto_increment,
	`formInputId` 	integer 	not null,
	`validationId` 	integer 	not null,
	primary key (`id`)
) ENGINE=InnoDB default CHARSET=utf8;

	
-- --------------------------------------------------------------------------
	
create table if not exists `FormInputAttribute` (
	`id` 		integer 	not null auto_increment,
	`formInputId` 	integer 	not null,
	`attributeId` 	integer 	not null,
	primary key (`id`)
) ENGINE=InnoDB default CHARSET=utf8;

	
-- --------------------------------------------------

create table if not exists `Category_Page` (
	`id` 		integer 	not null auto_increment,
	`categoryId` 	integer 	not null,
	`pageId` 	integer 	not null,
	primary key (`id`)
) ENGINE=InnoDB default CHARSET=utf8;

	
-- --------------------------------------------------

create table if not exists `ContactFieldOption` (
	`id` 			integer 	not null auto_increment,
	`contactFieldId` 	integer 	not null,
	`displayName` 		varchar(255) 	not null,
	`defaultSelected`	char(1)		not null,
	`displayOrder`		integer 	not null,
	primary key (`id`)
) ENGINE=InnoDB default CHARSET=utf8;
	

-- --------------------------------------------------

create table if not exists `RegType_RegOptionPrice` (
	`id`			integer		not null auto_increment,
	`regTypeId`		integer,
	`regOptionPriceId`	integer		not null,
	primary key(`id`)
) ENGINE=InnoDB default CHARSET=utf8;

-- --------------------------------------------------

create table if not exists `VariableQuantityOption` (
	`id`			integer		not null auto_increment,
	`eventId`		integer		not null,
	`sectionId`		integer		not null,
	`code` 			varchar(255) 	not null,
	`description`		varchar(255) 	not null,
	`capacity` 		integer		not null,
	`displayOrder` 		integer 	not null,
	primary key(`id`)
) ENGINE=InnoDB default CHARSET=utf8;

-- --------------------------------------------------

create table if not exists `VariableQuantityOption_RegOptionPrice` (
	`id`			integer		not null auto_increment,
	`variableQuantityId`	integer		not null,
	`regOptionPriceId`	integer		not null,
	primary key(`id`)
) ENGINE=InnoDB default CHARSET=utf8;

-- --------------------------------------------------

create table if not exists `Appearance` (
	`id`				integer 	not null auto_increment,
	`eventId`			integer		not null,	
	`headerContent` 		text 		not null,
	`footerContent` 		text 		not null,
	`menuTitle`			text		not null,
	`headerBackgroundColor`		varchar(6)	not null,
	`backgroundColor`		varchar(6)	not null,
	`pageBackgroundColor`		varchar(6)	not null,
	`menuTitleBackgroundColor`	varchar(6)	not null,
	`menuBackgroundColor`		varchar(6)	not null,
	`menuHighlightColor`		varchar(6)	not null,
	`formBackgroundColor`		varchar(6)	not null,
	`footerBackgroundColor`		varchar(6)	not null,
	`buttonTextColor`		varchar(6)	not null,
	`buttonBackgroundColor`		varchar(6)	not null,
	primary key(`id`)
) ENGINE=InnoDB default CHARSET=utf8;

-- --------------------------------------------------

create table if not exists `EmailTemplate` (
	`id`			integer 	not null auto_increment,
	`eventId`		integer		not null,
	`contactFieldId`	integer		not null,
	`enabled`		char(1)		not null,
	`fromAddress`		varchar(255),
	`bcc`			varchar(255),
	`subject`		text,
	`header`		text,
	`footer`		text,
	primary key(`id`)
) ENGINE=InnoDB default CHARSET=utf8;

-- --------------------------------------------------

create table if not exists `RegistrationGroup` (
	`id`			integer		not null auto_increment,
	primary key(`id`)
) ENGINE=InnoDB default CHARSET=utf8;

-- --------------------------------------------------

create table if not exists `Registration` (
	`id`			        integer		    not null auto_increment,
	`dateRegistered`	    datetime	    not null,
	`comments`		        text		    not null,
	`dateCancelled`		    datetime,
	`regGroupId`		    integer		    not null, 
	`categoryId`		    integer		    not null,
	`eventId`		        integer		    not null,
	`regTypeId`		        integer		    not null,
	`confirmationNumber`	varchar(255)	not null,
	`leadNumber`            integer(5)      not null,
	primary key(`id`)
) ENGINE=InnoDB default CHARSET=utf8;

-- --------------------------------------------------

create table if not exists `Registration_Information` (
	`id`			integer		not null auto_increment,
	`registrationId`	integer		not null,
	`contactFieldId`	integer		not null,
	`value`			text		not null,
	primary key(`id`)
) ENGINE=InnoDB default CHARSET=utf8;

-- --------------------------------------------------

create table if not exists `Registration_RegOption` (
	`id`			integer		not null auto_increment,
	`registrationId`	integer		not null,
	`regOptionId`		integer		not null,
	`priceId`		integer		not null,
	`dateCancelled`		datetime,
	`dateAdded`		datetime,
	primary key(`id`)
) ENGINE=InnoDB default CHARSET=utf8;

-- --------------------------------------------------

create table if not exists `Registration_VariableQuantityOption` (
	`id`			integer		not null auto_increment,
	`registrationId`	integer		not null,
	`variableQuantityId`	integer		not null,
	`priceId`		integer		not null,
	`quantity`		integer		not null,
	`lastModified`		datetime	not null,
	primary key(`id`)
) ENGINE=InnoDB default CHARSET=utf8;
	

-- --------------------------------------------------

create table if not exists `PaymentType` (
	`id` 			integer		not null auto_increment,
	`displayName`		varchar(255)	not null,
	primary key(`id`)
) ENGINE=InnoDB default CHARSET=utf8;

-- --------------------------------------------------

create table if not exists `CheckDirections` (
	`id`			integer		not null auto_increment,
	`paymentTypeId`		integer		not null default 1	comment 'payment type should always be check', 
	`eventId`		integer		not null,
	`instructions`		text		not null,
	primary key(`id`)
) ENGINE=InnoDB default CHARSET=utf8;

-- --------------------------------------------------

create table if not exists `PurchaseOrderDirections` (
	`id`			integer		not null auto_increment,
	`paymentTypeId`		integer		not null default 2	comment 'payment type should always be purchase order', 
	`eventId`		integer		not null,
	`instructions`		text		not null,
	primary key(`id`)
) ENGINE=InnoDB default CHARSET=utf8;

-- --------------------------------------------------

create table if not exists `AuthorizeNetDirections` (
	`id`			integer		not null auto_increment,
	`paymentTypeId`		integer		not null default 3	comment 'payment type should always be authorize.NET', 
	`eventId`		integer		not null,
	`instructions`		text		not null,
	`login`			varchar(255)	not null,
	`transactionKey`	varchar(255)	not null,
	primary key(`id`)
) ENGINE=InnoDB default CHARSET=utf8;

-- --------------------------------------------------

create table if not exists `Payment` (
	`id`			integer		not null auto_increment,
	`eventId`		integer		not null,
	`paymentTypeId`		integer		not null,
	`regGroupId`		integer		not null,
	`transactionDate`	datetime	not null,
	`paymentReceived`	char(1)		not null default 'F',

	`checkNumber`		varchar(100),   -- check fields

	`purchaseOrderNumber`	varchar(100),   -- PO fields

	`cardType`		varchar(255),     -- auth.NET fields
	`cardSuffix`		varchar(4),
	`authorizationCode`	varchar(255),
	`transactionId`		varchar(255),
	`name`			varchar(255),
	`address`		varchar(255),
	`city`			varchar(255),
	`state`			varchar(255),
	`zip`			varchar(255),
	`country`		varchar(255),
	`amount`		decimal(10, 2),
	primary key(`id`)	
) ENGINE=InnoDB default CHARSET=utf8;

-- --------------------------------------------------

create table if not exists `Report` (
	`id`			integer		not null auto_increment,
	`eventId`		integer		not null,
	`name`			varchar(255)	not null,
	`showDateRegistered`	char(1)		not null default 'F',
	`showDateCancelled`	char(1)		not null default 'F',
	`showCategory`		char(1)		not null default 'F',
	`showRegType`		char(1)		not null default 'F',
	`showTotalCost`		char(1)		not null default 'F',
	`showTotalPaid`		char(1)		not null default 'F',
	`showRemainingBalance`	char(1)		not null default 'F',
	`isPaymentsToDate`	char(1)		not null default 'F',
	`isAllRegToDate`	char(1)		not null default 'F',
	`isOptionCount`		char(1)		not null default 'F',
	`isRegTypeBreakdown`	char(1)		not null default 'F',
	primary key(`id`)
) ENGINE=InnoDB default CHARSET=utf8;

-- --------------------------------------------------

create table if not exists `Report_ContactField` (
	`id`			integer		not null auto_increment,
	`reportId`		integer		not null,
	`contactFieldId`	integer		not null,
	`displayOrder`		integer		not null,
	primary key(`id`)
) ENGINE=InnoDB default CHARSET=utf8;

-- --------------------------------------------------

create table if not exists `User` (
	`id`		integer		not null auto_increment,
	`email`		varchar(255)	not null,
	`password`	varchar(40) 	not null,
	primary key(`id`)
) ENGINE=InnoDB default CHARSET=utf8;

-- --------------------------------------------------

create table if not exists `GroupRegistration` (
	`id`			integer 	not null auto_increment,
	`eventId`		integer		not null,
	`enabled`		char(1)		not null,
	`defaultRegType`	char(1)		not null,
	primary key(`id`)
) ENGINE=InnoDB default CHARSET=utf8;

-- --------------------------------------------------

create table if not exists `GroupRegistration_ContactField` (
	`id`			        integer 	not null auto_increment,
	`groupRegistrationId`	integer		not null,
	`contactFieldId`	    integer		not null,
	primary key(`id`)
) ENGINE=InnoDB default CHARSET=utf8;

-- --------------------------------------------------

create table if not exists `RegType_EmailTemplate` (
	`id`			    integer 	not null auto_increment,
	`regTypeId`		    integer,
	`emailTemplateId`	integer		not null,
	primary key(`id`)
) ENGINE=InnoDB default CHARSET=utf8;

-- --------------------------------------------------

create table if not exists `StaticPage` (
	`id`			integer		not null auto_increment,
	`eventId`		integer		not null,
	`name`			varchar(100)	not null,
	`title`			varchar(255),
	`content`		text,
	primary key(`id`)
) ENGINE=InnoDB default CHARSET=utf8;

-- --------------------------------------------------

create table if not exists `BadgeTemplate` (
    `id`            integer         not null auto_increment,
    `eventId`       integer         not null,
    `name`          varchar(255)    not null,
    `type`          varchar(255)    not null,
    primary key(`id`)
) ENGINE=InnoDB default CHARSET=utf8;

-- --------------------------------------------------

create table if not exists `BadgeTemplate_RegType` (
    `id`                integer         not null auto_increment,
    `badgeTemplateId`   integer         not null,
    `regTypeId`         integer,
    primary key(`id`)
) ENGINE=InnoDB default CHARSET=utf8;
    
-- --------------------------------------------------

create table if not exists `BadgeCell` (
    `id`                integer         not null auto_increment,
    `badgeTemplateId`   integer         not null,
    `xCoord`            decimal(10,3)   not null,
    `yCoord`            decimal(10,3)   not null,
    `width`             decimal(10,3)   not null,
    `font`              varchar(255)    not null,
    `fontSize`          decimal(10,1)   not null,
    `horizontalAlign`   char(1)         not null,
    `hasBarcode`        char(1)         not null,
    primary key(`id`)
) ENGINE=InnoDB default CHARSET=utf8;

-- --------------------------------------------------

create table if not exists `BadgeCell_TextContent` (
    `id`                integer         not null auto_increment,
    `badgeCellId`       integer         not null,      
    `displayOrder`      integer         not null,
    `text`              varchar(255),
    `contactFieldId`    integer,
    `showRegType`       char(1)         not null default 'F',
    `showLeadNumber`    char(1)         not null default 'F',
    primary key(`id`)
) ENGINE=InnoDB default CHARSET=utf8;

-- --------------------------------------------------

create table if not exists `BadgeBarcodeField` (
    `id`                integer         not null auto_increment,
    `badgeCellId`       integer         not null,    
    `contactFieldId`    integer         not null,  
    primary key(`id`)
) ENGINE=InnoDB default CHARSET=utf8;

-- --------------------------------------------------

create table if not exists `Role` (
    `id`            integer         not null auto_increment,
    `description`   varchar(255)    not null,
    primary key(`id`)
) ENGINE=InnoDB default CHARSET=utf8;

-- --------------------------------------------------

create table if not exists `User_Role` (
    `id`            integer         not null auto_increment,
    `userId`        integer         not null,
    `roleId`        integer         not null,
    `eventId`       integer,
    primary key(`id`)
) ENGINE=InnoDB default CHARSET=utf8;

alter table User_Role
    add constraint user_role_userId_fk
    foreign key (userId) references User(id);
    
alter table User_Role
    add constraint user_role_roleId_fk
    foreign key (roleId) references Role(id);
    
alter table User_Role
    add constraint user_role_eventId_fk
    foreign key (eventId) references Event(id);
    

-- -----------------------------------------
-- ADD TABLE CONSTRAINTS
-- -----------------------------------------

alter table Event
	add constraint event_code_uni
	unique (code);

-- --------------------------------------------------

alter table Page 
	add constraint page_eventId_fk
	foreign key (eventId) references Event(id);

alter table Page
	add constraint page_displayOrder_uni
	unique (displayOrder);

-- --------------------------------------------------
		
alter table Section
	add constraint section_eventId_fk
	foreign key (eventId) references Event(id);

alter table Section
	add constraint section_pageId_fk
	foreign key (pageId) references Page(id);

alter table Section
	add constraint section_contentTypeId_fk
	foreign key (contentTypeId) references ContentType(id);

alter table Section
	add constraint section_displayOrder_uni
	unique (displayOrder);

-- --------------------------------------------------
	
alter table RegOptionGroup
	add constraint regOptGroup_eventId_fk
	foreign key (eventId) references Event(id);

alter table RegOptionGroup
	add constraint regOptGroup_sectionId_fk
	foreign key (sectionId) references Section(id);

alter table RegOptionGroup
	add constraint regOptGroup_regOptionId_fk
	foreign key (regOptionId) references RegOption(id);

alter table RegOptionGroup
	add constraint regOptGroup_sectionId_dispOrder_uni
	unique (sectionId, displayOrder);

alter table RegOptionGroup
	add constraint regOptGroup_regOptId_dispOrder_uni
	unique (regOptionId, displayOrder);

-- --------------------------------------------------

alter table RegOption
	add constraint regOption_eventId_fk
	foreign key (eventId) references Event(id);

alter table RegOption
	add constraint regOption_parentGroupId_fk 
	foreign key (parentGroupId) references RegOptionGroup(id);
	
alter table RegOption
	add constraint regOption_group_code_uni 
	unique (parentGroupId, code);
	
alter table RegOption
	add constraint regOption_displayOrder_uni
	unique (parentGroupId, displayOrder);

-- --------------------------------------------------

alter table RegType
	add constraint regType_eventId_fk
	foreign key (eventId) references Event(id);
	
alter table RegType
	add constraint regType_sectionId_fk
	foreign key (sectionId) references Section(id);

alter table RegType
	add constraint regType_code_uni
	unique (eventId, code);
	
alter table RegType
	add constraint regType_displayOrder_uni unique(sectionId, displayOrder);

-- --------------------------------------------------

alter table RegOptionPrice
	add constraint regOptionPrice_eventId_fk
	foreign key (eventId) references Event(id);

-- --------------------------------------------------

alter table RegOption_RegOptionPrice
	add constraint regOpt_regOptPrice_regOpt_fk
	foreign key (regOptionId) references RegOption(id);

alter table RegOption_RegOptionPrice
	add constraint regOpt_regOptPrice_optPrice_fk
	foreign key (regOptionPriceId) references RegOptionPrice(id);

alter table RegOption_RegOptionPrice
	add constraint regOpt_regOptPrice_optPrice_uni
	unique (regOptionId, regOptionPriceId);

-- --------------------------------------------------

alter table Category
	add constraint category_displayName_uni
	unique (displayName(255));

-- --------------------------------------------------

alter table FormInput
	add constraint formInput_name_uni
	unique (name);

-- --------------------------------------------------

alter table ContactField
	add constraint contactField_eventId_fk
	foreign key (eventId) references Event(id);

alter table ContactField
	add constraint contactField_sectionId_fk
	foreign key (sectionId) references Section(id);
	
alter table ContactField
	add constraint contactField_formInputId_fk
	foreign key (formInputId) references FormInput(id);

alter table ContactField
	add constraint	contactField_code_uni
	unique (sectionId, code(255));
	
alter table ContactField
	add constraint contactField_displayOrder_uni
	unique (sectionId, displayOrder);

-- --------------------------------------------------

alter table RegTypeContactField
	add constraint regTypeContactField_regTypeId_fk
	foreign key (regTypeId) references RegType(id);
	
alter table RegTypeContactField
	add constraint regTypeContactField_contactFieldId_fk
	foreign key (contactFieldId) references ContactField(id);
	
alter table RegTypeContactField
	add constraint regTypeContactField_regTypeId_contactFieldId_uni
	unique (regTypeId, contactFieldId);

-- --------------------------------------------------

alter table CategoryRegType
	add constraint categoryRegType_categoryId_fk
	foreign key (categoryId) references Category(id);
	
alter table CategoryRegType
	add constraint categoryRegType_regTypeId_fk
	foreign key (regTypeId) references RegType(id);
	
alter table CategoryRegType
	add constraint categoryRegType_categoryId_regTypeId_uni
	unique (categoryId, regTypeId);

-- --------------------------------------------------

alter table Attribute
	add constraint attribute_name_uni
	unique (name);

-- --------------------------------------------------

alter table ContactFieldAttribute
	add constraint contactFieldAttribute_contactFieldId_fk
	foreign key (contactFieldId) references ContactField(id);

alter table ContactFieldAttribute
	add constraint contactFieldAttribute_attributeId_fk
	foreign key (attributeId) references Attribute(id);
		
alter table ContactFieldAttribute
	add constraint contactFieldAttribute_contactFieldId_attributeId_uni
	unique (contactFieldId, attributeId);

-- --------------------------------------------------
	
alter table Validation
	add constraint validation_name_uni
	unique (name);

-- --------------------------------------------------

alter table ContactFieldValidation
	add constraint contactFieldValidation_contactFieldId_fk
	foreign key (contactFieldId) references ContactField(id);
	
alter table ContactFieldValidation
	add constraint contactFieldValidation_validationId_fk
	foreign key (validationId) references Validation(id);
	
alter table ContactFieldValidation
	add constraint contactFieldValidation_contactId_validId_uni
	unique (contactFieldId, validationId);

-- --------------------------------------------------

alter table FormInputValidation
	add constraint formInputValidation_formInputId_fk
	foreign key (formInputId) references FormInput(id);
	
alter table FormInputValidation
	add constraint formInputValidation_validationId_fk
	foreign key (validationId) references Validation(id);
	
alter table FormInputValidation
	add constraint formInputValidation_inputId_validId_uni
	unique (formInputId, validationId);

-- --------------------------------------------------

alter table FormInputAttribute
	add constraint formInputAttribute_formInputId_fk
	foreign key (formInputId) references FormInput(id);
	
alter table FormInputAttribute
	add constraint formInputAttribute_attributeId_fk
	foreign key (attributeId) references Attribute(id);
	
alter table FormInputAttribute
	add constraint formInputValidation_inputId_attrId_uni
	unique (formInputId, attributeId);

-- --------------------------------------------------

alter table Category_Page
	add constraint categoryPage_categoryId_fk
	foreign key (categoryId) references Category(id);
	
alter table Category_Page
	add constraint categoryPage_pageId_fk
	foreign key (pageId) references Page(id);

alter table Category_Page
	add constraint categoryPage_categoryId_pageId_uni
	unique (categoryId, pageId);

-- --------------------------------------------------

alter table ContactFieldOption
	add constraint contactFieldOption_contactFieldId_fk
	foreign key (contactFieldId) references ContactField(id);

alter table ContactFieldOption
	add constraint contactFieldOption_field_displayOrder_uni
	unique (contactFieldId, displayOrder);

-- --------------------------------------------------

alter table RegType_RegOptionPrice
	add constraint regType_regOptPrice_regTypeId_fk
	foreign key (regTypeId) references RegType(id);

alter table RegType_RegOptionPrice
	add constraint regType_regOptPrice_regOptPriceId_fk
	foreign key (regOptionPriceId) references RegOptionPrice(id);

alter table RegType_RegOptionPrice
	add constraint regType_regOptPrice_typePrice_uni
	unique (regTypeId, regOptionPriceId);

-- --------------------------------------------------

alter table VariableQuantityOption
	add constraint varQuantityOpt_eventId_fk
	foreign key (eventId) references Event(id);

alter table VariableQuantityOption
	add constraint varQuantOpt_sectionId_fk
	foreign key (sectionId) references Section(id);

alter table VariableQuantityOption
	add constraint varQuantOpt_sectionId_code_uni
	unique (sectionId, code);

alter table VariableQuantityOption
	add constraint varQuantOpt_sectionId_dispOrder_uni
	unique (sectionId, displayOrder);

-- --------------------------------------------------

alter table VariableQuantityOption_RegOptionPrice
	add constraint varQuantOpt_regOptPrice_varQuant_fk
	foreign key (variableQuantityId) references VariableQuantityOption(id);

alter table VariableQuantityOption_RegOptionPrice
	add constraint varQuantOpt_regOptPrice_regOptPrice_fk
	foreign key (regOptionPriceId) references RegOptionPrice(id);

alter table VariableQuantityOption_RegOptionPrice
	add constraint varQuantOpt_regOptPrice_varQuantPrice_uni
	unique (variableQuantityId, regOptionPriceId);

-- --------------------------------------------------

alter table Appearance
	add constraint appearance_eventId_fk
	foreign key (eventId) references Event(id);

alter table Appearance
	add constraint appearance_eventId_uni
	unique (eventId);

-- --------------------------------------------------

alter table EmailTemplate
	add constraint emailTemplate_eventId_fk
	foreign key (eventId) references Event(id);

alter table EmailTemplate
	add constraint emailTemplate_contactFieldId_fk
	foreign key (contactFieldId) references ContactField(id);

-- --------------------------------------------------

alter table Registration
	add constraint registration_categoryId_fk
	foreign key (categoryId) references Category(id);

alter table Registration
	add constraint registration_eventId_fk
	foreign key (eventId) references Event(id);

alter table Registration
	add constraint registration_regTypeId_fk
	foreign key (regTypeId) references RegType(id);

alter table Registration
	add constraint registration_regGroupId_fk
	foreign key (regGroupId) references RegistrationGroup(id);

alter table Registration
	add constraint registraion_confNum_uni
	unique (confirmationNumber);
	
alter table Registration
    add constraint registration_eventid_leadNum_uni
    unique (eventId, leadNumber);

-- --------------------------------------------------

alter table Registration_Information
	add constraint reg_info_registrationId_fk
	foreign key (registrationId) references Registration(id);

alter table Registration_Information
	add constraint reg_info_contactFieldId_fk
	foreign key (contactFieldId) references ContactField(id);

-- --------------------------------------------------

alter table Registration_RegOption
	add constraint reg_regOpt_registrationId_fk
	foreign key (registrationId) references Registration(id);

alter table Registration_RegOption
	add constraint reg_regOpt_regOptionId_fk
	foreign key (regOptionId) references RegOption(id);

alter table Registration_RegOption
	add constraint reg_regOpt_priceId_fk
	foreign key (priceId) references RegOptionPrice(id);

-- --------------------------------------------------

alter table Registration_VariableQuantityOption
	add constraint reg_varQuantOpt_regId_fk
	foreign key (registrationId) references Registration(id);

alter table Registration_VariableQuantityOption
	add constraint reg_varQuantOpt_varQuantId_fk
	foreign key (variableQuantityId) references VariableQuantityOption(id);

alter table Registration_VariableQuantityOption
	add constraint reg_varQuantOpt_priceId_fk
	foreign key (priceId) references RegOptionPrice(id);

alter table Registration_VariableQuantityOption
	add constraint reg_varQuantOpt_regVarOpt_uni
	unique (registrationId, variableQuantityId);

-- --------------------------------------------------

alter table CheckDirections
	add constraint checkDirections_paymentTypeId_fk
	foreign key (paymentTypeId) references PaymentType(id);

alter table CheckDirections
	add constraint checkDirections_eventId_fk
	foreign key (eventId) references Event(id);

alter table CheckDirections
	add constraint checkDirections_eventId_uni
	unique (eventId);

-- --------------------------------------------------

alter table PurchaseOrderDirections
	add constraint purchaseOrderDirections_paymentTypeId_fk
	foreign key (paymentTypeId) references PaymentType(id);

alter table PurchaseOrderDirections
	add constraint purchaseOrderDirections_eventId_fk
	foreign key (eventId) references Event(id);

alter table PurchaseOrderDirections
	add constraint purchaseOrderDirections_eventId_uni
	unique (eventId);

-- --------------------------------------------------

alter table AuthorizeNetDirections
	add constraint authorizeNetDirections_paymentTypeId_fk
	foreign key (paymentTypeId) references PaymentType(id);

alter table AuthorizeNetDirections
	add constraint authorizeNetDirections_eventId_fk
	foreign key (eventId) references Event(id);

alter table AuthorizeNetDirections
	add constraint authorizeNetDirections_eventId_uni
	unique (eventId);

-- --------------------------------------------------

alter table Payment
	add constraint payment_eventId_fk
	foreign key (eventId) references Event(id);

alter table Payment
	add constraint payment_paymentTypeId_fk
	foreign key (paymentTypeId) references PaymentType(id);

alter table Payment
	add constraint payment_regGroupId_fk
	foreign key (regGroupId) references RegistrationGroup(id);

-- --------------------------------------------------

alter table Report
	add constraint report_eventId_fk
	foreign key (eventId) references Event(id);

alter table Report
	add constraint report_eventId_name_uni
	unique (eventId, name);

-- --------------------------------------------------

alter table Report_ContactField
	add constraint report_contactField_reportId_fk
	foreign key (reportId) references Report(id);

alter table Report_ContactField
	add constraint report_contactField_fieldId_fk
	foreign key (contactFieldId) references ContactField(id);

alter table Report_ContactField
	add constraint report_contactField_reportId_fieldId_uni
	unique (reportId, contactFieldId);

alter table Report_ContactField
	add constraint report_contactField_report_dispOrder_uni
	unique (reportId, displayOrder);

-- --------------------------------------------------

alter table `User`
	add constraint user_email_uni unique	(email);

-- --------------------------------------------------

alter table GroupRegistration
	add constraint groupRegistration_eventId_fk
	foreign key (eventId) references Event(id);

alter table GroupRegistration
	add constraint groupRegistration_eventId_uni
	unique (eventId);

-- --------------------------------------------------

alter table GroupRegistration_ContactField
	add constraint groupReg_contactField_groupRegId_fk
	foreign key (groupRegistrationId) references GroupRegistration(id);

alter table GroupRegistration_ContactField
	add constraint groupReg_contactField_fieldId_fk
	foreign key (contactFieldId) references ContactField(id);

alter table GroupRegistration_ContactField
	add constraint groupReg_contactField_groupReg_field_uni
	unique (groupRegistrationId, contactFieldId);

-- --------------------------------------------------

alter table RegType_EmailTemplate
	add constraint regType_emailTemplate_regTypeId_fk
	foreign key (regTypeId) references RegType(id);

alter table RegType_EmailTemplate
	add constraint regType_emailTemplate_emailTempId_fk
	foreign key (emailTemplateId) references EmailTemplate(id);

alter table RegType_EmailTemplate
	add constraint regType_emailTemplate_typeTemplate_uni
	unique (regTypeId, emailTemplateId);

-- --------------------------------------------------

alter table StaticPage
	add constraint staticPage_eventId_fk
	foreign key (eventId) references Event(id);
	
alter table StaticPage
    add constraint staticPage_eventIdName_uni
    unique(eventId, name);

-- --------------------------------------------------

alter table BadgeTemplate
    add constraint badgeTemplate_eventId_fk
    foreign key (eventId) references Event(id);
    
-- --------------------------------------------------

alter table BadgeTemplate_RegType
    add constraint badgeTempl_regType_badgeId_fk
    foreign key (badgeTemplateId) references BadgeTemplate(id);
    
alter table BadgeTemplate_RegType
    add constraint badgeTempl_regType_regTypeId_fk
    foreign key (regTypeId) references RegType(id);
    
-- --------------------------------------------------

alter table BadgeCell
    add constraint badgeCell_badgeTemplId_fk
    foreign key (badgeTemplateId) references BadgeTemplate(id);
    
-- --------------------------------------------------

alter table BadgeCell_TextContent
    add constraint badgeCell_textCont_badgeCellId_fk
    foreign key (badgeCellId) references BadgeCell(id);
    
alter table BadgeCell_TextContent
    add constraint badgeCell_textCont_contFieldId_fk
    foreign key (contactFieldId) references ContactField(id);
    
alter table BadgeCell_TextContent
    add constraint badgeCell_textCont_cellId_dispOrder_uni
    unique (badgeCellId, displayOrder);
    
-- --------------------------------------------------

alter table BadgeBarcodeField
    add constraint badgeBarcodeField_badgeCellId_fk
    foreign key (badgeCellId) references BadgeCell(id);
    
alter table BadgeBarcodeField
    add constraint badgeBarcodeField_contFieldId_fk
    foreign key (contactFieldId) references ContactField(id);
    
-- --------------------------------------------------



    



