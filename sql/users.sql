/*
	Raje Singh
	users.sql
	WEBD3201
	September 25, 2020
*/

CREATE EXTENSION IF NOT EXISTS pgcrypto;

DROP SEQUENCE IF EXISTS users_id_seq;
CREATE SEQUENCE users_id_seq START 1000;

DROP TABLE IF EXISTS users;
CREATE TABLE users(
    Id INT PRIMARY KEY DEFAULT nextval('users_id_seq'),
    EmailAddress VARCHAR(255) UNIQUE,
    Password VARCHAR(255) NOT NULL,
    FirstName VARCHAR(128),
    LastName VARCHAR(128),
    LastAccess TIMESTAMP,
    EnrolDate TIMESTAMP,
    PhoneExtension VARCHAR(4),
    Enable BOOLEAN,
    Type VARCHAR(2)

);
INSERT INTO users(EmailAddress, Password, FirstName, LastName, LastAccess, EnrolDate, PhoneExtension, Enable, Type) VALUES (
'jdoe@dcmail.ca', crypt('some_password', gen_salt('bf')), 
'John', 'Doe', '2020-08-22 19:10:25', '2020-06-22 11:11:11', '437', true, 'a');

INSERT INTO users(EmailAddress, Password, FirstName, LastName, LastAccess, EnrolDate, PhoneExtension, Enable, Type) VALUES (
'aneef.chambers@dcmail.ca', crypt('blackdesert', gen_salt('bf')), 
'Aneef', 'Chambers', '2020-10-01 19:10:25', '2020-06-22 11:11:11', '541', true, 'a');

INSERT INTO users(EmailAddress, Password, FirstName, LastName, LastAccess, EnrolDate, PhoneExtension, Enable, Type) VALUES (
'bryan.snaith@dcmail.ca', crypt('friedchicken', gen_salt('bf')), 
'Bryan', 'Snaith', '2020-10-01 19:10:25', '2020-06-22 11:11:11', '432', true, 'a');

INSERT INTO users(EmailAddress, Password, FirstName, LastName, LastAccess, EnrolDate, PhoneExtension, Enable, Type) VALUES (
'deshma.nembhard@dcmail.ca', crypt('fifapro', gen_salt('bf')), 
'Yadeshma', 'Nembhard', '2020-10-15 19:10:25', '2020-06-22 11:11:11', '443', true, 's');

-- SELECT * FROM users;