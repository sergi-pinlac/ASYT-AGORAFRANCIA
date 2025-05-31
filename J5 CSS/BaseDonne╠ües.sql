-- Création de la base
CREATE DATABASE IF NOT EXISTS agora_francia;
USE agora_francia;

-- Table des utilisateurs
CREATE TABLE IF NOT EXISTS utilisateurs (
  id INT AUTO_INCREMENT PRIMARY KEY,
  nom VARCHAR(100),
  prenom VARCHAR(100),
  email VARCHAR(150) UNIQUE,
  mot_de_passe VARCHAR(255),
  type_compte ENUM('admin', 'vendeur', 'acheteur') NOT NULL,
  photo_profil VARCHAR(255),
  image_fond VARCHAR(255)
);

-- Table des articles (corrigée avec la colonne 'quantity')
CREATE TABLE IF NOT EXISTS articles (
  id INT PRIMARY KEY AUTO_INCREMENT,
  vendeur_id INT,
  reference INT,
  nom VARCHAR(255),
  description TEXT,
  type_categorie ENUM('nourriture','vetements','art','armes','mobilier','autres','reliques') NOT NULL,
  prix DECIMAL(10,2),
  quantity INT DEFAULT 1, -- ✅ ligne ajoutée
  type_vente ENUM('achat_immediat', 'negociation', 'enchere') NOT NULL,
  type_article ENUM('rare', 'haut_de_gamme', 'regulier') NOT NULL,
  image_principale VARCHAR(255),
  video_url VARCHAR(255),
  vendu BOOLEAN DEFAULT FALSE,
  date_ajout DATETIME DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (vendeur_id) REFERENCES utilisateurs(id)
);

-- Table des notifications
CREATE TABLE IF NOT EXISTS notifications (
  id INT PRIMARY KEY AUTO_INCREMENT,
  utilisateur_id INT NOT NULL,
  type_notification ENUM('achat', 'negociation', 'enchere', 'alerte', 'livraison') NOT NULL,
  titre VARCHAR(100) NOT NULL,
  message TEXT NOT NULL,
  article_id INT,
  lue BOOLEAN DEFAULT FALSE,
  date_creation DATETIME DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (utilisateur_id) REFERENCES utilisateurs(id),
  FOREIGN KEY (article_id) REFERENCES articles(id)
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
  quantity INT DEFAULT 1,
  statut ENUM('en_attente', 'achete') DEFAULT 'en_attente',
  date_ajout DATETIME DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (acheteur_id) REFERENCES utilisateurs(id),
  FOREIGN KEY (article_id) REFERENCES articles(id)
);

CREATE TABLE paiements (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    adresse VARCHAR(255),
    ville VARCHAR(100),
    code_postal VARCHAR(20),
    pays VARCHAR(100),
    telephone VARCHAR(30),
    type_carte VARCHAR(50),
    numero_carte VARCHAR(30),
    nom_carte VARCHAR(100),
    expiration VARCHAR(10),
    code_securite VARCHAR(10),
    FOREIGN KEY (user_id) REFERENCES utilisateurs(id) ON DELETE CASCADE
);




INSERT INTO utilisateurs (id, nom, prenom, email, mot_de_passe, type_compte)
VALUES 
(1, 'Périclès', 'Athénien', 'pericles@agora.fr', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'vendeur'),
(2, 'Héraclès', 'LeFougueux', 'heracles@olympe.gr', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'vendeur'),
(3, 'Euripide', 'Papadopoulos', 'euripide@theatro.gr', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'vendeur');

