
alter table Registration
    add column
    leadNumber integer(5);
    

alter table BadgeCell_TextContent
    add column
    showRegType char(1) not null default 'F';

alter table BadgeCell_TextContent
    add column
    showLeadNumber char(1) not null default 'F';
    
    
alter table Registration
    add constraint registration_eventid_leadNum_uni
    unique (eventId, leadNumber);
    
-- ---------------------------------------------
-- set lead numbers for existing registrations.
-- ---------------------------------------------
-- update Registration set leadNumber = FLOOR(5000 + (RAND()*94999)) where eventId = ? and id>=0 and id<?
-- ---------------------------
