<?php
session_start();
require_once('db.php');

$notifications = [];
$error = null;
$filter = $_GET['filter'] ?? 'toutes';


// Si l'utilisateur n'est pas connecté, on affiche juste une page vide avec "Aucune notification"
if (!isset($_SESSION['utilisateur']) || !isset($_SESSION['utilisateur']['id'])) {
    // On ne redirige pas, on prépare juste les variables pour afficher "Aucune notification"
} else {
    $userId = $_SESSION['utilisateur']['id'];
    $filter = $_GET['filter'] ?? 'toutes';
    $query = "SELECT * FROM notifications WHERE utilisateur_id = ?";
$search = $_GET['search'] ?? '';

if (!empty($search)) {
    $query .= " AND (titre LIKE ? OR message LIKE ?)";
    $params[] = "%$search%";
    $params[] = "%$search%";
}
  
    $params = [$userId];

    if ($filter === 'non lues') {
        $query .= " AND lue = 0";
    } elseif (in_array($filter, ['enchères', 'négociations', 'alertes'])) {
        $query .= " AND type_notification = ?";
        if ($filter === 'enchères') {
            $params[] = 'enchere';
        } elseif ($filter === 'négociations') {
            $params[] = 'negociation';
        } elseif ($filter === 'alertes') {
            $params[] = 'alerte';
        }
    }

    $query .= " ORDER BY date_creation DESC";

    
    try {
        $stmt = $pdo->prepare($query);
        $stmt->execute($params);
        $notifications = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        $notifications = [];
        $error = "Erreur lors du chargement des notifications";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8">
  <title>Agora Francia - Notifications</title>
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
  max-width: 1000px;
  margin: auto;
  background-size: cover;
  background-position: center;
  background-repeat: no-repeat;
  background-attachment: scroll;
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
  transition: background-color 0.3s, transform 0.3s, box-shadow 0.3s;
}

/* Animation au survol : le bouton se soulève légèrement et le fond grise */
.navigation button:hover {
  background-color: #cfa95e; /* gris clair */
  transform: translateY(-4px);
  box-shadow: 0 6px 12px rgba(0,0,0,0.15);
  transition: background-color 0.3s, transform 0.3s, box-shadow 0.3s;
}
.notifications-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 20px;
}

.notifications-title {
  font-size: 28px;
  font-weight: bold;
}

.search-bar input {
  padding: 8px;
  border-radius: 6px;
  border: 1px solid #ccc;
  width: 250px;
  transition: border-color 0.3s;
}

.search-bar input:focus {
  border-color: #a87e41;
  outline: none;
}

.search-bar button {
  padding: 8px 10px;
  border: none;
  background-color: #a87e41;
  color: white;
  border-radius: 6px;
  margin-left: 5px;
  cursor: pointer;
  transition: background-color 0.3s;
}

.search-bar button:hover {
  background-color: #8c6e32;
}

.filter-tabs {
  display: flex;
  gap: 10px;
  margin-bottom: 20px;
}

.filter-tab {
  background-color: #e5d4a1;
  padding: 8px 12px;
  border-radius: 6px;
  border: 2px solid #a87e41;
  cursor: pointer;
  font-weight: bold;
  transition: background-color 0.3s, border-color 0.3s;
}

.filter-tab:hover:not(.active) {
  background-color: #d1b97b;
}

.filter-tab.active {
  background-color: #d1b97b;
  border-color: #3a2f0b;
}

.notification-item {
  display: flex;
  gap: 15px;
  background-color: #fffaf0;
  padding: 15px;
  border: 2px solid #a87e41;
  border-radius: 10px;
  margin-bottom: 15px;
  box-shadow: 2px 2px 5px rgba(0,0,0,0.05);
}

.notification-item.unread {
  background-color: #fff2cc;
}

.notification-icon {
  font-size: 30px;
  margin-top: 10px;
}

.notification-content {
  flex: 1;
}

.notification-title {
  font-weight: bold;
  font-size: 18px;
  margin-bottom: 5px;
}

.notification-category {
  font-size: 14px;
  margin-left: 10px;
  background-color: #a87e41;
  color: white;
  padding: 2px 6px;
  border-radius: 4px;
}

.notification-time {
  font-style: italic;
  color: #555;
  margin-bottom: 8px;
  font-size: 14px;
}

.notification-message {
  margin-bottom: 10px;
}

.notification-actions button {
  background-color: #e5d4a1;
  border: 2px solid #a87e41;
  border-radius: 6px;
  padding: 6px 12px;
  font-weight: bold;
  color: #3a2f0b;
  cursor: pointer;
  margin-right: 10px;
  transition: background-color 0.3s;
}

.notification-actions button:hover {
  background-color: #d1b97b;
}

