<?php
session_start();
$pdo = new PDO('mysql:host=localhost;dbname=agora_francia;charset=utf8', 'root', '');

if (!isset($_SESSION['utilisateur'])) {
    header("Location: connexion.html");
    exit;
}

$utilisateur = $_SESSION['utilisateur'];
$type = $utilisateur['type'];

// Traitement des actions sur les offres
if (isset($_POST['action_offre'])) {
    $offreId = $_POST['offre_id'];
    $action = $_POST['action_offre'];
    $articleId = $_POST['article_id'];
    
    if ($action === 'accepter') {
        // Marquer l'article comme vendu
        $stmt = $pdo->prepare("UPDATE articles SET vendu = TRUE WHERE id = ?");
        $stmt->execute([$articleId]);
        
        // Créer une notification pour l'acheteur
        $stmt = $pdo->prepare("INSERT INTO notifications (utilisateur_id, type_notification, titre, message, article_id) 
                              SELECT acheteur_id, 'achat', 'Offre acceptée', CONCAT('Votre offre a été acceptée pour l\'article ', a.nom), ? 
                              FROM encheres e JOIN articles a ON e.article_id = a.id 
                              WHERE e.id = ?");
        $stmt->execute([$articleId, $offreId]);
        
        echo "<script>alert('Offre acceptée avec succès');</script>";
    }
    
    // Supprimer l'offre
    $stmt = $pdo->prepare("DELETE FROM encheres WHERE id = ?");
    $stmt->execute([$offreId]);
    
    $stmt = $pdo->prepare("DELETE FROM negociations WHERE id = ?");
    $stmt->execute([$offreId]);
    
    // Pour les achats immédiats, mettre à jour le statut du panier
    $stmt = $pdo->prepare("UPDATE paniers SET statut = 'achete' WHERE id = ?");
    $stmt->execute([$offreId]);
}

// Traitement ajout article
if (isset($_POST['ajouter_article'])) {
    $stmt = $pdo->prepare("INSERT INTO articles (vendeur_id, reference, nom, description, type_categorie, prix, type_vente, type_article, image_principale, video_url)
                           VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->execute([
        $utilisateur['id'],
        rand(100, 999),
        $_POST['nom_article'],
        $_POST['description'],
        $_POST['type_categorie'],
        $_POST['prix'],
        $_POST['type_vente'],
        $_POST['type_article'],
        $_POST['image_principale'] ?? null,
        $_POST['video_url'] ?? null
    ]);
    echo "<script>alert('✅ Article publié avec média.');</script>";
}

// Traitement ajout vendeur
if (isset($_POST['ajouter_vendeur']) && $type === 'admin') {
    $stmt = $pdo->prepare("INSERT INTO utilisateurs (nom, prenom, email, mot_de_passe, type_compte) VALUES (?, ?, ?, ?, 'vendeur')");
    $stmt->execute([
        $_POST['nom_vendeur'],
        $_POST['prenom_vendeur'],
        $_POST['email_vendeur'],
        password_hash($_POST['mot_de_passe'], PASSWORD_DEFAULT)
    ]);
    echo "<script>alert('✅ Vendeur ajouté.');</script>";
}

// Traitement suppression vendeur
if (isset($_POST['supprimer_vendeur']) && $type === 'admin') {
    $stmt = $pdo->prepare("DELETE FROM utilisateurs WHERE email = ? AND type_compte = 'vendeur'");
    $stmt->execute([$_POST['email_suppression']]);
    echo "<script>alert('🗑 Vendeur supprimé.');</script>";
}
?>

<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8">
  <title>Agora Francia - Espace utilisateur</title>
  <style>
    @import url('https://fonts.googleapis.com/css2?family=EB+Garamond&display=swap');

    body {
      font-family: 'EB Garamond', serif;
      background-color: #fdfaf4;
      background-image: url('https://www.transparenttextures.com/patterns/paper-fibers.png');
      margin: 20px;
      color: #3a2f0b;
    }

    .wrapper {
      position: relative;
      border: 10px double #a87e41;
      padding: 20px;
      max-width: 900px;
      margin: auto;
      box-shadow: 0 0 20px rgba(0,0,0,0.3)
    }
/* Animation au survol : le bouton se soulève légèrement et le fond grise */
.navigation button:hover {
  background-color: #cfa95e; /* gris clair */
  transform: translateY(-4px);
  box-shadow: 0 6px 12px rgba(0,0,0,0.15);
  transition: background-color 0.3s, transform 0.3s, box-shadow 0.3s;
}
    .header {
      display: flex;
      justify-content: space-between;
      align-items: center;
      border-bottom: 4px solid #a87e41;
      padding-bottom: 10px;
      margin-bottom: 10px;
    }

    .header h1 {
      color: #2f4a6d;
      font-size: 40px;
      letter-spacing: 2px;
      margin: 0;
      text-transform: uppercase;
    }

    .logo img {
      height: 70px;
      border: 2px solid #a87e41;
      padding: 5px;
      background-color: #fff;
    }

    .navigation {
      display: flex;
      justify-content: space-around;
      background-color: #f7efe3;
      border: 3px solid #a87e41;
      padding: 10px;
      margin: 15px 0;
    }

    .navigation button {
      background-color: #e5d4a1;
      border: 2px solid #a87e41;
      border-radius: 6px;
      padding: 10px 15px;
      font-size: 16px;
      font-weight: bold;
      color: #3a2f0b;
      cursor: pointer;
      box-shadow: 2px 2px 5px rgba(0,0,0,0.1);
      transition: background 0.3s;
    }

    .navigation button:hover {
      background-color: #d1b97b;
    }

    form {
      background-color: #fffaf0;
      padding: 20px;
      border: 2px solid #a87e41;
      border-radius: 10px;
      margin-bottom: 30px;
    }

    h3, h4 {
      margin-top: 30px;
    }

    input, select, textarea {
      width: 100%;
      padding: 8px;
      margin-bottom: 15px;
      border: 1px solid #ccc;
      border-radius: 6px;
    }

    button[type="submit"], button {
      background-color: #e5d4a1;
      color: #3a2f0b;
      border: 2px solid #a87e41;
      border-radius: 6px;
      padding: 10px 15px;
      font-weight: bold;
      cursor: pointer;
    }

    button:hover {
      background-color: #d1b97b;
    }

    .footer {
      text-align: center;
      padding: 15px;
      margin-top: 20px;
      border-top: 4px solid #a87e41;
      color: #3a2f0b;
      font-weight: bold;
      font-size: 18px;
    }

    /* Styles pour les offres */
    .offer-container {
      border: 1px solid #a87e41;
      padding: 15px;
      margin: 10px 0;
      border-radius: 8px;
      background-color: #fffaf0;
    }

    .offer-actions {
      margin-top: 10px;
    }

    .offer-actions button {
      margin-right: 10px;
    }

    .history-item {
      border: 1px solid #ccc;
      padding: 10px;
      margin: 5px 0;
      border-radius: 5px;
      background-color: #f7efe3;
    }
  </style>
</head>
<body>
  <div class="wrapper">
    <div class="header">
      <h1>Agora Francia</h1>
      <div class="logo">
        <img src="logo.jpg" alt="Logo Agora">
      </div>
    </div>

    <div class="navigation">
      <a href="accueil.php"><button>Accueil</button></a>
      <a href="parcourir.html"><button>Tout Parcourir</button></a>
      <a href="notifications.php"><button>Notifications</button></a>
      <a href="panier.html"><button>Panier</button></a>
      <a href="compte.php"><button>Votre Compte</button></a>
    </div>

    <h2>Bienvenue <?= htmlspecialchars($utilisateur['prenom']) ?> (<?= htmlspecialchars($type) ?>)</h2>

    <?php if ($type === 'admin' || $type === 'vendeur'): ?>
      <!-- Section Offres pour admin/vendeur -->
      <h3>Gestion des offres</h3>
      
      <!-- Enchères en cours -->
      <h4>Enchères en cours</h4>
      <?php
      $stmt = $pdo->prepare("SELECT e.id, e.montant_max, a.nom, a.id as article_id, u.prenom as acheteur 
                            FROM encheres e 
                            JOIN articles a ON e.article_id = a.id 
                            JOIN utilisateurs u ON e.acheteur_id = u.id 
                            WHERE a.vendeur_id = ? AND a.vendu = FALSE
                            ORDER BY e.montant_max DESC");
      $stmt->execute([$utilisateur['id']]);
      $encheres = $stmt->fetchAll();
      
      if (count($encheres)) {
          foreach ($encheres as $enchere) {
              echo '<div class="offer-container">
                      <strong>'.$enchere['nom'].'</strong><br>
                      Meilleure offre: '.$enchere['montant_max'].'€ par '.$enchere['acheteur'].'<br>
                      <div class="offer-actions">
                        <form method="POST">
                          <input type="hidden" name="offre_id" value="'.$enchere['id'].'">
                          <input type="hidden" name="article_id" value="'.$enchere['article_id'].'">
                          <button type="submit" name="action_offre" value="accepter">Accepter</button>
                          <button type="submit" name="action_offre" value="refuser">Refuser</button>
                        </form>
                      </div>
                    </div>';
          }
      } else {
          echo '<p>Aucune enchère en cours</p>';
      }
      ?>
      
      <!-- Négociations en cours -->
      <h4>Négociations en cours</h4>
      <?php
      $stmt = $pdo->prepare("SELECT n.id, n.offre, a.nom, a.id as article_id, u.prenom as acheteur 
                            FROM negociations n 
                            JOIN articles a ON n.article_id = a.id 
                            JOIN utilisateurs u ON n.acheteur_id = u.id 
                            WHERE a.vendeur_id = ? AND a.vendu = FALSE
                            ORDER BY n.date DESC");
      $stmt->execute([$utilisateur['id']]);
      $negociations = $stmt->fetchAll();
      
      if (count($negociations)) {
          foreach ($negociations as $negociation) {
              echo '<div class="offer-container">
                      <strong>'.$negociation['nom'].'</strong><br>
                      Offre: '.$negociation['offre'].'€ par '.$negociation['acheteur'].'<br>
                      <div class="offer-actions">
                        <form method="POST">
                          <input type="hidden" name="offre_id" value="'.$negociation['id'].'">
                          <input type="hidden" name="article_id" value="'.$negociation['article_id'].'">
                          <button type="submit" name="action_offre" value="accepter">Accepter</button>
                          <button type="submit" name="action_offre" value="refuser">Refuser</button>
                        </form>
                      </div>
                    </div>';
          }
      } else {
          echo '<p>Aucune négociation en cours</p>';
      }
      ?>
      
      <!-- Achats immédiats en attente -->
      <h4>Achats immédiats en attente</h4>
      <?php
      $stmt = $pdo->prepare("SELECT p.id, a.nom, a.prix, a.id as article_id, u.prenom as acheteur 
                            FROM paniers p 
                            JOIN articles a ON p.article_id = a.id 
                            JOIN utilisateurs u ON p.acheteur_id = u.id 
                            WHERE a.vendeur_id = ? AND p.statut = 'en_attente' AND a.type_vente = 'achat_immediat'
                            ORDER BY p.date_ajout DESC");
      $stmt->execute([$utilisateur['id']]);
      $achats = $stmt->fetchAll();
      
      if (count($achats)) {
          foreach ($achats as $achat) {
              echo '<div class="offer-container">
                      <strong>'.$achat['nom'].'</strong><br>
                      Prix: '.$achat['prix'].'€ par '.$achat['acheteur'].'<br>
                      <div class="offer-actions">
                        <form method="POST">
                          <input type="hidden" name="offre_id" value="'.$achat['id'].'">
                          <input type="hidden" name="article_id" value="'.$achat['article_id'].'">
                          <button type="submit" name="action_offre" value="accepter">Confirmer la vente</button>
                        </form>
                      </div>
                    </div>';
          }
      } else {
          echo '<p>Aucun achat immédiat en attente</p>';
      }
      ?>
      
      <!-- Historique des ventes -->
      <h3>Historique des ventes</h3>
      <?php
$stmt = $pdo->prepare("
SELECT 
    a.nom, 
    a.prix, 
    a.type_vente, 
    COALESCE(u1.prenom, u2.prenom, u3.prenom) AS acheteur, 
    a.date_ajout 
FROM articles a 
LEFT JOIN encheres e ON a.id = e.article_id 
LEFT JOIN utilisateurs u1 ON e.acheteur_id = u1.id 
LEFT JOIN negociations n ON a.id = n.article_id 
LEFT JOIN utilisateurs u2 ON n.acheteur_id = u2.id 
LEFT JOIN paniers p ON a.id = p.article_id 
LEFT JOIN utilisateurs u3 ON p.acheteur_id = u3.id 
WHERE a.vendeur_id = ? AND a.vendu = TRUE
GROUP BY a.id
ORDER BY a.date_ajout DESC
");
$stmt->execute([$utilisateur['id']]);

      $historique = $stmt->fetchAll();
      
      if (count($historique)) {
          foreach ($historique as $item) {
              echo '<div class="history-item">
                      <strong>'.$item['nom'].'</strong><br>
                      Prix: '.$item['prix'].'€ - Type: '.$item['type_vente'].'<br>
                      Acheteur: '.$item['acheteur'].' <br>
                      Date: '.$item['date_ajout'].'
                    </div>';
          }
      } else {
          echo '<p>Aucune vente dans l\'historique</p>';
      }
      ?>
      
      <!-- Formulaire d'ajout d'article -->
      <h3>Ajouter un article</h3>
      <form method="POST">
        <input type="text" name="nom_article" placeholder="Nom" required>
        <textarea name="description" placeholder="Description" required></textarea>
        <select name="type_categorie">
          <option>nourriture</option>
          <option>vetements</option>
          <option>art</option>
          <option>armes</option>
          <option>mobilier</option>
          <option>autres</option>
          <option>reliques</option>
        </select>
        <select name="type_article">
          <option>rare</option>
          <option>haut_de_gamme</option>
          <option>regulier</option>
        </select>
        <select name="type_vente">
          <option value="achat_immediat">Achat immédiat</option>
          <option value="negociation">Transaction</option>
          <option value="enchere">Enchère</option>
        </select>
        <input type="number" name="prix" placeholder="Prix (€)" step="0.01" required>
        <input type="text" name="image_principale" placeholder="Lien de la photo (ex: images/produit.jpg)">
        <input type="text" name="video_url" placeholder="Lien de la vidéo (YouTube, etc.)">
        <button name="ajouter_article">Publier</button>
      </form>
      
      <?php if ($type === 'admin'): ?>
        <!-- Formulaires admin -->
        <h3>Ajouter un vendeur</h3>
        <form method="POST">
          <input type="text" name="nom_vendeur" placeholder="Nom" required>
          <input type="text" name="prenom_vendeur" placeholder="Prénom" required>
          <input type="email" name="email_vendeur" placeholder="Email" required>
          <input type="password" name="mot_de_passe" placeholder="Mot de passe" required>
          <button name="ajouter_vendeur">Ajouter</button>
        </form>

        <h3>Supprimer un vendeur</h3>
        <form method="POST">
          <input type="email" name="email_suppression" placeholder="Email vendeur">
          <button name="supprimer_vendeur">Supprimer</button>
        </form>
      <?php endif; ?>
      
    <?php elseif ($type === 'acheteur'): ?>
      <!-- Section Panier pour acheteur -->
      <h3>Votre Panier</h3>
      <?php
      $stmt = $pdo->prepare("SELECT a.nom, a.prix, a.description FROM paniers p JOIN articles a ON p.article_id = a.id WHERE p.acheteur_id = ?");
      $stmt->execute([$utilisateur['id']]);
      $total = 0;
      while ($item = $stmt->fetch()) {
          echo "<div>{$item['nom']} - {$item['description']} - {$item['prix']} €</div>";
          $total += $item['prix'];
      }
      echo "<strong>Total : {$total} €</strong><br><button>Passer à la commande</button>";
      ?>
    <?php endif; ?>

    <div class="footer">
      <small>agoriafrancia@ece.fr | &copy; 2025 Agoria Francia | +33 06 30 44 46 50</small>
    </div>
  </div>
</body>
</html>