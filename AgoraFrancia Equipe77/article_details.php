<?php
session_start();
require_once('db.php');

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
@import url('https://fonts.googleapis.com/css2?family=EB+Garamond&family=Cinzel:wght@600&display=swap');

body {
  font-family: 'EB Garamond', serif;
  background-color: #fdfaf4;
  background-image: url('images/grece.jpg');
  background-size: cover;
  background-position: center;
  background-repeat: no-repeat;
  background-attachment: fixed;
  color: #3a2f0b;
  margin: 20px;
}

.wrapper {
  border: 10px double #a87e41;
  padding: 20px;
  max-width: 1000px;
  margin: auto;
  border-radius: 16px;
  background: rgba(255, 255, 255, 0.95);
  box-shadow: 0 10px 30px rgba(0,0,0,0.15);
  animation: fadeIn 1s ease;
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
  font-family: 'Cinzel', serif;
  color: #2f4a6d;
  font-size: 42px;
  letter-spacing: 3px;
  margin: 0;
  text-transform: uppercase;
}

.logo img {
  height: 70px;
  border: 2px solid #a87e41;
  padding: 5px;
  background-color: #fff;
  border-radius: 10px;
}

.navigation {
  display: flex;
  justify-content: space-around;
  background-color: #f7efe3;
  border: 3px solid #a87e41;
  padding: 12px;
  margin: 20px 0;
  border-radius: 10px;
  box-shadow: 0 4px 10px rgba(168, 126, 65, 0.3);
}

.navigation button {
  background-color: #e5d4a1;
  border: 2px solid #a87e41;
  border-radius: 6px;
  padding: 10px 18px;
  font-size: 16px;
  font-weight: bold;
  color: #3a2f0b;
  cursor: pointer;
  transition: background 0.3s, box-shadow 0.3s, transform 0.2s;
  box-shadow: 0 4px 6px rgba(168, 126, 65, 0.4);
}

.navigation button:hover {
  background-color: #cfa95e;
  box-shadow: 0 6px 12px rgba(168, 126, 65, 0.5);
  transform: translateY(-3px);
}

.footer {
  text-align: center;
  padding: 15px;
  margin-top: 20px;
  border-top: 4px solid #a87e41;
  color: #3a2f0b;
  font-weight: bold;
  font-size: 16px;
}

a.back-button {
  display: inline-block;
  margin-top: 20px;
  padding: 10px 20px;
  background-color: #8b0000;
  color: white;
  border: 2px solid #a87e41;
  border-radius: 6px;
  font-weight: bold;
  text-decoration: none;
  transition: background 0.3s;
}

a.back-button:hover {
  background-color: #6b0000;
}

@keyframes fadeIn {
  from { opacity: 0; transform: translateY(10px); }
  to { opacity: 1; transform: translateY(0); }
}

h2, h3 {
  font-family: 'Cinzel', serif;
  color: #2f4a6d;
}

.article-details img {
  max-width: 40%; 
  height: auto;
  opacity: 1;
  display: block;
  margin: 0 auto 20px auto;
  border-radius: 8px;
  box-shadow: 0 2px 8px rgba(0,0,0,0.1);
}

.article-details {
  opacity: 1 !important;
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
