
drop table if exists User_Event;
drop table if exists User;
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
drop table if exists CategoryPage;
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
drop table if exists RegOption_RegOptionGroup;
drop table if exists RegOption;
drop table if exists Section_RegOptionGroup;
drop table if exists RegOptionGroup;
drop table if exists Section;
drop table if exists Page;
drop table if exists Event;
drop table if exists ContentType;

-- --------------------------------------------------
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
	`cancellationPolicy` 	text 		not null,
	`regClosedText` 	text 		not null,
	PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ;

alter table
	Event
add constraint
	event_code_uni
unique
	(code);
	
-- --------------------------------------------------------------------------
	
create table if not exists `Page` (
	`id` 		integer 	not null auto_increment,
	`eventId` 	integer 	not null,
	`title` 	varchar(255) 	not null,
	`displayOrder` 	integer 	not null,
	primary key (`id`)
) ENGINE=InnoDB default CHARSET=utf8;

alter table 
	Page
add constraint
	page_eventId_fk
foreign key
	(eventId)
references
	Event(id)
on delete cascade;

alter table
	Page
add constraint
	page_displayOrder_uni
unique
	(displayOrder);
	
-- --------------------------------------------------------------------------
	
create table if not exists `Section` (
	`id` 		integer 	not null auto_increment,
	`pageId` 	integer 	not null,
	`name`	 	varchar(255)	not null,
	`text`		text,
	`numbered`	varchar(255)	not null default 'false',
	`contentTypeId` integer 	not null,
	`displayOrder` 	integer 	not null,
	primary key (`id`)
) ENGINE=InnoDB default CHARSET=utf8;

alter table
	Section
add constraint
	section_pageId_fk
foreign key
	(pageId)
references
	Page(id)
on delete cascade;

alter table
	Section
add constraint
	section_contentTypeId_fk
foreign key
	(contentTypeId)
references
	ContentType(id)
on delete cascade;

alter table 
	Section
add constraint
	section_displayOrder_uni
unique
	(displayOrder);
	
-- --------------------------------------------------------------------------

create table if not exists `RegOptionGroup` (
	`id` 		integer 	not null auto_increment,
	`description` 	varchar(255) 	not null,
	`required` 	varchar(255) 	not null,
	`multiple` 	varchar(255) 	not null,
	`minimum`	integer		not null,
	`maximum`	integer		not null,
	primary key (`id`)
) ENGINE=InnoDB default CHARSET=utf8;
	
-- --------------------------------------------------------------------------

create table if not exists `Section_RegOptionGroup` (
	`id`		integer		not null auto_increment,
	`sectionId`	integer		not null,
	`optionGroupId`	integer		not null,
	`displayOrder`	integer		not null,
	primary key (`id`)
) ENGINE=InnoDB default CHARSET=utf8;
	
alter table
	Section_RegOptionGroup
add constraint
	sectionOptGroup_dispOrder_uni
unique
	(sectionId, displayOrder);

alter table 
	Section_RegOptionGroup
add constraint
	sectionOptGroup_sectionId_fk
foreign key
	(sectionId)
references
	Section(id)
on delete cascade;

alter table 
	Section_RegOptionGroup
add constraint
	sectionOptGroup_groupId_fk
foreign key
	(optionGroupId)
references
	RegOptionGroup(id)
on delete cascade;

-- --------------------------------------------------------------------------

create table if not exists `RegOption` (
	`id` 			integer 	not null auto_increment,
	`parentGroupId` 	integer 	not null,
	`code` 			varchar(255) 	not null,
	`description`		varchar(255) 	not null,
	`capacity` 		integer 	not null,
	`defaultSelected`	varchar(255)	not null, 
	`showPrice`		varchar(255)	not null,
	`displayOrder` 		integer 	not null,
	primary key (`id`)
) ENGINE=InnoDB default CHARSET=utf8;

alter table
	RegOption
add constraint
	regOption_parentGroupId_fk
foreign key
	(parentGroupId)
references
	RegOptionGroup(id)
on delete cascade;
	
alter table 
	RegOption
add constraint
	regOption_group_code_uni
unique
	(parentGroupId, code);
	
alter table 
	RegOption
add constraint
	regOption_displayOrder_uni
unique
	(parentGroupId, displayOrder);
	
-- --------------------------------------------------------------------------

