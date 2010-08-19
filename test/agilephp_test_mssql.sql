CREATE TABLE dbo.inventory (
  id int identity(1,1),
  name varchar(255) NOT NULL,
  description varchar(255) NOT NULL,
  price float NOT NULL,
  category varchar(255) NOT NULL,
  image varbinary(max) NOT NULL,
  video varbinary(max),
  primary key( id )
);

CREATE TABLE dbo.mailing (
  id int identity(1,1),
  name varchar(150) NOT NULL,
  email varchar(150) NOT NULL,
  enabled boolean DEFAULT NULL,
  PRIMARY KEY (id)
);

CREATE TABLE dbo.roles (
  id int identity(1,1),
  name varchar(25) NOT NULL,
  description text,
  PRIMARY KEY (name)
);

CREATE TABLE sessions (
  id varchar(21) NOT NULL DEFAULT '',
  data text,
  created timestamp NOT NULL,
  PRIMARY KEY (id)
);

CREATE TABLE dbo.users (
  username varchar(150) NOT NULL,
  password varchar(255) NOT NULL,
  email varchar(255) NOT NULL,
  created date NOT NULL,
  last_login date DEFAULT NULL,
  roleId int DEFAULT NULL,
  enabled tinyint DEFAULT NULL,
  PRIMARY KEY (username),
  CONSTRAINT FK_UserRoles FOREIGN KEY (roleId) REFERENCES roles (id) ON DELETE SET NULL ON UPDATE CASCADE
);

INSERT INTO roles(name,description) values ('admin','This is an administrator account'),('test','This is a test account');
INSERT INTO users(username,password,email,created,last_login,roleId,enabled) values ('admin','9f86d081884c7d659a2feaa0c55ad015a3bf4f1b2b0b822cd15d6c15b0f00a08','root@localhost','2009-09-06 15:27:44','2010-01-26 22:27:02',1,'1'),('test','9f86d081884c7d659a2feaa0c55ad015a3bf4f1b2b0b822cd15d6c15b0f00a08','test','2010-01-22 19:01:00','2010-01-24 16:26:22',2,NULL);

CREATE PROCEDURE [dbo].[authenticate]( @userid varchar(150), @passwd varchar(255) ) AS
SELECT enabled as authenticate FROM users WHERE username = @userid AND password = @passwd;

CREATE PROCEDURE [dbo].[getusers] AS SELECT * from users;
