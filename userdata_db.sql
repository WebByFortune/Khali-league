CREATE DATABASE userdata_db;
USE userdata_db;



CREATE TABLE users(
    id INT PRIMARY KEY AUTO_INCREMENT,
    username VARCHAR(50) NOT NULL,
    email VARCHAR(100) NOT NULL,
    password VARCHAR (255) NOT NULL,
    is_admin TINYINT (1) DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP


);


INSERT INTO users(username,email,password,is_admin) VALUES(
    'admin', 'joeadmin@gmail.com', '#Isadmin123',1 
)
