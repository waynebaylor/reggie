
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
	);

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

-- add the display order column. this will be unique with sectionId/regOptionId.

alter table
	RegOptionGroup
add column
	displayOrder integer;

-- add sectionId column and set values.

alter table
	RegOptionGroup
add column
	sectionId integer;

update 
	RegOptionGroup 
join 
	Section_RegOptionGroup 
on 
	RegOptionGroup.id = Section_RegOptionGroup.optionGroupId 
set
	RegOptionGroup.sectionId = Section_RegOptionGroup.sectionId, 
	RegOptionGroup.displayOrder = Section_RegOptionGroup.displayOrder;

-- add regOptionId column and set values.

alter table
	RegOptionGroup
add column
	regOptionId integer;

update
	RegOptionGroup
join
	RegOption_RegOptionGroup
on
	RegOptionGroup.id = RegOption_RegOptionGroup.optionGroupId
set
	RegOptionGroup.regOptionId = RegOption_RegOptionGroup.regOptionId,
	RegOptionGroup.displayOrder = RegOption_RegOptionGroup.displayOrder;

-- apply constraints

alter table
	RegOptionGroup
modify column
	displayOrder integer not null;

alter table
	RegOptionGroup
add constraint
	regOptGroup_sectionId_fk
foreign key
	(sectionId)
references
	Section(id);

alter table
	RegOptionGroup
add constraint
	regOptGroup_regOptionId_fk
foreign key
	(regOptionId)
references
	RegOption(id);

alter table
	RegOptionGroup
add constraint
	regOptGroup_sectionId_dispOrder_uni
unique
	(sectionId, displayOrder);

alter table
	RegOptionGroup
add constraint
	regOptGroup_regOptId_dispOrder_uni
unique
	(regOptionId, displayOrder);

-- add eventId column to RegOptionGroup table.

alter table
	RegOptionGroup
add column
	eventId integer;

-- update eventId for groups under a section.

update
	RegOptionGroup
set
	eventId = (
		select
			Section.eventId
		from
			Section
		where
			Section.id = RegOptionGroup.sectionId
		limit 1
	);

-- update eventId for groups nested under an option.
update
	RegOptionGroup child
inner join (
	select
		Section.eventId as eventId, 
		RegOption.id as optId		
	from
		Section
	inner join
		RegOptionGroup parent
	on
		Section.id = parent.sectionId
	inner join
		RegOption
	on
		parent.id = RegOption.parentGroupId
) as x
on
	child.regOptionId = x.optId
set
	child.eventId = x.eventId;

-- apply eventId constraints.

alter table
	RegOptionGroup
modify column
	eventId integer not null;

alter table
	RegOptionGroup
add constraint
	regOptGroup_eventId_fk
foreign key
	(eventId)
references
	Event(id);







