-- Création de la base
CREATE DATABASE IF NOT EXISTS agora_francia;
USE agora_francia;

-- Table des utilisateurs
CREATE TABLE IF NOT EXISTS utilisateurs (
  id INT PRIMARY KEY,
  nom VARCHAR(100),
  prenom VARCHAR(100),
  email VARCHAR(150) UNIQUE,
  mot_de_passe VARCHAR(255),
  type ENUM('admin', 'vendeur', 'acheteur') NOT NULL,
  photo_profil VARCHAR(255),
  image_fond VARCHAR(255)
);

-- Table des articles
CREATE TABLE IF NOT EXISTS articles (
  id INT PRIMARY KEY AUTO_INCREMENT,
  vendeur_id INT,
  reference INT,
  nom VARCHAR(255),
  description TEXT,
  type_categorie ENUM('nourriture','vetements','art','armes','mobilier','autres','reliques') NOT NULL,
  prix DECIMAL(10,2),
  type_vente ENUM('achat_immediat', 'negociation', 'enchere') NOT NULL,
  type_article ENUM('rare', 'haut_de_gamme', 'regulier') NOT NULL,
  image_principale VARCHAR(255),
  video_url VARCHAR(255),
  vendu BOOLEAN DEFAULT FALSE,
  date_ajout DATETIME DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (vendeur_id) REFERENCES utilisateurs(id)
);

-- Table des négociations
CREATE TABLE IF NOT EXISTS negociations (
  id INT PRIMARY KEY AUTO_INCREMENT,
  article_id INT,
  acheteur_id INT,
  offre DECIMAL(10,2),
  reponse_vendeur DECIMAL(10,2),
  tour INT,
  date DATETIME DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (article_id) REFERENCES articles(id),
  FOREIGN KEY (acheteur_id) REFERENCES utilisateurs(id)
);

-- Table des enchères
CREATE TABLE IF NOT EXISTS encheres (
  id INT PRIMARY KEY AUTO_INCREMENT,
  article_id INT,
  acheteur_id INT,
  montant_max DECIMAL(10,2),
  date DATETIME DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (article_id) REFERENCES articles(id),
  FOREIGN KEY (acheteur_id) REFERENCES utilisateurs(id)
);

-- Table des paniers
CREATE TABLE IF NOT EXISTS paniers (
  id INT PRIMARY KEY AUTO_INCREMENT,
  acheteur_id INT,
  article_id INT,
  statut ENUM('en_attente', 'achete') DEFAULT 'en_attente',
  date_ajout DATETIME DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (acheteur_id) REFERENCES utilisateurs(id),
  FOREIGN KEY (article_id) REFERENCES articles(id)
);

-- Table des paiements
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

-- Insertion des utilisateurs
INSERT INTO utilisateurs (id, nom, prenom, email, mot_de_passe, type)
VALUES 
(1, 'Périclès', 'Athénien', 'pericles@agora.fr', 'pericles', 'vendeur'),
(2, 'Héraclès', 'LeFougueux', 'heracles@olympe.gr', 'heracles', 'vendeur'),
(3, 'Euripide', 'Papadopoulos', 'euripide@theatro.gr', 'euripide', 'vendeur');

-- Insertion des articles
INSERT INTO articles (vendeur_id, reference, nom, description, type_categorie, prix, type_vente, type_article, image_principale)
VALUES 
(1, 101, 'Couronne de laurier', 'Symbole antique de victoire, en bronze doré.', 'art', 150.00, 'achat_immediat', 'haut_de_gamme', 'images/laurier.jpg'),
(2, 102, 'Buste grec antique', 'Reproduction d un buste de philosophe grec. Résine patinée.', 'art', 300.00, 'enchere', 'rare', 'images/buste.jpg'),
(3, 201, 'Grappes de raisins', 'Raisins de la contrée de Santorin, les préférés de Zeus...', 'nourriture', 8.00, 'achat_immediat', 'rare', 'images/raisins.png'),
(1, 202, 'Olives vertes', 'Un beau bol d olives d Athènes, les meilleures de Grèce.', 'nourriture', 9.00, 'achat_immediat', 'regulier', 'images/olives_vertes.png'),
(2, 203, 'Olives noires', 'Un beau bol d olives d Athènes, les meilleures de Grèce.', 'nourriture', 9.50, 'achat_immediat', 'regulier', 'images/olives_noires.png'),
(2, 301, 'Lances', 'Lances utilisées par les guerriers perses, de bonne facture.', 'armes', 65.00, 'negociation', 'regulier', 'images/lance.png'),
(3, 204, 'Huile d olive', 'Huile pressée à froid d olives d’Attique, pure et parfumée.', 'nourriture', 12.00, 'achat_immediat', 'haut_de_gamme', 'images/huile_olive.png'),
(3, 205, 'Fromage de chèvre', 'Fromage frais affiné au lait de chèvre de Crète.', 'nourriture', 10.00, 'achat_immediat', 'regulier', 'images/fromage_chevre.png'),
(1, 206, 'Figues', 'Figues séchées au soleil, sucrées et moelleuses.', 'nourriture', 7.50, 'achat_immediat', 'regulier', 'images/figues.png'),
(3, 302, 'Épées', 'Épées forgées à la main, inspirées des guerriers de Sparte.', 'armes', 120.00, 'negociation', 'haut_de_gamme', 'images/epees.png'),
(2, 207, 'Dattes', 'Dattes sucrées de la région d’Alexandrie, très nourrissantes.', 'nourriture', 11.00, 'achat_immediat', 'regulier', 'images/dates.png'),
(1, 601, 'Collier', 'Collier artisanal en or et perles, bijou rare.', 'vetements', 95.00, 'enchere', 'rare', 'images/collier.png'),
(2, 402, 'Céramique', 'Vase peint à la main représentant une scène mythologique.', 'mobilier', 130.00, 'enchere', 'haut_de_gamme', 'images/ceramique.png'),
(1, 303, 'Casque', 'Casque de bronze de style corinthien, reproduction fidèle.', 'armes', 140.00, 'enchere', 'rare', 'images/casque.png'),
(3, 602, 'Bracelet', 'Bracelet en bronze antique décoré de motifs grecs.', 'vetements', 45.00, 'achat_immediat', 'haut_de_gamme', 'images/bracelett.jpg'),
(1, 304, 'Bouclier', 'Bouclier de parade peint, utilisé lors des reconstitutions.', 'armes', 110.00, 'negociation', 'haut_de_gamme', 'images/bouclier.png'),
(3, 603, 'Boucles d oreilles', 'Bijoux finement ciselés en or massif, inspiration hellène.', 'vetements', 80.00, 'enchere', 'haut_de_gamme', 'images/boucles_oreilles.png'),
(2, 403, 'Amphore', 'Amphore antique en terre cuite pour transporter le vin.', 'mobilier', 115.00, 'achat_immediat', 'regulier', 'images/amphore.png');
