<?php
session_start();
require_once('db.php');

// Vérifie si l'ID est bien passé en GET
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die("❌ Identifiant d'article invalide.");
}

$articleId = intval($_GET['id']);

try {
    $stmt = $pdo->prepare("SELECT * FROM articles WHERE id = ?");
    $stmt->execute([$articleId]);
    $article = $stmt->fetch(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("❌ Erreur lors de la récupération de l'article.");
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <title><?= $article ? htmlspecialchars($article['nom']) : 'Article introuvable' ?> - Agora Francia</title>
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
      border: 10px double #a87e41;
      padding: 20px;
      max-width: 1000px;
      margin: auto;
      background-size: cover;
      box-shadow: 0 0 20px rgba(0,0,0,0.3);
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
      transition: background 0.3s;
    }

    .navigation button:hover {
      background-color: #d1b97b;
    }

    .article-details img {
      max-width: 300px;
      height: auto;
      border: 2px solid #a87e41;
      margin-bottom: 15px;
    }

    .article-details h2 {
      font-size: 28px;
      color: #2f4a6d;
      margin-bottom: 10px;
    }

    .article-details p {
      margin-bottom: 8px;
      font-size: 18px;
    }

    .back-button {
      margin-top: 20px;
      display: inline-block;
      background-color: #e5d4a1;
      padding: 10px 20px;
      border: 2px solid #a87e41;
      border-radius: 5px;
      text-decoration: none;
      color: #3a2f0b;
      font-weight: bold;
    }

    .back-button:hover {
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
      <a href="parcourir.php"><button>Tout Parcourir</button></a>
      <a href="notifications.php"><button>Notifications</button></a>
      <a href="panier.html"><button>Panier</button></a>
      <a href="compte.php"><button>Votre Compte</button></a>
    </div>

    <div class="section">
      <?php if ($article): ?>
        <div class="article-details">
          <img src="<?= htmlspecialchars($article['image_principale']) ?>" alt="<?= htmlspecialchars($article['nom']) ?>">
          <h2><?= htmlspecialchars($article['nom']) ?></h2>
          <p><strong>Catégorie :</strong> <?= htmlspecialchars($article['type_categorie']) ?></p>
          <p><strong>Type :</strong> <?= htmlspecialchars($article['type_article']) ?></p>
          <p><strong>Type d'achat :</strong> <?= htmlspecialchars($article['type_vente']) ?></p>
          <p><strong>Prix :</strong> <?= htmlspecialchars($article['prix']) ?> €</p>
          <p><strong>Description :</strong> <?= nl2br(htmlspecialchars($article['description'])) ?></p>
          <a href="notifications.php" class="back-button">← Retour</a>
        </div>
      <?php else: ?>
        <p>❌ Article introuvable.</p>
        <a href="parcourir.php" class="back-button">← Retour</a>
      <?php endif; ?>
    </div>

    <div class="footer">
      <small>agoriafrancia@ece.fr | Copyright &copy; 2025 Agora Francia | +33 06 30 44 46 50</small>
    </div>
  </div>

</body>
</html>
