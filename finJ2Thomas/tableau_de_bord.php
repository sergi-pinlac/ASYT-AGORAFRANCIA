<?php
session_start();
$pdo = new PDO('mysql:host=localhost;dbname=agora_francia;charset=utf8', 'root', '');

if (!isset($_SESSION['utilisateur'])) {
    header("Location: connexion.html");
    exit;
}

$utilisateur = $_SESSION['utilisateur'];
$type = $utilisateur['type'];
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
      <a href="accueil.html"><button>Accueil</button></a>
      <a href="parcourir.html"><button>Tout Parcourir</button></a>
      <a href="notifications.html"><button>Notifications</button></a>
      <a href="panier.html"><button>Panier</button></a>
      <a href="compte.php"><button>Votre Compte</button></a>
    </div>

    <h2>Bienvenue <?= htmlspecialchars($utilisateur['prenom']) ?> (<?= htmlspecialchars($type) ?>)</h2>

    <?php
    // ADMIN
    if ($type === 'admin') {
        echo "
        <h3>Ajouter un article</h3>
<form method='POST'>
    <input type='text' name='nom_article' placeholder='Nom' required>
    <textarea name='description' placeholder='Description' required></textarea>
    <select name='type_categorie'>
        <option>nourriture</option>
        <option>vetements</option>
        <option>art</option>
        <option>armes</option>
        <option>mobilier</option>
        <option>autres</option>
        <option>reliques</option>
    </select>
    <select name='type_article'>
        <option>rare</option>
        <option>haut_de_gamme</option>
        <option>regulier</option>
    </select>
    <select name='type_vente'>
        <option value='achat_immediat'>Achat immédiat</option>
        <option value='negociation'>Transaction</option>
        <option value='enchere'>Enchère</option>
    </select>
    <input type='number' name='prix' placeholder='Prix (€)' step='0.01' required>
    <input type='text' name='image_principale' placeholder='Lien de la photo (ex: images/produit.jpg)'>
    <input type='text' name='video_url' placeholder='Lien de la vidéo (YouTube, etc.)'>
    <button name='ajouter_article'>Publier</button>
</form>

        <h3>Ajouter un vendeur</h3>
        <form method='POST'>
            <input type='text' name='nom_vendeur' placeholder='Nom' required>
            <input type='text' name='prenom_vendeur' placeholder='Prénom' required>
            <input type='email' name='email_vendeur' placeholder='Email' required>
            <input type='password' name='mot_de_passe' placeholder='Mot de passe' required>
            <button name='ajouter_vendeur'>Ajouter</button>
        </form>

        <h3>Supprimer un vendeur</h3>
        <form method='POST'>
            <input type='email' name='email_suppression' placeholder='Email vendeur'>
            <button name='supprimer_vendeur'>Supprimer</button>
        </form>
        ";
    }

    // VENDEUR
    if ($type === 'vendeur') {
        echo "
        <h3>Ajouter un article</h3>
<form method='POST'>
    <input type='text' name='nom_article' placeholder='Nom' required>
    <textarea name='description' placeholder='Description' required></textarea>
    <select name='type_categorie'>
        <option>nourriture</option>
        <option>vetements</option>
        <option>art</option>
        <option>armes</option>
        <option>mobilier</option>
        <option>autres</option>
        <option>reliques</option>
    </select>
    <select name='type_article'>
        <option>rare</option>
        <option>haut_de_gamme</option>
        <option>regulier</option>
    </select>
    <select name='type_vente'>
        <option value='achat_immediat'>Achat immédiat</option>
        <option value='negociation'>Transaction</option>
        <option value='enchere'>Enchère</option>
    </select>
    <input type='number' name='prix' placeholder='Prix (€)' step='0.01' required>
    <input type='text' name='image_principale' placeholder='Lien de la photo (ex: images/produit.jpg)'>
    <input type='text' name='video_url' placeholder='Lien de la vidéo (YouTube, etc.)'>
    <button name='ajouter_article'>Publier</button>
</form>
        ";
    }

    // ACHETEUR
    if ($type === 'acheteur') {
        echo "<h3>Panier</h3>";
        $stmt = $pdo->prepare("SELECT a.nom, a.prix, a.description FROM paniers p JOIN articles a ON p.article_id = a.id WHERE p.acheteur_id = ?");
        $stmt->execute([$utilisateur['id']]);
        $total = 0;
        while ($item = $stmt->fetch()) {
            echo "<div>{$item['nom']} - {$item['description']} - {$item['prix']} €</div>";
            $total += $item['prix'];
        }
        echo "<strong>Total : {$total} €</strong><br><button>Passer à la commande</button>";
    }
    ?>

    <div class="footer">
      <small>agoriafrancia@ece.fr | &copy; 2025 Agoria Francia | +33 06 30 44 46 50</small>
    </div>
  </div>
</body>
</html>

<?php
// TRAITEMENTS
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

if (isset($_POST['supprimer_vendeur']) && $type === 'admin') {
    $stmt = $pdo->prepare("DELETE FROM utilisateurs WHERE email = ? AND type_compte = 'vendeur'");
    $stmt->execute([$_POST['email_suppression']]);
    echo "<script>alert('🗑 Vendeur supprimé.');</script>";
}
?>
