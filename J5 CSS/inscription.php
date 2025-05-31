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

/* ===== Wrapper ===== */
.wrapper {
  border: 10px double #a87e41;
  padding: 20px;
  max-width: 900px;
  margin: auto;
  border-radius: 16px;
  background: rgba(255, 255, 255, 0.95);
  box-shadow: 0 10px 30px rgba(0,0,0,0.15);
}

/* ===== Header ===== */
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

/* ===== Navigation ===== */
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

/* ===== Form Styles ===== */
.form-container {
  margin-top: 30px;
  padding: 20px;
  background-color: #fffaf0;
  border: 4px double #a87e41;
  border-radius: 10px;
  box-shadow: 0 4px 15px rgba(0,0,0,0.1);
}

.form-title {
  font-family: 'Cinzel', serif;
  color: #2f4a6d;
  font-size: 24px;
  text-align: center;
  margin-bottom: 20px;
  border-bottom: 2px solid #a87e41;
  padding-bottom: 10px;
}

.form-group {
  margin-bottom: 20px;
}

.form-group label {
  display: block;
  margin-bottom: 8px;
  font-weight: bold;
  font-size: 16px;
}

.form-group input, 
.form-group select {
  width: 100%;
  padding: 10px;
  border: 2px solid #a87e41;
  border-radius: 6px;
  background-color: #fdfaf4;
  font-family: 'EB Garamond', serif;
  font-size: 16px;
  transition: border 0.3s, box-shadow 0.3s;
}

.form-group input:focus, 
.form-group select:focus {
  border-color: #8b0000;
  box-shadow: 0 0 8px rgba(139, 0, 0, 0.3);
  outline: none;
}

.submit-btn {
  background-color: #8b0000;
  color: white;
  border: none;
  border-radius: 6px;
  padding: 12px 20px;
  font-size: 16px;
  font-weight: bold;
  cursor: pointer;
  width: 100%;
  transition: background 0.3s, transform 0.2s;
  margin-top: 10px;
}

.submit-btn:hover {
  background-color: #6b0000;
  transform: translateY(-2px);
}

.login-link {
  text-align: center;
  margin-top: 20px;
  font-size: 16px;
}

.login-link a {
  color: #2f4a6d;
  font-weight: bold;
  text-decoration: none;
  transition: color 0.3s;
}

.login-link a:hover {
  color: #8b0000;
  text-decoration: underline;
}

.error-message {
  color: #8b0000;
  font-weight: bold;
  text-align: center;
  margin: 15px 0;
  padding: 10px;
  background-color: rgba(139, 0, 0, 0.1);
  border-radius: 6px;
}

.success-message {
  color: #2f4a6d;
  font-weight: bold;
  text-align: center;
  margin: 15px 0;
  padding: 10px;
  background-color: rgba(47, 74, 109, 0.1);
  border-radius: 6px;
}

/* ===== Footer ===== */
.footer {
  text-align: center;
  padding: 15px;
  margin-top: 30px;
  border-top: 4px solid #a87e41;
  color: #3a2f0b;
  font-weight: bold;
  font-size: 16px;
}

/* ===== Animation ===== */
@keyframes fadeIn {
  from { opacity: 0; transform: translateY(10px); }
  to { opacity: 1; transform: translateY(0); }
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
                <a href="accueil.php"><button>Accueil</button></a>
                <a href="parcourir.html"><button>Tout Parcourir</button></a>
                <a href="notifications.php"><button>Notifications</button></a>
                <a href="panier.html"><button>Panier</button></a>
                <a href="compte.php"><button>Votre Compte</button></a>
            </div>

            <div class="message">
                ✅ <strong>Votre compte a bien été créé et vous êtes maintenant connecté !</strong><br>
                <div class="countdown" id="countdown"></div>
                <div class="accueil-retour">
                    <a href="accueil.php" class="retour-bouton">⟵ Revenir à l'accueil</a>
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
