/*
	Raje Singh
	calls.sql
	WEBD3201
	October 15, 2020
*/

DROP SEQUENCE IF EXISTS calls_id_seq CASCADE;
CREATE SEQUENCE calls_id_seq START 1;

DROP TABLE IF EXISTS calls CASCADE;
CREATE TABLE calls(
    Id INT PRIMARY KEY DEFAULT nextval('calls_id_seq'),
    ClientId INT REFERENCES clients ON DELETE CASCADE,
    CallStartTime TIMESTAMP,
    CallEndTime TIMESTAMP
);

INSERT INTO calls(ClientId, CallStartTime, CallEndTime) Values(
    10000, '2020-10-15 17:30:25', '2020-10-15 17:50:25'
);
INSERT INTO calls(ClientId, CallStartTime, CallEndTime) Values(
    10001, '2020-10-15 19:10:25', '2020-10-15 19:20:45'
);
INSERT INTO calls(ClientId, CallStartTime, CallEndTime) Values(
    10002, '2020-10-15 16:10:25', '2020-10-15 16:25:30'
);