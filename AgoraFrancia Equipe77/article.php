<?php
$conn = new PDO('mysql:host=localhost;dbname=agora_francia;charset=utf8', 'root', '');

if (isset($_GET['id'])) {
    $id = intval($_GET['id']);
    $stmt = $conn->prepare("SELECT * FROM articles WHERE id = ?");
    $stmt->execute([$id]);
    $article = $stmt->fetch(PDO::FETCH_ASSOC);
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <title><?= $article ? htmlspecialchars($article['nom']) : 'Article introuvable' ?> - Agora Francia</title>
  <link rel="stylesheet" href="style.css"> 
  <style>
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
          <p><strong>Catégorie :</strong> <?= htmlspecialchars($article['categorie']) ?></p>
          <p><strong>Type :</strong> <?= htmlspecialchars($article['type_article']) ?></p>
          <p><strong>Type d'achat :</strong> <?= htmlspecialchars($article['type_vente']) ?></p>
          <p><strong>Prix :</strong> <?= htmlspecialchars($article['prix']) ?> €</p>
          <p><strong>Description :</strong> <?= htmlspecialchars($article['description']) ?></p>
          <a href="parcourir.php" class="back-button">← Retour</a>
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
