
-- add eventId column to payments table.

alter table
	Payment
add column
	eventId integer;

-- set eventId column values.

update
	Payment
set
	Payment.eventId = (
		select 
			Registration.eventId 
		from 
			Registration
		where
			Registration.regGroupId = Payment.regGroupId
		limit 1
	)

-- add eventId constraints.

alter table
	Payment
modify column
	eventId integer not null;

alter table
	Payment
add constraint
	payment_eventId_fk
foreign key
	(eventId)
references
	Event(id);

-- add eventId column to VariableQuantityOption table.

alter table
	VariableQuantityOption
add column
	eventId integer;

-- set eventId column values.

update
	VariableQuantityOption
set
	eventId = (
		select 
			Section.eventId 
		from 
			Section 
		where
			Section.id = VariableQuantityOption.sectionId
		limit 1
	);

-- add eventId constraints

alter table
	VariableQuantityOption
modify column
	eventId integer not null;

alter table
	VariableQuantityOption
add constraint
	varQuantityOpt_eventId_fk
foreign key
	(eventId)
references
	Event(id);