create table if not exists `RegOption_RegOptionGroup` (
	`id`		integer		not null auto_increment,
	`regOptionId`	integer		not null,
	`optionGroupId`	integer		not null,
	`displayOrder`	integer		not null,
	primary key (`id`)
) ENGINE=InnoDB default CHARSET=utf8;

alter table
	RegOption_RegOptionGroup
add constraint
	regOpt_regOptGroup_optId_fk
foreign key
	(regOptionId)
references
	RegOption(id)
on delete cascade;

alter table 
	RegOption_RegOptionGroup
add constraint
	regOpt_regOptGroup_groupId_fk
foreign key
	(optionGroupId)
references
	RegOptionGroup(id)
on delete cascade;

alter table 
	RegOption_RegOptionGroup
add constraint
	regOpt_regOptGroup_dispOrder_uni
unique
	(regOptionId, displayOrder);
	
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

alter table
	RegType
add constraint
	regType_eventId_fk
foreign key
	(eventId)
references
	Event(id)
on delete cascade;
	
alter table
	RegType
add constraint
	regType_sectionId_fk
foreign key
	(sectionId)
references	
	Section(id);

alter table
	RegType
add constraint
	regType_code_uni
unique
	(eventId, code);
	
alter table
	RegType
add constraint
	regType_displayOrder_uni
unique(sectionId, displayOrder);

-- --------------------------------------------------------------------------

