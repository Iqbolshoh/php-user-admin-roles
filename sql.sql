CREATE DATABASE IF NOT EXISTS Roles; 
USE Roles;

CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(30) NOT NULL,
    email VARCHAR(255) NOT NULL UNIQUE,
    username VARCHAR(255) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role VARCHAR(20) NOT NULL DEFAULT 'user'
);

INSERT INTO users (name, email, username, password, role) VALUES
('Iqbolshoh', 'Iqbolshoh@gmail.com', 'Iqbolshoh', 'ed84bce861e67710a76393623d36b5ca6b9bcaaf658f57232be80c85af0ee52e', 'admin'),
('user', 'user@gmail.com', 'user', 'ed84bce861e67710a76393623d36b5ca6b9bcaaf658f57232be80c85af0ee52e', 'user');