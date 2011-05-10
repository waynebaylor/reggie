
create table if not exists `StaticPage` (
	`id`			integer		not null auto_increment,
	`eventId`		integer		not null,
	`name`			varchar(100)	not null,
	`title`			varchar(255),
	`content`		text,
	primary key(`id`)
) ENGINE=InnoDB default CHARSET=utf8;


alter table StaticPage
	add constraint staticPage_eventId_fk
	foreign key (eventId) references Event(id);
	
alter table StaticPage
    add constraint staticPage_eventIdName_uni
    unique(eventId, name);

