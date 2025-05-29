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

        // Affichage HTML directement (sans echo géant)
        ?>
        <!DOCTYPE html>
        <html lang="fr">
        <head>
            <meta charset="UTF-8">
            <title>Inscription réussie</title>
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
                    box-shadow: 2px 2px 5px rgba(0,0,0,0.1);
                    transition: background 0.3s;
                }
                .navigation button:hover {
                    background-color: #d1b97b;
                }
                .message {
                    margin-top: 30px;
                    font-size: 20px;
                    font-weight: bold;
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
                .countdown {
                    margin-top: 15px;
                }
                .accueil-retour {
                    text-align: center;
                    margin-top: 20px;
                }

                .retour-bouton {
                    display: inline-block;
                    background-color: #e5d4a1;
                    border: 3px double #a87e41;
                    border-radius: 8px;
                    padding: 10px 20px;
                    text-decoration: none;
                    color: #3a2f0b;
                    font-family: 'EB Garamond', serif;
                    font-size: 18px;
                    font-weight: bold;
                    box-shadow: 2px 2px 6px rgba(0,0,0,0.15);
                    transition: background-color 0.3s, transform 0.2s;
                }

                .retour-bouton:hover {
                    background-color: #d1b97b;
                    transform: translateY(-2px);
                }

            </style>
            <script>
                let seconds = 5;
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
                <a href="notifications.php"><button>Notifications</button></a>
                <a href="panier.html"><button>Panier</button></a>
                <a href="compte.php"><button>Votre Compte</button></a>
            </div>

            <div class="message">
                ✅ <strong>Votre compte a bien été créé et vous êtes maintenant connecté !</strong><br>
                <div class="countdown" id="countdown"></div>
                <div class="accueil-retour">
                    <a href="accueil.html" class="retour-bouton">⟵ Revenir à l'accueil</a>
                </div>
            </div>

            <div class="footer">
                <small>agoriafrancia@ece.fr | Copyright &copy; 2025 Agora Francia | +33 06 30 44 46 50</small>
            </div>
        </div>
        </body>
        </html>
        <?php
        exit;
    } else {
        echo "<p style='color:red; font-weight:bold;'>❌ Une erreur est survenue lors de la création du compte.</p>";
        exit;
    }
}
?>
