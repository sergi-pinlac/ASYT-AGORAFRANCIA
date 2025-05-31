<?php
session_start();
?>

<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8">
  <title>Agora Francia - Compte</title>
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
      animation: fadeIn 1s ease;
    }

    .wrapper {
      border: 10px double #a87e41;
      padding: 20px;
      max-width: 900px;
      margin: auto;
      background: rgba(255, 250, 244, 0.95);
      border-radius: 16px;
      box-shadow: 0 10px 30px rgba(0,0,0,0.15);
    }

    /* Header */
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
      font-size: 40px;
      letter-spacing: 3px;
      margin: 0;
      color: #2f4a6d;
      text-transform: uppercase;
    }
    .logo img {
      height: 70px;
      border: 2px solid #a87e41;
      padding: 5px;
      background-color: #fff;
      border-radius: 10px;
    }

    /* Navigation */
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
      transition: background-color 0.3s, box-shadow 0.3s, transform 0.3s;
      box-shadow: 0 4px 6px rgba(168, 126, 65, 0.4);
    }
    .navigation button:hover {
      background-color: #cfa95e;
      box-shadow: 0 6px 12px rgba(168, 126, 65, 0.5);
      transform: translateY(-4px);
    }

    /* Form container */
    .form-container {
      margin-top: 30px;
      background-color: #fffaf0;
      padding: 25px;
      border: 2px solid #a87e41;
      border-radius: 10px;
      box-shadow: 0 2px 10px rgba(0,0,0,0.1);
      max-width: 500px;
      margin-left: auto;
      margin-right: auto;
    }
    form {
      display: flex;
      flex-direction: column;
      gap: 20px;
    }
    label {
      font-weight: bold;
      color: #3a2f0b;
    }
    input, select {
      padding: 10px;
      font-family: 'EB Garamond', serif;
      font-size: 16px;
      border: 1px solid #ccc;
      border-radius: 6px;
      color: #3a2f0b;
      transition: border-color 0.3s;
    }
    input:focus, select:focus {
      border-color: #a87e41;
      outline: none;
    }
    button[type="submit"] {
      background-color: #e5d4a1;
      color: #3a2f0b;
      border: 2px solid #a87e41;
      border-radius: 6px;
      padding: 12px;
      font-weight: bold;
      font-size: 16px;
      cursor: pointer;
      transition: background-color 0.3s, box-shadow 0.3s;
      box-shadow: 0 4px 8px rgba(168, 126, 65, 0.5);
      font-family: 'EB Garamond', serif;
    }
    button[type="submit"]:hover {
      background-color: #cfa95e;
      box-shadow: 0 6px 14px rgba(168, 126, 65, 0.7);
    }

    /* Footer */
    .footer {
      text-align: center;
      padding: 15px;
      margin-top: 40px;
      border-top: 4px solid #a87e41;
      color: #3a2f0b;
      font-weight: bold;
      font-size: 16px;
      font-family: 'EB Garamond', serif;
    }

    /* Message */
    .message {
      text-align: center;
      font-size: 18px;
      margin: 30px 0;
      color: #2f4a6d;
      font-family: 'Cinzel', serif;
    }

    .actions-compte {
      display: flex;
      justify-content: center;
      gap: 20px;
      margin-top: 20px;
    }

    .retour-bouton {
      background-color: #e5d4a1;
      border: 2px solid #a87e41;
      border-radius: 8px;
      padding: 12px 25px;
      color: #3a2f0b;
      font-weight: bold;
      font-family: 'EB Garamond', serif;
      font-size: 16px;
      text-decoration: none;
      cursor: pointer;
      box-shadow: 0 4px 8px rgba(168, 126, 65, 0.4);
      display: inline-flex;
      align-items: center;
      justify-content: center;
      transition: background-color 0.3s, box-shadow 0.3s;
    }
    .retour-bouton:hover {
      background-color: #cfa95e;
      box-shadow: 0 6px 12px rgba(168, 126, 65, 0.6);
    }
    @keyframes fadeIn {
  from { opacity: 0; transform: translateY(10px); }
  to { opacity: 1; transform: translateY(0); }
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

    <section class="form-container">
         <?php if (isset($_SESSION['utilisateur'])): ?>
           <h3 style="margin-bottom: 5px;">Bienvenue, <?= htmlspecialchars($_SESSION['utilisateur']['prenom']) ?> <?= htmlspecialchars($_SESSION['utilisateur']['nom']) ?> !</h3>
<p style="margin-top: 0;">Statut : <strong><?= htmlspecialchars($_SESSION['utilisateur']['type']) ?></strong></p>

            <div style="text-align: center;">
  <img src="images/bienvenue.jpg" alt="Photo de profil" style="max-width: 500px; border-radius: 50%; border: 3px solid #a87e41; margin: 1px auto;">
 <p style="font-style: italic; font-weight: bold; font-size: 18px; color: #5a4a2f; margin-top: 10px;">
    "Souriez citoyen d’Athènes, Zeus vous souhaite la bienvenue sur Agora Francia !"
  </p>
  </div>
            <div class="actions-compte">
              <a href="tableau_de_bord.php"><button class="retour-bouton">Accéder au tableau de bord</button></a>
              <a href="logout.php"><button class="retour-bouton">Se déconnecter</button></a>
            </div>
        <?php else: ?>
            <h3>Vous n'êtes pas connecté.</h3>
    
            <h3>Veuillez vous connecter ou créer un compte pour utiliser le site, comme le ferait Webos d’Athenet, le demi-dieu du Wi-Fi !</h3>
            <div class="actions-compte">
                <a href="connexion.html"><button class="retour-bouton">Se connecter</button></a>
                <a href="inscription.html"><button class="retour-bouton">Créer un compte</button></a>
            </div>
         <?php endif; ?>
    </section>

    <div class="footer">
      <small>agoriafrancia@ece.fr | Copyright &copy; 2025 Agoria Francia | +33 06 30 44 46 50</small>
    </div>
  </div>
</body>
</html>
