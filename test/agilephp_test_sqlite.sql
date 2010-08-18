CREATE TABLE "users" (
	"username" varchar PRIMARY KEY NOT NULL,
	"password" varchar NOT NULL,
	"email" varchar NOT NULL,
	"created" datetime NOT NULL,
	"last_login" datetime,
	"roleId" integer CONSTRAINT FK_UserRoles REFERENCES roles(id),
	"enabled" bool
);

CREATE TABLE "roles" (
    "id" INTEGER PRIMARY KEY AUTOINCREMENT,
	"name" varchar PRIMARY KEY NOT NULL,
	"description" text
);

CREATE TABLE "sessions" (
	"id" varchar PRIMARY KEY,
	"data" text,
	"created" timestamp DEFAULT 'CURRENT_TIMESTAMP'
);

CREATE TABLE "inventory" (
  "id" INTEGER PRIMARY KEY AUTOINCREMENT,
  "name" varchar NOT NULL,
  "description" varchar NOT NULL,
  "price" decimal NOT NULL,
  "category" varchar NOT NULL,
  "image" blob NOT NULL,
  "video" blob
);

CREATE TABLE "mailing" (
  "id" INTEGER NOT NULL PRIMARY KEY AUTOINCREMENT,
  "name" varchar NOT NULL,
  "email" varchar NOT NULL,
  "enabled" bool DEFAULT NULL
);

insert  into "roles"(name,description) values ('admin','This is an administrator account');
insert  into "roles"(name,description) values ('test','This is a test account');

insert  into "users"(username,password,email,created,last_login,roleId,enabled) values ('admin','9f86d081884c7d659a2feaa0c55ad015a3bf4f1b2b0b822cd15d6c15b0f00a08','root@localhost','2009-09-06 15:27:44','2010-01-26 22:27:02',1,'1');
insert  into "users"(username,password,email,created,last_login,roleId,enabled) values ('test','9f86d081884c7d659a2feaa0c55ad015a3bf4f1b2b0b822cd15d6c15b0f00a08','test','2010-01-22 19:01:00','2010-01-24 16:26:22',2,NULL);

CREATE TRIGGER tirFK_UserRoles_fkInsert
BEFORE INSERT ON [users]
FOR EACH ROW BEGIN
	SELECT RAISE( ROLLBACK, 'Insert on table "users" violates foreign key constraint "FK_UserRoles"' )
	WHERE NEW.roleId IS NOT NULL AND (SELECT id FROM roles WHERE name = NEW.roleId) IS NULL;
END;


CREATE TRIGGER turFK_UserRoles_refUpdate
BEFORE UPDATE ON [users]
FOR EACH ROW BEGIN
    SELECT RAISE( ROLLBACK, 'Update on table "users" violates foreign key constraint "FK_UserRoles"' )
      WHERE NEW.roleId IS NOT NULL AND (SELECT id FROM roles WHERE name = NEW.roleId) IS NULL;
END;

CREATE TRIGGER tdrFK_UserRoles_refDelete
BEFORE DELETE ON [roles]
FOR EACH ROW BEGIN
  SELECT RAISE( ROLLBACK, 'Delete on table "roles" violates foreign key constraint "FK_UserRoles"' )
  WHERE (SELECT roleId FROM users WHERE roleId = OLD.name) IS NOT NULL;
END;

CREATE TRIGGER tucFK_UserRoles
BEFORE UPDATE ON [roles]
FOR EACH ROW BEGIN
    UPDATE users SET roleId = NEW.name WHERE users.roleId = OLD.name;
END;

CREATE TRIGGER tdsnFK_UserRoles
BEFORE DELETE ON [roles]
FOR EACH ROW BEGIN
    UPDATE users SET roleId = NULL WHERE roleId = OLD.name;
END;