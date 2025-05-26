<?php
session_start();
$pdo = new PDO('mysql:host=localhost;dbname=agora_francia;charset=utf8', 'root', '');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nom = $_POST['nom'];
    $prenom = $_POST['prenom'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_BCRYPT);
    $type = $_POST['type_compte'];

    // Vérifie si l'email existe déjà
    $stmt = $pdo->prepare("SELECT * FROM utilisateurs WHERE email = ?");
    $stmt->execute([$email]);
    if ($stmt->fetch()) {
        echo "<p style='color:red; font-weight:bold;'>❌ Un compte avec cet email existe déjà.</p>";
        exit;
    }

    // Enregistrement
    $stmt = $pdo->prepare("INSERT INTO utilisateurs (nom, prenom, email, mot_de_passe, type_compte) VALUES (?, ?, ?, ?, ?)");
    if ($stmt->execute([$nom, $prenom, $email, $password, $type])) {
        $user_id = $pdo->lastInsertId();
        $_SESSION['utilisateur'] = [
            'id' => $user_id,
            'nom' => $nom,
            'prenom' => $prenom,
            'type' => $type
        ];

        // Affiche un message de confirmation et redirige après 3 secondes
       echo "
<!DOCTYPE html>
<html lang='fr'>
<head>
    <meta charset='UTF-8'>
    <title>Inscription réussie</title>
    <style>
        body {
            font-family: 'EB Garamond', serif;
            background-color: #fdfaf4;
            color: #3a2f0b;
            text-align: center;
            padding-top: 100px;
        }

        .message {
            border: 3px solid #a87e41;
            display: inline-block;
            padding: 30px;
            background-color: #fffaf0;
            border-radius: 10px;
            box-shadow: 2px 2px 10px rgba(0,0,0,0.1);
            font-size: 20px;
        }

        .accueil-link {
            display: block;
            margin-top: 30px;
            font-size: 18px;
            color: #2f4a6d;
            text-decoration: none;
        }

        .accueil-link:hover {
            text-decoration: underline;
        }

        .countdown {
            margin-top: 15px;
            font-size: 16px;
            color: #6a4e20;
        }
    </style>
    <script>
        let seconds = 20;
        function updateCountdown() {
            const countdown = document.getElementById('countdown');
            countdown.textContent = 
                'Redirection vers votre compte dans ' + seconds + ' secondes...';
            if (seconds > 0) {
                seconds--;
                setTimeout(updateCountdown, 1000);
            } else {
                window.location.href = 'compte.php';
            }
        }
        window.onload = updateCountdown;
    </script>
</head>
<body>
    <div class='message'>
        ✅ <strong>Votre compte a bien été créé et vous êtes maintenant connecté !</strong><br>
        <div class='countdown' id='countdown'></div>
        <a class='accueil-link' href='accueil.html'>⟵ Revenir à l'accueil</a>
    </div>
</body>
</html>
";



        exit;
    } else {
        echo "<p style='color:red; font-weight:bold;'>❌ Une erreur est survenue lors de la création du compte.</p>";
        exit;
    }
}
?>
