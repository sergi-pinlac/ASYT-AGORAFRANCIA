-- Création de la base (optionnel si déjà créée)
CREATE DATABASE IF NOT EXISTS agora_francia;
USE agora_francia;

-- Table utilisateurs
CREATE TABLE IF NOT EXISTS utilisateurs (
  id INT PRIMARY KEY AUTO_INCREMENT,
  nom VARCHAR(100),
  prenom VARCHAR(100),
  email VARCHAR(150) UNIQUE,
  mot_de_passe VARCHAR(255),
  type ENUM('admin', 'vendeur', 'acheteur') NOT NULL,
  photo_profil VARCHAR(255),
  image_fond VARCHAR(255)
);

-- Table articles
CREATE TABLE IF NOT EXISTS articles (
  id INT PRIMARY KEY AUTO_INCREMENT,
  vendeur_id INT,
  nom VARCHAR(255),
  description TEXT,
  prix DECIMAL(10,2),
  type_vente ENUM('achat_immediat', 'negociation', 'enchere') NOT NULL,
  type_article ENUM('rare', 'haut_de_gamme', 'regulier') NOT NULL,
  image_principale VARCHAR(255),
  video_url VARCHAR(255),
  vendu BOOLEAN DEFAULT FALSE,
  date_ajout DATETIME DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (vendeur_id) REFERENCES utilisateurs(id)
);

CREATE TABLE IF NOT EXISTS negociations (
  id INT PRIMARY KEY AUTO_INCREMENT,
  article_id INT,
  acheteur_id INT,
  offre DECIMAL(10,2),
  reponse_vendeur DECIMAL(10,2),
  tour INT, -- de 1 à 5
  date DATETIME DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (article_id) REFERENCES articles(id),
  FOREIGN KEY (acheteur_id) REFERENCES utilisateurs(id)
);


CREATE TABLE IF NOT EXISTS encheres (
  id INT PRIMARY KEY AUTO_INCREMENT,
  article_id INT,
  acheteur_id INT,
  montant_max DECIMAL(10,2),
  date DATETIME DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (article_id) REFERENCES articles(id),
  FOREIGN KEY (acheteur_id) REFERENCES utilisateurs(id)
);

CREATE TABLE IF NOT EXISTS paniers (
  id INT PRIMARY KEY AUTO_INCREMENT,
  acheteur_id INT,
  article_id INT,
  statut ENUM('en_attente', 'achete') DEFAULT 'en_attente',
  date_ajout DATETIME DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (acheteur_id) REFERENCES utilisateurs(id),
  FOREIGN KEY (article_id) REFERENCES articles(id)
);


CREATE TABLE IF NOT EXISTS paiements (
  id INT PRIMARY KEY AUTO_INCREMENT,
  utilisateur_id INT,
  type_carte ENUM('Visa', 'MasterCard', 'Amex', 'PayPal'),
  numero_carte VARCHAR(20),
  nom_carte VARCHAR(100),
  expiration DATE,
  code_securite VARCHAR(5),
  FOREIGN KEY (utilisateur_id) REFERENCES utilisateurs(id)
);


INSERT INTO utilisateurs (nom, prenom, email, mot_de_passe, type)
VALUES ('Périclès', 'Athénien', 'pericles@agora.fr', 'motdepasse', 'vendeur');


INSERT INTO articles (vendeur_id, nom, description, prix, type_vente, type_article, image_principale)
VALUES 
(1, 'Couronne de laurier', 'Symbole antique de victoire, en bronze doré.', 150.00, 'achat_immediat', 'haut_de_gamme', 'images/laurier.jpg'),
(2, 'Buste grec antique', 'Reproduction d\'un buste de philosophe grec. Résine patinée.', 300.00, 'enchere', 'rare', 'images/buste.jpg'):