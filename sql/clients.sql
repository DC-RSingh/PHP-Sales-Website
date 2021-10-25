/*
	Raje Singh
	clients.sql
	WEBD3201
	November 12, 2020
*/

DROP SEQUENCE IF EXISTS clients_id_seq CASCADE;
CREATE SEQUENCE clients_id_seq START 10000;

DROP TABLE IF EXISTS clients CASCADE;
CREATE TABLE clients(
    Id INT PRIMARY KEY DEFAULT nextval('clients_id_seq'),
    EmailAddress VARCHAR(255) UNIQUE,
    FirstName VARCHAR(128),
    LastName VARCHAR(128),
    PhoneAreaCode VARCHAR(3),
    PhoneNumber VARCHAR(7),
    LogoPath VARCHAR(255),
    SalespersonId INT REFERENCES users ON DELETE CASCADE
);

INSERT INTO clients(EmailAddress, FirstName, LastName, PhoneAreaCode, PhoneNumber, SalespersonId) VALUES(
'jane.doe@dcmail.ca', 'Jane', 'Doe', '437', '2456789', 1003
);
INSERT INTO clients(EmailAddress, FirstName, LastName, PhoneAreaCode, PhoneNumber, SalespersonId) VALUES(
'james.doe@dcmail.ca', 'James', 'Doe', '437', '3567789', 1003
);
INSERT INTO clients(EmailAddress, FirstName, LastName, PhoneAreaCode, PhoneNumber, SalespersonId) VALUES(
'janet.doe@dcmail.ca', 'Janet', 'Doe', '437', '4567889', 1003
);