-- Insertion des articles
INSERT INTO articles (vendeur_id, reference, nom, description, type_categorie, prix, quantity, type_vente, type_article, image_principale)
VALUES 
(1, 101, 'Couronne de laurier', 'Symbole antique de victoire, en bronze doré.', 'art', 150.00, 2, 'achat_immediat', 'haut_de_gamme', 'images/laurier.jpg'),
(2, 102, 'Buste grec antique', 'Reproduction d un buste de philosophe grec. Résine patinée.', 'art', 300.00, 1, 'enchere', 'rare', 'images/buste.jpg'),
(3, 201, 'Grappes de raisins', 'Raisins de la contrée de Santorin, les préférés de Zeus...', 'nourriture', 8.00, 30, 'achat_immediat', 'rare', 'images/raisins.png'),
(1, 202, 'Olives vertes', 'Un beau bol d olives d Athènes, les meilleures de Grèce.', 'nourriture', 9.00, 40, 'achat_immediat', 'regulier', 'images/olives_vertes.png'),
(2, 203, 'Olives noires', 'Un beau bol d olives d Athènes, les meilleures de Grèce.', 'nourriture', 9.50, 35, 'achat_immediat', 'regulier', 'images/olives_noires.png'),
(2, 301, 'Lances', 'Lances utilisées par les guerriers perses, de bonne facture.', 'armes', 65.00, 5, 'negociation', 'regulier', 'images/lance.png'),
(3, 204, 'Huile d olive', 'Huile pressée à froid d olives d’Attique, pure et parfumée.', 'nourriture', 12.00, 25, 'achat_immediat', 'haut_de_gamme', 'images/huile_olive.png'),
(3, 205, 'Fromage de chèvre', 'Fromage frais affiné au lait de chèvre de Crète.', 'nourriture', 10.00, 20, 'achat_immediat', 'regulier', 'images/fromage_chevre.png'),
(1, 206, 'Figues', 'Figues séchées au soleil, sucrées et moelleuses.', 'nourriture', 7.50, 30, 'achat_immediat', 'regulier', 'images/figues.png'),
(3, 302, 'Épées', 'Épées forgées à la main, inspirées des guerriers de Sparte.', 'armes', 120.00, 3, 'negociation', 'haut_de_gamme', 'images/epees.png'),
(2, 207, 'Dattes', 'Dattes sucrées de la région d’Alexandrie, très nourrissantes.', 'nourriture', 11.00, 50, 'achat_immediat', 'regulier', 'images/dates.png'),
(1, 601, 'Collier', 'Collier artisanal en or et perles, bijou rare.', 'vetements', 95.00, 2, 'enchere', 'rare', 'images/collier.png'),
(2, 402, 'Céramique', 'Vase peint à la main représentant une scène mythologique.', 'mobilier', 130.00, 3, 'enchere', 'haut_de_gamme', 'images/ceramique.png'),
(1, 303, 'Casque', 'Casque de bronze de style corinthien, reproduction fidèle.', 'armes', 140.00, 2, 'enchere', 'rare', 'images/casque.png'),
(3, 602, 'Bracelet', 'Bracelet en bronze antique décoré de motifs grecs.', 'vetements', 45.00, 5, 'achat_immediat', 'haut_de_gamme', 'images/bracelet.png'),
(1, 304, 'Bouclier', 'Bouclier de parade peint, utilisé lors des reconstitutions.', 'armes', 110.00, 3, 'negociation', 'haut_de_gamme', 'images/bouclier.png'),
(3, 603, 'Boucles d oreilles', 'Bijoux finement ciselés en or massif, inspiration hellène.', 'vetements', 80.00, 2, 'enchere', 'haut_de_gamme', 'images/boucles_oreilles.png'),
(2, 403, 'Amphore', 'Amphore antique en terre cuite pour transporter le vin.', 'mobilier', 115.00, 4, 'achat_immediat', 'regulier', 'images/amphore.png'),
(3, 501, 'Trident de Poséidon', 'Hyper pratique pour prendre un bain rapidement.', 'reliques', 100000, 1, 'achat_immediat', 'haut_de_gamme', 'images/trident.png'),
(1, 701, 'Cyclope empaillé', 'Créature mythique naturalisée, trouvée dans une grotte crétoise.', 'autres', 2500.00, 1, 'enchere', 'rare', 'images/cyclope.jpg'),
(2, 404, 'Colonne dorique', 'Colonne de marbre blanc de style dorique, idéale pour une entrée majestueuse.', 'mobilier', 800.00, 2, 'negociation', 'haut_de_gamme', 'images/colonne.png'),
(3, 604, 'Bague en or massif', 'Bague antique gravée, symbole de royauté perse.', 'vetements', 150.00, 3, 'achat_immediat', 'rare', 'images/bague.png'),
(2, 502, 'Œil de Cyclope', 'Relique magique censée voir au-delà du monde visible.', 'reliques', 50000.00, 1, 'enchere', 'rare', 'images/oeil.jpg'),
(1, 103, 'Lyre en or', 'Instrument musical finement orné, ayant appartenu à un poète mythique.', 'art', 1200.00, 1, 'enchere', 'haut_de_gamme', 'images/lyre.png'),
(1, 104, 'Statue de Platon', 'Reproduction fidèle d’une statue grecque en marbre.', 'art', 950.00, 1, 'achat_immediat', 'haut_de_gamme', 'images/statue.png'),
(3, 503, 'Pièces d’or de Troie', 'Trésor antique supposé appartenir à Priam, roi de Troie.', 'reliques', 10000.00, 5, 'achat_immediat', 'rare', 'images/pieces.jpg'),
(2, 701, 'Toge en lin', 'Vêtement léger porté par les citoyens romains et grecs.', 'vetements', 25.00, 50, 'achat_immediat', 'regulier', 'images/toge_lin.jpg'),
(3, 702, 'Amulette dAphrodite', 'Petite amulette en bronze censée porter chance en amour.', 'reliques', 75.00, 10, 'achat_immediat', 'rare', 'images/amulette_aphrodite.jpg'),
(1, 703, 'Ancre antique', 'Ancre en fer forgé utilisée pour les navires grecs.', 'autres', 180.00, 3, 'negociation', 'haut_de_gamme', 'images/ancre.jpg'),
(1, 709, 'Parchemin ancien', 'Rouleau de parchemin avec écriture grecque ancienne.', 'autres', 90.00, 20, 'achat_immediat', 'regulier', 'images/parchemin.png');