<?php
session_start();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <title>Accueil - Agora Francia</title>
  <style>
    body {
      font-family: 'EB Garamond', serif;
      background-color: #fdfaf4;
      color: #3a2f0b;
      text-align: center;
      padding: 50px;
    }
    .welcome {
      margin-top: 20px;
      font-size: 20px;
      color: #2f4a6d;
    }
    .btn {
      margin-top: 30px;
      display: inline-block;
      padding: 10px 20px;
      background-color: #a87e41;
      color: white;
      text-decoration: none;
      border-radius: 5px;
    }
    .btn:hover {
      background-color: #8a6633;
    }
  </style>
</head>
<body>
  <h1>Bienvenue sur Agora Francia</h1>

  <?php if (isset($_SESSION['utilisateur'])): ?>
    <div class="welcome">
      👤 Connecté en tant que <strong><?= htmlspecialchars($_SESSION['utilisateur']['prenom']) ?> <?= htmlspecialchars($_SESSION['utilisateur']['nom']) ?></strong><br>
      Type de compte : <?= htmlspecialchars($_SESSION['utilisateur']['type']) ?><br>
      <a class="btn" href="compte.php">Voir mon compte</a>
    </div>
  <?php else: ?>
    <div class="welcome">
      Vous n'êtes pas connecté.<br>
      <a class="btn" href="connexion.html">Se connecter</a> ou 
      <a class="btn" href="inscription.html">Créer un compte</a>
    </div>
  <?php endif; ?>
</body>
</html>
