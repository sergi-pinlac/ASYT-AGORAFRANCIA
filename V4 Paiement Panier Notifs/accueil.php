<?php
// Connexion à la base de données
try {
    $pdo = new PDO('mysql:host=localhost;dbname=agora_francia;charset=utf8', 'root', '');
} catch (PDOException $e) {
    die("Erreur de connexion à la base de données : " . $e->getMessage());
}

// Récupération de 3 articles au hasard pour la sélection du jour
$requeteJour = $pdo->query("SELECT id, nom, prix, image_principale, type_vente FROM articles WHERE vendu = 0 ORDER BY RAND() LIMIT 3");
$articlesJour = $requeteJour->fetchAll(PDO::FETCH_ASSOC);

// Récupération de 5 articles pour les ventes flash
$requeteFlash = $pdo->query("SELECT id, nom, prix, image_principale, type_vente FROM articles WHERE vendu = 0 ORDER BY RAND() LIMIT 5");
$articlesFlash = $requeteFlash->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <title>Agora Francia</title>
  <style>
    @import url('https://fonts.googleapis.com/css2?family=EB+Garamond&display=swap');

    body {
      font-family: 'EB Garamond', serif;
      background-color: #fdfaf4;
      background-image: url('https://www.transparenttextures.com/patterns/paper-fibers.png');
      color: #3a2f0b;
      margin: 20px;
    }

    .wrapper {
      border: 10px double #a87e41;
      padding: 20px;
      max-width: 900px;
      margin: auto;
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

    .section {
      border: 4px double #a87e41;
      background-color: #fffaf0;
      padding: 20px;
      margin-top: 20px;
    }

    .welcome-message {
      text-align: center;
      margin-bottom: 30px;
    }

    .welcome-message h2 {
      color: #2f4a6d;
      font-size: 28px;
      margin-bottom: 15px;
    }

    .welcome-message p {
      font-size: 16px;
      line-height: 1.6;
    }

    .section-title {
      color: #8b0000;
      font-size: 22px;
      border-bottom: 2px solid #a87e41;
      padding-bottom: 5px;
      margin-bottom: 15px;
      text-align: center;
    }

    .carousel {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
      gap: 20px;
      padding: 10px 0;
    }

    .flash-carousel {
        display: flex;
        gap: 15px;
        overflow-x: auto;
        overflow-y: hidden; /* ✅ empêche les débordements vers le haut ou le bas */
        padding-bottom: 10px;
        scroll-behavior: smooth;
        scrollbar-width: thin;
        scrollbar-color: #a87e41 #f7efe3;
        align-items: flex-start;
    }

    .flash-carousel::-webkit-scrollbar {
      height: 8px;
    }

    .flash-carousel::-webkit-scrollbar-track {
      background: #f7efe3;
    }

    .flash-carousel::-webkit-scrollbar-thumb {
      background-color: #a87e41;
      border-radius: 4px;
    }

    .carousel-item {
        min-width: 200px;
        border: 2px solid #a87e41;
        padding: 10px;
        background-color: #fdfaf4;
        background-image: url('https://www.transparenttextures.com/patterns/paper-fibers.png');
        text-align: center;
        border-radius: 10px;
        box-shadow: 0 0 10px rgba(0,0,0,0.05);
        transition: transform 0.2s ease, box-shadow 0.2s ease;
        margin-top: 5px;
    }

    .carousel-item:hover {
        transform: translateY(-5px);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
    }

    .carousel-item img {
        width: 100%;
        height: 120px;
        object-fit: contain;
        margin-bottom: 10px;
        background-color: #f7efe3; 
        padding: 5px; 
    }

    .carousel-item h3 {
      font-size: 16px;
      margin: 5px 0;
      color: #2f4a6d;
    }

    .carousel-item .price {
      font-weight: bold;
      color: #8b0000;
    }

    .type-badge {
      display: inline-block;
      padding: 2px 6px;
      border-radius: 10px;
      font-size: 10px;
      font-weight: bold;
      margin-top: 5px;
      text-transform: uppercase;
    }

    .type-immediate {
      background-color: #2f4a6d;
      color: white;
    }

    .type-negotiation {
      background-color: #a87e41;
      color: white;
    }

    .type-auction {
      background-color: #8b0000;
      color: white;
    }

    .empty-carousel {
      text-align: center;
      padding: 40px 0;
      color: #777;
      font-style: italic;
      width: 100%;
    }

    .add-item-btn {
      display: block;
      width: 200px;
      margin: 20px auto;
      padding: 12px;
      background-color: #8b0000;
      color: white;
      border: none;
      border-radius: 5px;
      font-size: 16px;
      text-align: center;
      text-decoration: none;
      transition: background 0.3s;
    }

    .add-item-btn:hover {
      background-color: #6b0000;
    }

    .contact-info {
        margin-top: 30px;
        text-align: center;
    }

    .contact-info h3 {
        color: #2f4a6d;
        font-size: 20px;
        margin-bottom: 10px;
    }

    .contact-info p {
        margin: 5px 0;
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
    /* Ajout d'une animation subtile sur les boutons de navigation */
.navigation button {
  box-shadow: 0 4px 6px rgba(168, 126, 65, 0.4);
  transition: background 0.3s, box-shadow 0.3s, transform 0.2s;
}

.navigation button:hover {
  background-color: #cfa95e;
  box-shadow: 0 6px 10px rgba(168, 126, 65, 0.7);
  transform: translateY(-3px);
}

/* Effet hover plus doux sur les images des articles */
.carousel-item img {
  transition: transform 0.3s ease, filter 0.3s ease;
  border-radius: 8px;
}

.carousel-item img:hover {
  transform: scale(1.05);
  filter: brightness(1.1);
}

/* Ajout d’une ombre portée légère sur le wrapper pour plus de profondeur */
.wrapper {
  box-shadow: 0 10px 30px rgba(0,0,0,0.15);
  border-radius: 15px;
}

/* Style amélioré pour les badges de type de vente */
.type-badge {
  padding: 4px 10px;
  font-size: 12px;
  letter-spacing: 1px;
  box-shadow: 0 2px 6px rgba(0,0,0,0.15);
  user-select: none;
}

/* Scrollbar personnalisée pour le carousel flash sur Chrome et Firefox */
.flash-carousel::-webkit-scrollbar {
  height: 10px;
  border-radius: 5px;
}

.flash-carousel::-webkit-scrollbar-thumb {
  background-color: #8b5e1c;
  border-radius: 5px;
  border: 2px solid #f7efe3;
}

/* Ajout d’un léger effet de survol sur les liens des articles */
.carousel-item a {
  color: inherit;
  text-decoration: none;
  display: block;
  transition: color 0.3s;
}

.carousel-item a:hover {
  color: #8b0000;
}

/* Style pour la carte Google Maps */
.map-container {
  width: 100%;
  height: 250px;
  margin-top: 15px;
  border: 4px solid #a87e41;
  border-radius: 10px;
  overflow: hidden;
  box-shadow: 0 4px 15px rgba(0,0,0,0.1);
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
      <div class="welcome-message">
        <h2>Bienvenue à Agora Francia</h2>
        <p>Inspiré des marchés grecs antiques, Agora Francia vous propose une expérience unique de commerce en ligne où vous pouvez acheter immédiatement, négocier avec les vendeurs ou participer à des enchères pour des objets rares.</p>
      </div>


      <!-- Sélection du jour -->
      <div class="daily-selection">
        <h3 class="section-title">Sélection du Jour</h3>
        <div class="carousel">
          <?php if (empty($articlesJour)): ?>
            <div class="empty-carousel">Aucun article disponible pour le moment</div>
          <?php else: ?>
            <?php foreach ($articlesJour as $article): ?>
              <a href="article_details2.php?id=<?= $article['id'] ?>" style="text-decoration: none; color: inherit;">
              <div class="carousel-item">
                <img src="<?= htmlspecialchars($article['image_principale']) ?>" alt="<?= htmlspecialchars($article['nom']) ?>">
                <h3><?= htmlspecialchars($article['nom']) ?></h3>
                <div class="price"><?= number_format($article['prix'], 2, ',', ' ') ?> €</div>
                <div class="type-badge <?= [
                  'achat_immediat' => 'type-immediate',
                  'negociation' => 'type-negotiation',
                  'enchere' => 'type-auction'
                ][$article['type_vente']] ?>">
                  <?= [
                    'achat_immediat' => 'Immédiat',
                    'negociation' => 'Négociation',
                    'enchere' => 'Enchère'
                  ][$article['type_vente']] ?>
                </div>
              </div>
            <?php endforeach; ?>
          <?php endif; ?>
        </div>
      </div>

      <!-- Ventes flash -->
      <div class="flash-sales" style="margin-top: 30px;">
        <h3 class="section-title">Ventes Flash - Best-sellers de la semaine</h3>
        <div class="flash-carousel">
          <?php if (empty($articlesFlash)): ?>
            <div class="empty-carousel">Aucun article disponible pour le moment</div>
          <?php else: ?>
            <?php foreach ($articlesFlash as $article): ?>
              <a href="article_details.php?id=<?= $article['id'] ?>" style="text-decoration: none; color: inherit;">
              <div class="carousel-item">
                <img src="<?= htmlspecialchars($article['image_principale']) ?>" alt="<?= htmlspecialchars($article['nom']) ?>">
                <h3><?= htmlspecialchars($article['nom']) ?></h3>
                <div class="price"><?= number_format($article['prix'], 2, ',', ' ') ?> €</div>
                <div class="type-badge <?= [
                  'achat_immediat' => 'type-immediate',
                  'negociation' => 'type-negotiation',
                  'enchere' => 'type-auction'
                ][$article['type_vente']] ?>">
                  <?= [
                    'achat_immediat' => 'Immédiat',
                    'negociation' => 'Négociation',
                    'enchere' => 'Enchère'
                  ][$article['type_vente']] ?>
                </div>
              </div>
            <?php endforeach; ?>
          <?php endif; ?>
        </div>
      </div>

     <div class="contact-info">
        <h3>Contactez Agora Francia</h3>
        <p>Email: agorafrancia@ece.fr</p>
        <p>Téléphone: +33 6 30 44 46 50</p>
        <p>Adresse: 12 Rue d'Athènes, 75009 Paris</p>
        <div class="map-container">
          <iframe
            src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d2624.660938062215!2d2.326909615674266!3d48.84356907928612!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x47e66e36e1f6b74f%3A0x7a20f31d84dc7f02!2s12%20Rue%20d&#39;Ath%C3%A8nes%2C%2075009%20Paris!5e0!3m2!1sfr!2sfr!4v1716981800000!5m2!1sfr!2sfr"
            width="100%"
            height="100%"
            style="border:0;"
            allowfullscreen=""
            loading="lazy"
            referrerpolicy="no-referrer-when-downgrade">
          </iframe>
        </div>
      </div>
    </div>

    <div class="footer">
      <small>agoriafrancia@ece.fr | Copyright &copy; 2025 Agora Francia | +33 6 30 44 46 50</small>
    </div>

  </div>

</body>
</html>
