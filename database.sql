drop database if exists modend;
create database modend;

use modend;

create table modules(
	moduleId int auto_increment primary key,
	moduleName varchar(50) not null,
	installedVersion double not null
);

create table exceptions(
	exceptionId int auto_increment primary key,
	exceptionType varchar(20) not null,
	exceptionMessage varchar(255) not null,
	inputData blob,
	exceptionDate datetime not null
);