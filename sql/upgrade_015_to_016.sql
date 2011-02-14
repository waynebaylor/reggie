
-- changing true/false from text 'true' and 'false' to single char 'T' and 'F'

alter table
	RegOptionGroup
modify
	required char(1) not null;

alter table
	RegOptionGroup
modify
	multiple char(1) not null;

alter table
	RegOption
modify
	defaultSelected char(1) not null;

alter table
	RegOption
modify
	showPrice char(1) not null;

alter table
	ContactFieldOption
modify
	defaultSelected char(1) not null;

alter table
	EmailTemplate
modify
	enabled char(1) not null;

alter table
	Payment
modify
	paymentReceived char(1) not null default 'F';

alter table
	Report
modify
	showDateRegistered char(1) not null default 'F';

alter table
	Report
modify
	showCategory char(1) not null default 'F';

alter table
	Report
modify
	showRegType char(1) not null default 'F';

alter table
	Report
modify
	showTotalCost char(1) not null default 'F';

alter table
	Report
modify
	showTotalPaid char(1) not null default 'F';

alter table
	Report
modify
	showRemainingBalance char(1) not null default 'F';

alter table
	Report
modify
	showDateCancelled char(1) not null default 'F';

alter table
	User
modify
	isAdmin char(1) not null;

alter table
	GroupRegistration
modify
	enabled char(1) not null;

alter table
	GroupRegistration
modify
	defaultRegType char(1) not null;

-- update column values

update 
	RegOptionGroup
set
	required = upper(required),
	multiple = upper(multiple);

update
	RegOption
set
	defaultSelected = upper(defaultSelected),
	showPrice = upper(showPrice);

update
	ContactFieldOption
set
	defaultSelected = upper(defaultSelected);

update
	EmailTemplate
set
	enabled = upper(enabled);

update
	Payment
set
	paymentReceived = upper(paymentReceived);

update
	Report
set
	showDateRegistered = upper(showDateRegistered),
	showCategory = upper(showCategory),
	showRegType = upper(showRegType),
	showTotalCost = upper(showTotalCost),
	showTotalPaid = upper(showTotalPaid),
	showRemainingBalance = upper(showRemainingBalance),
	showDateCancelled = upper(showDateCancelled);

update 
	User
set
	isAdmin = upper(isAdmin);

update
	GroupRegistration
set
	enabled = upper(enabled),
	defaultRegType = upper(defaultRegType);

update 
	ContactFieldValidation
set
	validationValue = 'T'
where
	validationValue = 'true';

update 
	ContactFieldValidation
set
	validationValue = 'F'
where
	validationValue = 'false';

-- add columnfor new report type

alter table
	Report
add column
	isPaymentsToDate char(1) not null default 'F';



