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
@import url('https://fonts.googleapis.com/css2?family=EB+Garamond&family=Cinzel:wght@600&display=swap');

/* ===== Base Styles ===== */
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
  max-width: 1000px;
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
  transition: all 0.3s ease;
  box-shadow: 0 4px 6px rgba(168, 126, 65, 0.4);
}

.navigation button:hover {
  background-color: #cfa95e;
  box-shadow: 0 6px 12px rgba(168, 126, 65, 0.5);
  transform: translateY(-3px);
}

/* ===== Notifications Header ===== */
.notifications-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 20px;
  padding: 10px;
  background-color: rgba(255, 250, 240, 0.8);
  border-radius: 10px;
}

.notifications-title {
  font-family: 'Cinzel', serif;
  color: #2f4a6d;
  font-size: 28px;
  margin: 0;
}

/* ===== Search Bar ===== */
.search-bar {
  display: flex;
  align-items: center;
}

.search-bar input {
  padding: 10px 15px;
  border: 2px solid #a87e41;
  border-radius: 6px;
  width: 250px;
  font-family: 'EB Garamond', serif;
  background-color: #fdfaf4;
  transition: all 0.3s ease;
}

.search-bar input:focus {
  border-color: #8b0000;
  box-shadow: 0 0 8px rgba(139, 0, 0, 0.3);
  outline: none;
}

.search-bar button {
  padding: 10px 15px;
  border: none;
  background-color: #8b0000;
  color: white;
  border-radius: 6px;
  margin-left: 10px;
  cursor: pointer;
  font-family: 'EB Garamond', serif;
  font-weight: bold;
  transition: all 0.3s ease;
}

.search-bar button:hover {
  background-color: #6b0000;
  transform: translateY(-2px);
}

/* ===== Filter Tabs ===== */
.filter-tabs {
  display: flex;
  gap: 10px;
  margin-bottom: 20px;
  flex-wrap: wrap;
}

.filter-tab {
  background-color: #e5d4a1;
  padding: 10px 15px;
  border-radius: 8px;
  border: 2px solid #a87e41;
  cursor: pointer;
  font-weight: bold;
  transition: all 0.3s ease;
  box-shadow: 0 2px 5px rgba(0,0,0,0.1);
}

.filter-tab:hover:not(.active) {
  background-color: #d1b97b;
  transform: translateY(-2px);
}

.filter-tab.active {
  background-color: #8b0000;
  color: white;
  border-color: #3a2f0b;
}

/* ===== Notification Items ===== */
.notification-item {
  display: flex;
  gap: 15px;
  background-color: #fffaf0;
  padding: 20px;
  border: 3px double #a87e41;
  border-radius: 10px;
  margin-bottom: 15px;
  box-shadow: 0 4px 8px rgba(0,0,0,0.1);
  transition: all 0.3s ease;
}

.notification-item:hover {
  transform: translateY(-3px);
  box-shadow: 0 6px 12px rgba(0,0,0,0.15);
}

.notification-item.unread {
  background-color: #fff2cc;
  border-left: 6px solid #8b0000;
}

.notification-icon {
  font-size: 30px;
  margin-top: 5px;
  min-width: 40px;
  text-align: center;
}

.notification-content {
  flex: 1;
}

.notification-title {
  font-weight: bold;
  font-size: 18px;
  margin-bottom: 5px;
  color: #2f4a6d;
  display: flex;
  align-items: center;
  flex-wrap: wrap;
}

.notification-category {
  font-size: 14px;
  margin-left: 10px;
  background-color: #a87e41;
  color: white;
  padding: 3px 8px;
  border-radius: 12px;
  text-transform: capitalize;
}

.notification-time {
  font-style: italic;
  color: #5a4a2a;
  margin-bottom: 10px;
  font-size: 14px;
}

.notification-message {
  margin-bottom: 15px;
  line-height: 1.5;
}

.notification-actions {
  display: flex;
  gap: 10px;
  flex-wrap: wrap;
}

.notification-actions button {
  background-color: #e5d4a1;
  border: 2px solid #a87e41;
  border-radius: 6px;
  padding: 8px 15px;
  font-weight: bold;
  color: #3a2f0b;
  cursor: pointer;
  transition: all 0.3s ease;
}

.notification-actions button:hover {
  background-color: #d1b97b;
  transform: translateY(-2px);
}

/* ===== Empty State ===== */
.empty-notifications {
  background-color: #fffaf0;
  padding: 40px;
  border: 3px dashed #a87e41;
  text-align: center;
  border-radius: 10px;
  font-size: 18px;
  color: #5a4a2a;
}

.empty-notifications a {
  color: #8b0000;
  text-decoration: none;
  font-weight: bold;
  transition: color 0.3s;
}

.empty-notifications a:hover {
  text-decoration: underline;
  color: #6b0000;
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

/* ===== Animations ===== */
@keyframes fadeIn {
  from { opacity: 0; transform: translateY(10px); }
  to { opacity: 1; transform: translateY(0); }
}

/* ===== Category Colors ===== */
.category-enchere { background-color: #8b0000 !important; }
.category-negociation { background-color: #2f4a6d !important; }
.category-alerte { background-color: #5a4a2a !important; }
.category-achat { background-color: #3a6617 !important; }
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
    'achat' => '🛒',      // Pour les ajouts au panier
    'panier' => '🛒',     // Pour les commandes finalisées
    'negociation' => '🤝',
    'enchere' => '🏆',
    'alerte' => '🔔',
    'livraison' => '✉️',
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
