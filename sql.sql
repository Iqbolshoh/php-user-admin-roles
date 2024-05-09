CREATE DATABASE IF NOT EXISTS Roles; 
USE Roles;

CREATE TABLE IF NOT EXISTS accounts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(30) NOT NULL,
    number VARCHAR(20) NOT NULL UNIQUE,
    email VARCHAR(255) NOT NULL UNIQUE,
    username VARCHAR(255) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role VARCHAR(20) NOT NULL DEFAULT 'user',
    profile_image VARCHAR(255) DEFAULT 'no_image.png',
    registration_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

INSERT INTO accounts (name, number, email, username, password, role) VALUES
('Iqbolshoh', '997799333', 'Iqbolshoh@gmail.com', 'Iqbolshoh', 'ed84bce861e67710a76393623d36b5ca6b9bcaaf658f57232be80c85af0ee52e', 'admin'),
('user', '993399777', 'user@gmail.com', 'user', 'ed84bce861e67710a76393623d36b5ca6b9bcaaf658f57232be80c85af0ee52e', 'user');