create table if not exists `RegOptionPrice` (
	`id` 		integer 	not null auto_increment,
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

alter table 
	RegOption_RegOptionPrice
add constraint
	regOpt_regOptPrice_regOpt_fk
foreign key
	(regOptionId)
references
	RegOption(id)
on delete cascade;

alter table
	RegOption_RegOptionPrice
add constraint
	regOpt_regOptPrice_optPrice_fk
foreign key
	(regOptionPriceId)
references
	RegOptionPrice(id)
on delete cascade;

alter table 
	RegOption_RegOptionPrice
add constraint
	regOpt_regOptPrice_optPrice_uni
unique
	(regOptionId, regOptionPriceId);

-- --------------------------------------------------------------------------

create table if not exists `Category` (
	`id` 		integer 	not null auto_increment,
	`displayName` 	varchar(255) 	not null,
	PRIMARY KEY (`id`)
) ENGINE=InnoDB default CHARSET=utf8;

alter table 
	Category
add constraint
	category_displayName_uni
unique
	(displayName(255));

-- --------------------------------------------------------------------------
	
create table if not exists `FormInput` (
	`id` 		integer 	not null,
	`name` 		varchar(255) 	not null,
	`displayName` 	varchar(255) 	not null,
	primary key (`id`)
) ENGINE=InnoDB default CHARSET=utf8;

alter table 
	FormInput
add constraint
	formInput_name_uni
unique
	(name);

-- --------------------------------------------------------------------------

create table if not exists `ContactField` (
	`id` 		integer 	not null auto_increment,
	`code` 		varchar(255) 	not null,
	`sectionId` 	integer 	not null,
	`formInputId` 	integer 	not null,
	`displayName` 	varchar(255) 	not null,
	`displayOrder` 	integer 	not null,
	PRIMARY KEY (`id`)
) ENGINE=InnoDB default CHARSET=utf8;

alter table
	ContactField
add constraint
	contactField_sectionId_fk
foreign key
	(sectionId)
references
	Section(id)
on delete cascade;
	
alter table
	ContactField
add constraint
	contactField_formInputId_fk
foreign key
	(formInputId)
references
	FormInput(id)
on delete cascade;

alter table 
	ContactField
add constraint
	contactField_code_uni
unique
	(sectionId, code(255));
	
alter table
	ContactField
add constraint
	contactField_displayOrder_uni
unique
	(sectionId, displayOrder);

	
-- --------------------------------------------------------------------------
-- if regTeypId is NULL, then the contact field belongs to all event reg types.
-- 	
create table if not exists `RegTypeContactField` (
	`id` 			integer 	not null auto_increment,
	`regTypeId` 		integer,
	`contactFieldId` 	integer 	not null,
	primary key (`id`)
) ENGINE=InnoDB default CHARSET=utf8;

alter table 
	RegTypeContactField
add constraint
	regTypeContactField_regTypeId_fk
foreign key
	(regTypeId)
references
	RegType(id)
on delete cascade;
	
alter table 
	RegTypeContactField
add constraint
	regTypeContactField_contactFieldId_fk
foreign key
	(contactFieldId)
references
	ContactField(id)
on delete cascade;
	
alter table 
	RegTypeContactField
add constraint
	regTypeContactField_regTypeId_contactFieldId_uni
unique
	(regTypeId, contactFieldId);

-- --------------------------------------------------------------------------
		
create table if not exists `CategoryRegType` (
	`id` 		integer 	not null auto_increment,
	`categoryId` 	integer 	not null,
	`regTypeId` 	integer 	not null,
	primary key (`id`)
) ENGINE=InnoDB default CHARSET=utf8;

alter table 
	CategoryRegType
add constraint
	categoryRegType_categoryId_fk
foreign key
	(categoryId)
references
	Category(id)
on delete cascade;
	
alter table
	CategoryRegType
add constraint
	categoryRegType_regTypeId_fk
foreign key
	(regTypeId)
references
	RegType(id)
on delete cascade;
	
alter table 
	CategoryRegType
add constraint
	categoryRegType_categoryId_regTypeId_uni
unique
	(categoryId, regTypeId);

-- --------------------------------------------------------------------------
	
create table if not exists `Attribute` (
	`id` 		integer 	not null,
	`name` 		varchar(255) 	not null,
	`displayName` 	varchar(255) 	not null,
	primary key (`id`)
) ENGINE=InnoDB default CHARSET=utf8;

alter table 
	Attribute
add constraint
	attribute_name_uni
unique
	(name);
	
-- --------------------------------------------------------------------------
	
create table if not exists `ContactFieldAttribute` (
	`id` 			integer 	not null auto_increment,
	`contactFieldId` 	integer 	not null,
	`attributeId` 		integer 	not null,
	`attrValue` 		varchar(255) 	not null,
	primary key (`id`)
) ENGINE=InnoDB default CHARSET=utf8;

alter table
	ContactFieldAttribute
add constraint
	contactFieldAttribute_contactFieldId_fk
foreign key
	(contactFieldId)
references
	ContactField(id)
on delete cascade;

alter table
	ContactFieldAttribute
add constraint
	contactFieldAttribute_attributeId_fk
foreign key
	(attributeId)
references
	Attribute(id)
on delete cascade;
		
alter table
	ContactFieldAttribute
add constraint
	contactFieldAttribute_contactFieldId_attributeId_uni
unique
	(contactFieldId, attributeId);
	
-- --------------------------------------------------------------------------
	
create table if not exists `Validation` (
	`id` 		integer 	not null,
	`name` 		varchar(255) 	not null,
	`displayName` 	varchar(255) 	not null,
	primary key (`id`)
) ENGINE=InnoDB default CHARSET=utf8;

alter table
	Validation
add constraint
	validation_name_uni
unique
	(name);

-- --------------------------------------------------------------------------
	
create table if not exists `ContactFieldValidation` (
	`id` 			integer 	not null auto_increment,
	`contactFieldId` 	integer 	not null,
	`validationId` 		integer 	not null,
	`validationValue` 	varchar(255) 	not null,
	primary key (`id`)
) ENGINE=InnoDB default CHARSET=utf8;

alter table
	ContactFieldValidation
add constraint
	contactFieldValidation_contactFieldId_fk
foreign key
	(contactFieldId)
references
	ContactField(id)
on delete cascade;
	
alter table
	ContactFieldValidation
add constraint
	contactFieldValidation_validationId_fk
foreign key
	(validationId)
references
	Validation(id)
on delete cascade;
	
alter table
	ContactFieldValidation
add constraint
	contactFieldValidation_contactId_validId_uni
unique
	(contactFieldId, validationId);
		
-- --------------------------------------------------------------------------
	
create table if not exists `FormInputValidation` (
	`id` 		integer 	not null auto_increment,
	`formInputId` 	integer 	not null,
	`validationId` 	integer 	not null,
	primary key (`id`)
) ENGINE=InnoDB default CHARSET=utf8;

alter table
	FormInputValidation
add constraint
	formInputValidation_formInputId_fk
foreign key
	(formInputId)
references
	FormInput(id)
on delete cascade;
	
alter table
	FormInputValidation
add constraint
	formInputValidation_validationId_fk
foreign key
	(validationId)
references
	Validation(id)
on delete cascade;
	
alter table
	FormInputValidation
add constraint
	formInputValidation_inputId_validId_uni
unique
	(formInputId, validationId);
	
-- --------------------------------------------------------------------------
	
create table if not exists `FormInputAttribute` (
	`id` 		integer 	not null auto_increment,
	`formInputId` 	integer 	not null,
	`attributeId` 	integer 	not null,
	primary key (`id`)
) ENGINE=InnoDB default CHARSET=utf8;

alter table
	FormInputAttribute
add constraint
	formInputAttribute_formInputId_fk
foreign key
	(formInputId)
references
	FormInput(id)
on delete cascade;
	
alter table
	FormInputAttribute
add constraint
	formInputAttribute_attributeId_fk
foreign key
	(attributeId)
references
	Attribute(id)
on delete cascade;
	
alter table
	FormInputAttribute
add constraint
	formInputValidation_inputId_attrId_uni
unique
	(formInputId, attributeId);
	
-- --------------------------------------------------

create table if not exists `CategoryPage` (
	`id` 		integer 	not null auto_increment,
	`categoryId` 	integer 	not null,
	`pageId` 	integer 	not null,
	primary key (`id`)
) ENGINE=InnoDB default CHARSET=utf8;

alter table
	CategoryPage
add constraint
	categoryPage_categoryId_fk
foreign key
	(categoryId)
references
	Category(id)
on delete cascade;
	
alter table
	CategoryPage
add constraint
	categoryPage_pageId_fk
foreign key
	(pageId)
references
	Page(id)
on delete cascade;

alter table
	CategoryPage
add constraint
	categoryPage_categoryId_pageId_uni
unique
	(categoryId, pageId);
	
-- --------------------------------------------------

create table if not exists `ContactFieldOption` (
	`id` 			integer 	not null auto_increment,
	`contactFieldId` 	integer 	not null,
	`displayName` 		varchar(255) 	not null,
	`defaultSelected`	varchar(255)	not null,
	`displayOrder`		integer 	not null,
	primary key (`id`)
) ENGINE=InnoDB default CHARSET=utf8;
	
alter table
	ContactFieldOption
add constraint
	contactFieldOption_contactFieldId_fk
foreign key
	(contactFieldId)
references
	ContactField(id)
on delete cascade;

alter table
	ContactFieldOption
add constraint
	contactFieldOption_field_displayOrder_uni
unique
	(contactFieldId, displayOrder);

-- --------------------------------------------------

create table if not exists `RegType_RegOptionPrice` (
	`id`			integer		not null auto_increment,
	`regTypeId`		integer,
	`regOptionPriceId`	integer		not null,
	primary key(`id`)
) ENGINE=InnoDB default CHARSET=utf8;

alter table
	RegType_RegOptionPrice
add constraint
	regType_regOptPrice_regTypeId_fk
foreign key
	(regTypeId)
references
	RegType(id)
on delete cascade;

alter table
	RegType_RegOptionPrice
add constraint
	regType_regOptPrice_regOptPriceId_fk
foreign key
	(regOptionPriceId)
references
	RegOptionPrice(id)
on delete cascade;

alter table
	RegType_RegOptionPrice
add constraint
	regType_regOptPrice_typePrice_uni
unique
	(regTypeId, regOptionPriceId);

-- --------------------------------------------------

create table if not exists `VariableQuantityOption` (
	`id`			integer		not null auto_increment,
	`sectionId`		integer		not null,
	`code` 			varchar(255) 	not null,
	`description`		varchar(255) 	not null,
	`capacity` 		integer		not null,
	`displayOrder` 		integer 	not null,
	primary key(`id`)
) ENGINE=InnoDB default CHARSET=utf8;

alter table 
	VariableQuantityOption
add constraint
	varQuantOpt_sectionId_fk
foreign key
	(sectionId)
references
	Section(id)
on delete cascade;

alter table
	VariableQuantityOption
add constraint
	varQuantOpt_sectionId_code_uni
unique
	(sectionId, code);

alter table
	VariableQuantityOption
add constraint
	varQuantOpt_sectionId_dispOrder_uni
unique
	(sectionId, displayOrder);

-- --------------------------------------------------

create table if not exists `VariableQuantityOption_RegOptionPrice` (
	`id`			integer		not null auto_increment,
	`variableQuantityId`	integer		not null,
	`regOptionPriceId`	integer		not null,
	primary key(`id`)
) ENGINE=InnoDB default CHARSET=utf8;

alter table 
	VariableQuantityOption_RegOptionPrice
add constraint
	varQuantOpt_regOptPrice_varQuant_fk
foreign key
	(variableQuantityId)
references
	VariableQuantityOption(id)
on delete cascade;

alter table
	VariableQuantityOption_RegOptionPrice
add constraint
	varQuantOpt_regOptPrice_regOptPrice_fk
foreign key
	(regOptionPriceId)
references
	RegOptionPrice(id)
on delete cascade;

alter table 
	VariableQuantityOption_RegOptionPrice
add constraint
	varQuantOpt_regOptPrice_varQuantPrice_uni
unique
	(variableQuantityId, regOptionPriceId);

-- --------------------------------------------------

create table if not exists `Appearance` (
	`id`			integer 	not null auto_increment,
	`eventId`		integer		not null,
	`headerContent` 	text 		not null,
	`footerContent` 	text 		not null,
	`headerColor`		varchar(6)	not null,
	`footerColor`		varchar(6)	not null,
	`menuColor`		varchar(6)	not null,
	`backgroundColor`	varchar(6)	not null,
	`formColor`		varchar(6)	not null,
	`buttonColor`		varchar(6),
	primary key(`id`)
) ENGINE=InnoDB default CHARSET=utf8;

alter table 
	Appearance
add constraint
	appearance_eventId_fk
foreign key
	(eventId)
references
	Event(id)
on delete cascade;

alter table
	Appearance
add constraint
	appearance_eventId_uni
unique
	(eventId);

-- --------------------------------------------------

create table if not exists `EmailTemplate` (
	`id`			integer 	not null auto_increment,
	`eventId`		integer		not null,
	`enabled`		varchar(255)	not null,
	`fromAddress`		varchar(255),
	`bcc`			varchar(255),
	`subject`		text,
	`header`		text,
	`footer`		text,
	primary key(`id`)
) ENGINE=InnoDB default CHARSET=utf8;

alter table 
	EmailTemplate
add constraint
	emailTemplate_eventId_fk
foreign key
	(eventId)
references
	Event(id)
on delete cascade;

alter table
	EmailTemplate
add constraint
	emailTemplate_eventId_uni
unique
	(eventId);

-- --------------------------------------------------

create table if not exists `RegistrationGroup` (
	`id`			integer		not null auto_increment,
	primary key(`id`)
) ENGINE=InnoDB default CHARSET=utf8;

-- --------------------------------------------------

create table if not exists `Registration` (
	`id`			integer		not null auto_increment,
	`dateRegistered`	datetime	not null,
	`comments`		text		not null,
	`dateCancelled`		datetime,
	`regGroupId`		integer		not null, -- group identifier, so payments, refunds, voids, etc can be associated with a group.
	`categoryId`		integer		not null,
	`eventId`		integer		not null,
	`regTypeId`		integer		not null,
	primary key(`id`)
) ENGINE=InnoDB default CHARSET=utf8;

alter table 
	Registration
add constraint
	registration_categoryId_fk
foreign key
	(categoryId)
references
	Category(id);

alter table 
	Registration
add constraint
	registration_eventId_fk
foreign key
	(eventId)
references
	Event(id);

alter table
	Registration
add constraint
	registration_regTypeId_fk
foreign key
	(regTypeId)
references
	RegType(id);

alter table
	Registration
add constraint
	registration_regGroupId_fk
foreign key
	(regGroupId)
references
	RegistrationGroup(id);

-- --------------------------------------------------

create table if not exists `Registration_Information` (
	`id`			integer		not null auto_increment,
	`registrationId`	integer		not null,
	`contactFieldId`	integer		not null,
	`value`			text		not null,
	primary key(`id`)
) ENGINE=InnoDB default CHARSET=utf8;

alter table
	Registration_Information
add constraint
	reg_info_registrationId_fk
foreign key
	(registrationId)
references
	Registration(id);

alter table 
	Registration_Information
add constraint
	reg_info_contactFieldId_fk
foreign key
	(contactFieldId)
references
	ContactField(id);

-- --------------------------------------------------

create table if not exists `Registration_RegOption` (
	`id`			integer		not null auto_increment,
	`registrationId`	integer		not null,
	`regOptionId`		integer		not null,
	`priceId`		integer,
	`comments`		text		not null,
	`dateCancelled`		datetime,
	primary key(`id`)
) ENGINE=InnoDB default CHARSET=utf8;

alter table 
	Registration_RegOption
add constraint
	reg_regOpt_registrationId_fk
foreign key
	(registrationId)
references
	Registration(id);

alter table 
	Registration_RegOption
add constraint
	reg_regOpt_regOptionId_fk
foreign key
	(regOptionId)
references
	RegOption(id);

alter table
	Registration_RegOption
add constraint
	reg_regOpt_priceId_fk
foreign key
	(priceId)
references
	RegOptionPrice(id);

alter table
	Registration_RegOption
add constraint
	reg_regOpt_regRegOpt_uni
unique
	(registrationId, regOptionId);

-- --------------------------------------------------

create table if not exists `Registration_VariableQuantityOption` (
	`id`			integer		not null auto_increment,
	`registrationId`	integer		not null,
	`variableQuantityId`	integer		not null,
	`priceId`		integer		not null,
	`quantity`		integer		not null,
	`comments`		integer		not null,
	`dateCancelled`		datetime,
	primary key(`id`)
) ENGINE=InnoDB default CHARSET=utf8;

alter table 
	Registration_VariableQuantityOption
add constraint
	reg_varQuantOpt_regId_fk
foreign key
	(registrationId)
references
	Registration(id);

alter table
	Registration_VariableQuantityOption
add constraint
	reg_varQuantOpt_varQuantId_fk
foreign key
	(variableQuantityId)
references
	VariableQuantityOption(id);

alter table
	Registration_VariableQuantityOption
add constraint
	reg_varQuantOpt_priceId_fk
foreign key
	(priceId)
references
	RegOptionPrice(id);

alter table
	Registration_VariableQuantityOption
add constraint
	reg_varQuantOpt_regVarOpt_uni
unique
	(registrationId, variableQuantityId);	

-- --------------------------------------------------

create table if not exists `PaymentType` (
	`id` 			integer		not null auto_increment,
	`displayName`		varchar(255)	not null,
	primary key(`id`)
) ENGINE=InnoDB default CHARSET=utf8;

-- --------------------------------------------------

create table if not exists `CheckDirections` (
	`id`			integer		not null auto_increment,
	`paymentTypeId`		integer		not null default 1, -- check
	`eventId`		integer		not null,
	`instructions`		text		not null,
	primary key(`id`)
) ENGINE=InnoDB default CHARSET=utf8;

alter table 
	CheckDirections
add constraint
	checkDirections_paymentTypeId_fk
foreign key
	(paymentTypeId)
references
	PaymentType(id)
on delete cascade;

alter table 
	CheckDirections
add constraint
	checkDirections_eventId_fk
foreign key
	(eventId)
references
	Event(id)
on delete cascade;

alter table
	CheckDirections
add constraint
	checkDirections_eventId_uni
unique
	(eventId);

-- --------------------------------------------------

create table if not exists `PurchaseOrderDirections` (
	`id`			integer		not null auto_increment,
	`paymentTypeId`		integer		not null default 2, -- purchase order
	`eventId`		integer		not null,
	`instructions`		text		not null,
	primary key(`id`)
) ENGINE=InnoDB default CHARSET=utf8;

alter table 
	PurchaseOrderDirections
add constraint
	purchaseOrderDirections_paymentTypeId_fk
foreign key
	(paymentTypeId)
references
	PaymentType(id)
on delete cascade;

alter table 
	PurchaseOrderDirections
add constraint
	purchaseOrderDirections_eventId_fk
foreign key
	(eventId)
references
	Event(id)
on delete cascade;

alter table
	PurchaseOrderDirections
add constraint
	purchaseOrderDirections_eventId_uni
unique
	(eventId);

-- --------------------------------------------------

create table if not exists `AuthorizeNetDirections` (
	`id`			integer		not null auto_increment,
	`paymentTypeId`		integer		not null default 3, -- Authorize.NET
	`eventId`		integer		not null,
	`instructions`		text		not null,
	`login`			varchar(255)	not null,
	`transactionKey`	varchar(255)	not null,
	primary key(`id`)
) ENGINE=InnoDB default CHARSET=utf8;

alter table 
	AuthorizeNetDirections
add constraint
	authorizeNetDirections_paymentTypeId_fk
foreign key
	(paymentTypeId)
references
	PaymentType(id)
on delete cascade;

alter table 
	AuthorizeNetDirections
add constraint
	authorizeNetDirections_eventId_fk
foreign key
	(eventId)
references
	Event(id);

alter table 
	AuthorizeNetDirections
add constraint
	authorizeNetDirections_eventId_uni
unique
	(eventId);

-- --------------------------------------------------

create table if not exists `Payment` (
	`id`			integer		not null auto_increment,
	`paymentTypeId`		integer		not null,
	`regGroupId`		integer		not null,
	`transactionDate`	datetime	not null,

	`checkNumber`		varchar(100),   -- check fields

	`purchaseOrderNumber`	varchar(100),   -- PO fields

	`cardSuffix`		varchar(16),    -- auth.NET fields
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

alter table
	Payment
add constraint
	payment_paymentTypeId_fk
foreign key
	(paymentTypeId)
references
	PaymentType(id);

alter table
	Payment
add constraint
	payment_regGroupId_fk
foreign key
	(regGroupId)
references
	RegistrationGroup(id);

-- --------------------------------------------------

create table if not exists `Report` (
	`id`			integer		not null auto_increment,
	`eventId`		integer		not null,
	`name`			varchar(255)	not null,
	`showDateRegistered`	varchar(255)	not null default 'false',
	`showCategory`		varchar(255)	not null default 'false',
	`showRegType`		varchar(255)	not null default 'false',
	`showPaymentType`	varchar(255)	not null default 'false',
	`showTotalCost`		varchar(255)	not null default 'false',
	`showTotalPaid`		varchar(255)	not null default 'false',
	`showRemainingBalance`	varchar(255)	not null default 'false',
	primary key(`id`)
) ENGINE=InnoDB default CHARSET=utf8;

alter table 
	Report
add constraint
	report_eventId_fk
foreign key
	(eventId)
references
	Event(id)
on delete cascade;

alter table
	Report
add constraint
	report_eventId_name_uni
unique
	(eventId, name);

-- --------------------------------------------------

create table if not exists `Report_ContactField` (
	`id`			integer		not null auto_increment,
	`reportId`		integer		not null,
	`contactFieldId`	integer		not null,
	`displayOrder`		integer		not null,
	primary key(`id`)
) ENGINE=InnoDB default CHARSET=utf8;

alter table 
	Report_ContactField
add constraint
	report_contactField_reportId_fk
foreign key
	(reportId)
references 
	Report(id)
on delete cascade;

alter table
	Report_ContactField
add constraint
	report_contactField_fieldId_fk
foreign key
	(contactFieldId)
references
	ContactField(id)
on delete cascade;

alter table
	Report_ContactField
add constraint
	report_contactField_reportId_fieldId_uni
unique
	(reportId, contactFieldId);

alter table
	Report_ContactField
add constraint
	report_contactField_report_dispOrder_uni
unique
	(reportId, displayOrder);

-- --------------------------------------------------

create table if not exists `User` (
	`id`		integer		not null auto_increment,
	`email`		varchar(255)	not null,
	`password`	varchar(40) 	not null,
	`isAdmin`	varchar(255)	not null,
	primary key(`id`)
) ENGINE=InnoDB default CHARSET=utf8;

alter table 
	User
add constraint
	user_email_uni
unique
	(email);

-- --------------------------------------------------

create table if not exists `User_Event` (
	`id`		integer 	not null auto_increment,
	`userId`	integer		not null,
	`eventId`	integer		not null,
	primary key(`id`)
) ENGINE=InnoDB default CHARSET=utf8;

alter table
	User_Event
add constraint
	userEvent_userId_fk
foreign key
	(userId)
references
	User(id);

alter table
	User_Event
add constraint
	userEvent_eventId_fk
foreign key
	(eventId)
references
	Event(id);

alter table 
	User_Event
add constraint
	userEvent_userId_eventId_uni
unique
	(userId, eventId);









