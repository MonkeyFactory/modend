drop database if exists modend;
create database modend;

use modend;

create table modules(
	moduleId int auto_increment primary key,
	moduleName varchar(50) not null,
	installedVersion double not null
);

insert into modules (moduleName, installedVersion) values("page", 1.0);