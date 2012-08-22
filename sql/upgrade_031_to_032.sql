create table if not exists `Event_Metadata` (
    `id`                integer         not null auto_increment,
    `eventId`           integer         not null,
    `contactFieldId`    integer         not null,
    `metadata`          varchar(255)    not null,
    primary key(`id`)
) ENGINE=InnoDB default CHARSET=utf8;

alter table Event_Metadata
    add constraint event_meta_eventId_fk
    foreign key (eventId) references Event(id);
    
alter table Event_Metadata
    add constraint event_meta_contactFieldId_fk
    foreign key (contactFieldId) references ContactField(id);
    
alter table Event_Metadata
    add constraint event_meta_event_cf_uni
    unique (eventId, contactFieldId);
    
    
    
