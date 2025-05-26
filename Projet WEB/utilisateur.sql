CREATE DATABASE IF NOT EXISTS agora_francia;

USE agora_francia;

CREATE TABLE IF NOT EXISTS utilisateurs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(100) NOT NULL,
    prenom VARCHAR(100) NOT NULL,
    email VARCHAR(150) NOT NULL UNIQUE,
    mot_de_passe VARCHAR(255) NOT NULL,
    type_compte ENUM('client', 'vendeur', 'admin') NOT NULL   
);
