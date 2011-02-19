
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



