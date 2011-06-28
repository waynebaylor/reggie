
create table if not exists `BadgeTemplate` (
    `id`            integer         not null auto_increment,
    `eventId`       integer         not null,
    `name`          varchar(255)    not null,
    `type`          varchar(255)    not null,
    primary key(`id`)
) ENGINE=InnoDB default CHARSET=utf8;

create table if not exists `BadgeTemplate_RegType` (
    `id`                integer         not null auto_increment,
    `badgeTemplateId`   integer         not null,
    `regTypeId`         integer,
    primary key(`id`)
) ENGINE=InnoDB default CHARSET=utf8;

create table if not exists `BadgeCell` (
    `id`                integer         not null auto_increment,
    `badgeTemplateId`   integer         not null,
    `xCoord`            decimal(10,3)   not null,
    `yCoord`            decimal(10,3)   not null,
    `width`             decimal(10,3)   not null,
    `font`              varchar(255)    not null,
    `fontSize`          decimal(10,1)   not null,
    `horizontalAlign`   char(1)         not null,
    `hasBarcode`        char(1)         not null,
    primary key(`id`)
) ENGINE=InnoDB default CHARSET=utf8;

create table if not exists `BadgeCell_TextContent` (
    `id`                integer         not null auto_increment,
    `badgeCellId`       integer         not null,      
    `displayOrder`      integer         not null,
    `text`              varchar(255),
    `contactFieldId`    integer,
    primary key(`id`)
) ENGINE=InnoDB default CHARSET=utf8;

create table if not exists `BadgeBarcodeField` (
    `id`                integer         not null auto_increment,
    `badgeCellId`       integer         not null,    
    `contactFieldId`    integer         not null,  
    primary key(`id`)
) ENGINE=InnoDB default CHARSET=utf8;

alter table BadgeTemplate
    add constraint badgeTemplate_eventId_fk
    foreign key (eventId) references Event(id);
    
alter table BadgeTemplate_RegType
    add constraint badgeTempl_regType_badgeId_fk
    foreign key (badgeTemplateId) references BadgeTemplate(id);
    
alter table BadgeTemplate_RegType
    add constraint badgeTempl_regType_regTypeId_fk
    foreign key (regTypeId) references RegType(id);
    
alter table BadgeCell
    add constraint badgeCell_badgeTemplId_fk
    foreign key (badgeTemplateId) references BadgeTemplate(id);
    
alter table BadgeCell_TextContent
    add constraint badgeCell_textCont_badgeCellId_fk
    foreign key (badgeCellId) references BadgeCell(id);
    
alter table BadgeCell_TextContent
    add constraint badgeCell_textCont_contFieldId_fk
    foreign key (contactFieldId) references ContactField(id);
    
alter table BadgeCell_TextContent
    add constraint badgeCell_textCont_cellId_dispOrder_uni
    unique (badgeCellId, displayOrder);

alter table BadgeBarcodeField
    add constraint badgeBarcodeField_badgeCellId_fk
    foreign key (badgeCellId) references BadgeCell(id);
    
alter table BadgeBarcodeField
    add constraint badgeBarcodeField_contFieldId_fk
    foreign key (contactFieldId) references ContactField(id);
    
    
        
