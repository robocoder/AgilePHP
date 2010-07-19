DROP DATABASE IF EXISTS agilephp_test;
CREATE DATABASE agilephp_test;

\connect agilephp_test;

DROP TABLE IF EXISTS inventory;
CREATE TABLE inventory (
  id SERIAL,
  name varchar(255) NOT NULL,
  description varchar(255) NOT NULL,
  price float NOT NULL,
  category varchar(255) NOT NULL,
  image bytea NOT NULL,
  video bytea,
  primary key( id )
);

DROP TABLE IF EXISTS mailing;
CREATE TABLE mailing (
  id SERIAL,
  name varchar(150) NOT NULL,
  email varchar(150) NOT NULL,
  enabled boolean DEFAULT NULL,
  PRIMARY KEY (id)
);

DROP TABLE IF EXISTS roles;
CREATE TABLE roles (
  name varchar(25) NOT NULL,
  description text,
  PRIMARY KEY (name)
);

DROP TABLE IF EXISTS sessions;
CREATE TABLE sessions (
  id varchar(21) NOT NULL DEFAULT '',
  data text,
  created timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id)
);

DROP TABLE IF EXISTS users;
CREATE TABLE users (
  username varchar(150) NOT NULL,
  password varchar(255) NOT NULL,
  email varchar(255) NOT NULL,
  created date NOT NULL,
  last_login date DEFAULT NULL,
  roleId varchar(25) DEFAULT NULL,
  enabled boolean DEFAULT NULL,
  PRIMARY KEY (username),
  CONSTRAINT FK_UserRoles FOREIGN KEY (roleId) REFERENCES roles (name) ON DELETE SET NULL ON UPDATE CASCADE
);

INSERT INTO roles(name,description) values ('admin','This is an administrator account'),('test','This is a test account');
INSERT INTO users(username,password,email,created,last_login,roleId,enabled) values ('admin','9f86d081884c7d659a2feaa0c55ad015a3bf4f1b2b0b822cd15d6c15b0f00a08','root@localhost','2009-09-06 15:27:44','2010-01-26 22:27:02','admin','1'),('test','9f86d081884c7d659a2feaa0c55ad015a3bf4f1b2b0b822cd15d6c15b0f00a08','test','2010-01-22 19:01:00','2010-01-24 16:26:22','test',NULL);

CREATE FUNCTION getusers() RETURNS SETOF users as $$
 SELECT * FROM users;
$$ language SQL;

CREATE FUNCTION authenticate( userid varchar, passwd varchar) RETURNS bool AS $$
DECLARE
 result bool;
BEGIN
 SELECT count(*) INTO result FROM users where username = userid AND password = passwd;
RETURN result;
END;
$$ language plpgsql;
