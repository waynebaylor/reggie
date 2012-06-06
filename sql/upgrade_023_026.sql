
alter table User_Event
    drop foreign key userEvent_userId_fk;
    
alter table User_Event
    drop foreign key userEvent_eventId_fk;
    
alter table User_Event
    drop index userEvent_userId_eventId_uni;

drop table if exists User_Event;

alter table RegOption
    add column
    `text` text;
    
alter table User
    drop column isAdmin;
    
create table if not exists `Role` (
    `id`            integer         not null auto_increment,
    `name`          varchar(255)    not null,
    `description`   text            not null,
    `scope`         varchar(255)    not null,
    primary key(`id`)
) ENGINE=InnoDB default CHARSET=utf8;

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
    
insert into
    Role(id, name, scope, description)
values
    (1, 'System Administrator', 'GENERAL',  'Create, edit, and delete users and events. Full access to all existing event features.'),
    (2, 'User Administrator',   'GENERAL',  'Create, edit, and delete users.'),
    (3, 'Event Administrator',  'GENERAL',  'Create, edit, and delete events. Full access to all existing event features.'),
    (4, 'Event Manager',        'EVENT',    'Edit and delete event. Full access to event features.'),
    (5, 'Event Registrar',      'EVENT',    'Create, edit, and cancel registrations. View, create, edit, and delete event reports. View attendee summaries.'),
    (6, 'View Event',           'EVENT',    'View event reports and attendee summaries.');
    
insert into
    User_Role(userId, roleId)
values
    (1, 1);
    
