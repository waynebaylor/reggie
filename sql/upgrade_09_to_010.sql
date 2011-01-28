
-- ===================
-- confirmation number
-- ===================
alter table
	Registration
add column
	confirmationNumber varchar(255);

update 
	Registration
set
	confirmationNumber = id;

alter table
	Registration
modify
	confirmationNumber varchar(255) not null;

alter table 
	Registration
add constraint
	registraion_confNum_uni
unique
	(confirmationNumber);
