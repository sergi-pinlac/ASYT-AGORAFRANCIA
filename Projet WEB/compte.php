<?php
session_start();

if (!isset($_SESSION['utilisateur'])) {
    header("Location: accueil.html"); 
    exit;
}

$utilisateur = $_SESSION['utilisateur'];
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Votre Compte - Agora Francia</title>
    <style>
        body {
            font-family: 'EB Garamond', serif;
            background-color: #fdfaf4;
            background-image: url('https://www.transparenttextures.com/patterns/paper-fibers.png');
            color: #3a2f0b;
            padding: 20px;
        }

        .wrapper {
            border: 10px double #a87e41;
            padding: 20px;
            max-width: 800px;
            margin: auto;
            background-color: #fffaf0;
            box-shadow: 0 0 20px rgba(0,0,0,0.2);
        }

        h1 {
            text-align: center;
            color: #2f4a6d;
        }

        .info {
            font-size: 18px;
            margin-top: 30px;
            line-height: 1.6;
        }
    </style>
</head>
<body>
    <div class="wrapper">
        <h1>Bienvenue, <?= htmlspecialchars($utilisateur['prenom']) ?> !</h1>
        <div class="info">
            <p><strong>Nom :</strong> <?= htmlspecialchars($utilisateur['nom']) ?></p>
            <p><strong>Prénom :</strong> <?= htmlspecialchars($utilisateur['prenom']) ?></p>
            <p><strong>Type de compte :</strong> <?= htmlspecialchars($utilisateur['type']) ?></p>
            <a class='accueil-link' href='accueil.html'>⟵ Revenir à l'accueil</a>
        </div>
    </div>
</body>
</html>