.empty-notifications {
  background-color: #fffaf0;
  padding: 30px;
  border: 2px dashed #a87e41;
  text-align: center;
  font-style: italic;
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
      <a href="accueil.php"><button>Accueil</button></a>
      <a href="parcourir.html"><button>Tout Parcourir</button></a>
      <a href="notifications.php"><button>Notifications</button></a>
      <a href="panier.html"><button>Panier</button></a>
      <a href="compte.php"><button>Votre Compte</button></a>
    </div>

    <div class="notifications-header">
      <h2 class="notifications-title">Vos Notifications</h2>
    </div>

  <div class="filter-tabs">
  <div class="filter-tab <?= ($filter === 'toutes') ? 'active' : '' ?>">Toutes</div>
  <div class="filter-tab <?= ($filter === 'non lues') ? 'active' : '' ?>">Non lues</div>
  <div class="filter-tab <?= ($filter === 'enchères') ? 'active' : '' ?>">Enchères</div>
  <div class="filter-tab <?= ($filter === 'négociations') ? 'active' : '' ?>">Négociations</div>
  <div class="filter-tab <?= ($filter === 'alertes') ? 'active' : '' ?>">Alertes</div>
</div>

    <div id="notifications-list">
      <div id="notifications-list">
  <?php if (!isset($_SESSION['utilisateur']) || !isset($_SESSION['utilisateur']['id'])): ?>
    <div class="empty-notifications">
      Vous devez être connecté pour voir vos notifications.
       <a href="compte.php" style="color: #a87e41; text-decoration: underline;">Se connecter</a>
    </div>
  <?php elseif (empty($notifications)): ?>
    <div class="empty-notifications">
      Vous n'avez aucune notification pour le moment.
    </div>
      <?php else: ?>
        <?php foreach ($notifications as $notification): ?>
          <div class="notification-item <?= $notification['lue'] ? '' : 'unread' ?>">
            <div class="notification-icon">
              <?php 
                $icons = [
                  'achat' => '🛒',
                  'negociation' => '🤝',
                  'enchere' => '🏆',
                  'alerte' => '🔔',
                  'livraison' => '✉️',
                  'panier' => '🧺'
                ];
                echo $icons[$notification['type_notification']] ?? 'ℹ️';
              ?>
            </div>
            <div class="notification-content">
              <div class="notification-title">
                <?= htmlspecialchars($notification['titre']) ?>
                <span class="notification-category category-<?= $notification['type_notification'] ?>">
                  <?= ucfirst($notification['type_notification']) ?>
                </span>
              </div>
              <div class="notification-time">
                <?= date('d/m/Y H:i', strtotime($notification['date_creation'])) ?>
              </div>
              <div class="notification-message">
                <?= htmlspecialchars($notification['message']) ?>
              </div>
              <div class="notification-actions">
                <?php if ($notification['article_id']): ?>
                  <button class="btn-view" data-id="<?= $notification['article_id'] ?>">Voir l'article</button>
                <?php endif; ?>
                <button class="btn-dismiss" data-id="<?= $notification['id'] ?>">
                  <?= $notification['lue'] ? 'Ignorer' : 'Marquer comme lu' ?>
                </button>
              </div>
            </div>
          </div>
        <?php endforeach; ?>
      <?php endif; ?>
    </div>

    <div class="footer">
      <small>agoriafrancia@ece.fr | Copyright &copy; 2025 Agoria Francia | +33 06 30 44 46 50</small>
    </div>
  </div>


  <script>
  document.querySelectorAll('.btn-dismiss').forEach(btn => {
  btn.addEventListener('click', function () {
    const notificationId = this.dataset.id;
    const action = this.textContent.trim().toLowerCase();
    const notificationItem = this.closest('.notification-item');

    if (action === 'marquer comme lu') {
      // Marquer comme lue
      fetch('mark_notification_read.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: `id=${notificationId}`
      })
        .then(response => response.json())
        .then(data => {
          if (data.success) {
            notificationItem.classList.remove('unread');
            this.textContent = 'Ignorer';
          }
        });
    } else if (action === 'ignorer') {
      // Supprimer
      fetch('delete_notification.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: `id=${notificationId}`
      })
        .then(response => response.json())
        .then(data => {
          if (data.success) {
            notificationItem.remove(); // Supprime de l'affichage
          }
        });
    }
  });
});



    document.querySelectorAll('.btn-view').forEach(btn => {
      btn.addEventListener('click', function() {
        const articleId = this.dataset.id;
        window.location.href = `article_details.php?id=${articleId}`;
      });
    });

    document.querySelectorAll('.filter-tab').forEach(tab => {
      tab.addEventListener('click', function() {
        document.querySelector('.filter-tab.active')?.classList.remove('active');
        this.classList.add('active');

        const filterType = this.textContent.trim().toLowerCase();
        window.location.href = `notifications.php?filter=${filterType}`;
      });
    });
  </script>
</body>
</html>
