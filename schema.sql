CREATE DATABASE doingsdoneDB
  DEFAULT CHARACTER SET utf8
  DEFAULT COLLATE utf8_general_ci;


USE doingsdoneDB;

CREATE TABLE projects (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name CHAR(255),
  user_id INT
);

CREATE TABLE tasks (
  id INT AUTO_INCREMENT PRIMARY KEY,
  dt_add DATETIME(0),
  dt_done DATETIME(0),
  name CHAR(255),
  file_path CHAR(255),
  dt_deadline DATETIME(0),
  user_id INT,
  project_id INT
);

CREATE TABLE users (
  id INT AUTO_INCREMENT PRIMARY KEY,
  dt_reg DATETIME(0),
  email CHAR(255),
  name CHAR(255),
  password CHAR(255),
  contacts CHAR(255)
);

CREATE UNIQUE INDEX email ON users(email);
CREATE UNIQUE INDEX filepath ON tasks(file_path);
CREATE INDEX task_name ON tasks(name);

