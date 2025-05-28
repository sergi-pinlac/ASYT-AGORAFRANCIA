document.addEventListener('DOMContentLoaded', () => {
  const filterBtn = document.getElementById('filter-button');
  const productsContainer = document.getElementById('products-container');
  const purchaseModal = document.getElementById('purchase-modal');
  const closeModal = document.querySelector('.close-modal');
  const purchaseForm = document.getElementById('purchase-form');
  let selectedCategory = '';

  // Charger les articles avec filtres
  const loadArticles = async () => {
    const type_article = Array.from(document.querySelectorAll('input[name="type_article[]"]:checked')).map(cb => cb.value);
    const type_vente = Array.from(document.querySelectorAll('input[name="type_vente[]"]:checked')).map(cb => cb.value);

    const params = new URLSearchParams();
    type_article.forEach(v => params.append('type_article[]', v));
    type_vente.forEach(v => params.append('type_vente[]', v));
    if (selectedCategory) params.append('categorie', selectedCategory);

    try {
      const response = await fetch('filtrer_articles.php?' + params.toString());
      if (!response.ok) throw new Error('Erreur réseau');
      
      const articles = await response.json();
      displayArticles(articles);
    } catch (error) {
      console.error('Erreur:', error);
      productsContainer.innerHTML = `<p>Erreur lors du chargement des articles</p>`;
    }
  };

  // Afficher les articles
  const displayArticles = (articles) => {
    productsContainer.innerHTML = '';
    
    if (articles.length === 0) {
      productsContainer.innerHTML = '<p>Aucun article trouvé avec ces critères</p>';
      return;
    }

    articles.forEach(article => {
      const div = document.createElement('div');
      div.className = 'product';
      div.innerHTML = `
        <img src="${article.image_principale}" alt="${article.nom}">
        <h4>${article.nom}</h4>
        <div class="type">${article.type_article} | ${article.type_vente}</div>
        <div class="price">${article.prix} €</div>
        <div class="product-actions">
          <button class="view-btn" data-id="${article.id}">Voir détails</button>
          ${getActionButtons(article)}
        </div>
      `;
      productsContainer.appendChild(div);
    });

    setupActionButtons(); // Important : reconfigurer après le rendu
  };

  // Générer les boutons d'action selon le type de vente
  const getActionButtons = (article) => {
    switch(article.type_vente) {
      case 'achat_immediat':
        return `<button class="buy-now" data-id="${article.id}" data-type="achat_immediat">Acheter maintenant</button>`;
      case 'negociation':
        return `<button class="negotiate" data-id="${article.id}" data-type="negociation">Faire une offre</button>`;
      case 'enchere':
        return `<button class="bid" data-id="${article.id}" data-type="enchere">Faire une enchère</button>`;
      default:
        return '';
    }
  };

  // Afficher les détails d'un article
  const showArticleDetails = async (id) => {
    try {
      const response = await fetch(`get_articles.php?id=${id}`);
      if (!response.ok) throw new Error('Erreur réseau');
      
      const article = await response.json();

      if (article.error) {
        productsContainer.innerHTML = `<p>${article.error}</p>`;
        return;
      }

      productsContainer.innerHTML = `
        <div class="article-details">
          <h2>${article.nom}</h2>
          <img src="${article.image_principale}" alt="${article.nom}" style="max-width:300px;">
          <p><strong>Description :</strong> ${article.description}</p>
          <p><strong>Prix :</strong> ${article.prix} €</p>
          <p><strong>Type de vente :</strong> ${article.type_vente}</p>
          <p><strong>Type d'article :</strong> ${article.type_article}</p>
          ${article.video_url ? `<video controls width="300"><source src="${article.video_url}" type="video/mp4">Votre navigateur ne supporte pas la vidéo.</video>` : ''}
          <div class="product-actions" style="margin-top:20px;">
            ${getActionButtons(article)}
            <button id="retour-btn">⬅ Retour à la liste</button>
          </div>
        </div>
      `;

      document.getElementById('retour-btn').addEventListener('click', loadArticles);
      setupActionButtons(); // Pour les nouveaux boutons dans la vue détaillée
    } catch (error) {
      console.error('Erreur:', error);
      productsContainer.innerHTML = `<p>Erreur lors du chargement des détails</p>`;
    }
  };

  // Configurer les boutons d'action
  const setupActionButtons = () => {
    document.querySelectorAll('.buy-now, .negotiate, .bid').forEach(btn => {
      btn.addEventListener('click', (e) => {
        openPurchaseModal(
          e.target.dataset.id,
          e.target.dataset.type,
          e.target.closest('.article-details') ? true : false
        );
      });
    });
  };

  // Ouvrir le modal d'achat
  const openPurchaseModal = (articleId, purchaseType, isDetailView = false) => {
    document.getElementById('article-id').value = articleId;
    document.getElementById('article-type').value = purchaseType;
    
    // Masquer tous les groupes de formulaire d'abord
    document.getElementById('quantity-group').style.display = 'none';
    document.getElementById('offer-group').style.display = 'none';
    document.getElementById('bid-group').style.display = 'none';
    
    // Configurer le modal selon le type d'achat
    switch(purchaseType) {
      case 'achat_immediat':
        document.getElementById('modal-title').textContent = 'Achat immédiat';
        document.getElementById('quantity-group').style.display = 'block';
        break;
      case 'negociation':
        document.getElementById('modal-title').textContent = 'Faire une offre';
        document.getElementById('offer-group').style.display = 'block';
        break;
      case 'enchere':
        document.getElementById('modal-title').textContent = 'Faire une enchère';
        document.getElementById('bid-group').style.display = 'block';
        break;
    }
    
    purchaseModal.style.display = 'block';
  };

  // Fermer le modal
  closeModal.addEventListener('click', () => {
    purchaseModal.style.display = 'none';
  });

  // Gérer la soumission du formulaire
  purchaseForm.addEventListener('submit', async (e) => {
    e.preventDefault();
    
    const articleId = document.getElementById('article-id').value;
    const purchaseType = document.getElementById('article-type').value;
    let formData = new FormData();
    formData.append('article_id', articleId);
    
    switch(purchaseType) {
      case 'achat_immediat':
        formData.append('quantity', document.getElementById('quantity').value);
        break;
      case 'negociation':
        formData.append('offer_price', document.getElementById('offer-price').value);
        break;
      case 'enchere':
        formData.append('bid_price', document.getElementById('bid-price').value);
        break;
    }
    
    try {
      const response = await fetch('process_purchase.php', {
        method: 'POST',
        body: formData
      });
      
      if (!response.ok) throw new Error('Erreur réseau');
      
      const result = await response.json();
      
      if (result.success) {
        alert(result.message);
        purchaseModal.style.display = 'none';
        loadArticles(); // Mettre à jour la liste après achat
      } else {
        alert('Erreur: ' + result.message);
      }
    } catch (error) {
      console.error('Erreur:', error);
      alert('Une erreur est survenue lors du traitement');
    }
  });

  // Gérer les clics sur les boutons Voir détails
  productsContainer.addEventListener('click', (e) => {
    if (e.target.classList.contains('view-btn')) {
      const articleId = e.target.dataset.id;
      showArticleDetails(articleId);
    }
  });

  // Configurer les boutons d'action après chargement initial
  setupActionButtons();

  // Événements
  filterBtn.addEventListener('click', loadArticles);

  document.querySelectorAll('.top-bar a').forEach(link => {
    link.addEventListener('click', e => {
      e.preventDefault();
      selectedCategory = link.textContent.trim();
      loadArticles();
    });
  });

  // Chargement initial
  loadArticles();
});
