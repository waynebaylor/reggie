
-- --------------------------------------------

insert into 
	FormInput(id, name, displayName) 
values
	(1, 'text', 'Text Field'),
	(2, 'textarea', 'Text Area'),
	(3, 'checkbox', 'Checkbox'),
	(4, 'radio', 'Radio Button'),
	(5, 'select', 'Select');
	
-- --------------------------------------------
	
insert into
	Validation(id, name, displayName)
values
	(1, 'required', 'Required'),
	(2, 'minlength', 'Minimum Length (in characters)'),
	(3, 'maxlength', 'Maximum Length (in characters)');
	
-- --------------------------------------------
	
insert into 
	Attribute(id, name, displayName)
values
	(1, 'size', 'Display Size (in characters)'),
	(2, 'cols', 'Display Width (in characters)'),
	(3, 'rows', 'Display Height (in lines)');
	
-- --------------------------------------------
	
insert into
	FormInputValidation(formInputId, validationId)
values
	(1, 1), -- text
	(1, 2),
	(1, 3),
	(2, 1), -- textarea
	(2, 2),
	(2, 3),
	(3, 1), -- checkbox
	(4, 1), -- radio
	(5, 1); -- select
	
-- --------------------------------------------

insert into
	FormInputAttribute(formInputId, attributeId)
values
	(1, 1), -- text
	(2, 2), -- textarea
	(2, 3);
	
-- --------------------------------------------

insert into
	Category(id, displayName)
values
	(1, 'Attendee'),
	(2, 'Exhibitor'),
	(3, 'Special');	
	
-- --------------------------------------------

insert into
	ContentType(id, name)
values
	(1, 'Registration Types'),
	(2, 'Information Fields'),
	(3, 'Registration Options'),
	(4, 'Text'),
	(5, 'Variable Quantity Options');

-- --------------------------------------------

insert into
	PaymentType(id, displayName)
values
	(1, 'Check'),
	(2, 'Purchase Order'),
	(3, 'Authorize.NET');

-- --------------------------------------------	

insert into
	User(id, email, password)
values
	(1, 'wade.taylor@baylorsc.com', '400e999ddb50b048dff711856116871d09b7ff55');

-- --------------------------------------------	

insert into
    Role(id, name, scope, description)
values
    (1, 'System Administrator', 'GENERAL',  'Create, edit, and delete users and events. Full access to all existing event features.'),
    (2, 'User Administrator',   'GENERAL',  'Create, edit, and delete users.'),
    (3, 'Event Administrator',  'GENERAL',  'Create, edit, and delete events. Full access to all existing event features.'),
    (4, 'Event Manager',        'EVENT',    'Edit and delete event. Full access to event features.'),
    (5, 'Event Registrar',      'EVENT',    'Create, edit, and cancel registrations. View, create, edit, and delete event reports. View attendee summaries.'),
    (6, 'View Event',           'EVENT',    'View event reports and attendee summaries.');
    
    
-- --------------------------------------------	

insert into
    User_Role(userId, roleId)
values
    (1, 1);     -- make wade system admin
    
-- --------------------------------------------	
-- --------------------------------------------	




	
