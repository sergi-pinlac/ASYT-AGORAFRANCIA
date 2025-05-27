document.addEventListener('DOMContentLoaded', () => {
  const filterBtn = document.getElementById('filter-button');
  const productsContainer = document.getElementById('products-container');
  let selectedCategory = '';

  const loadArticles = async () => {
    const type_article = Array.from(document.querySelectorAll('input[name="type_article[]"]:checked')).map(cb => cb.value);
    const type_vente = Array.from(document.querySelectorAll('input[name="type_vente[]"]:checked')).map(cb => cb.value);

    const params = new URLSearchParams();
    type_article.forEach(v => params.append('type_article[]', v));
    type_vente.forEach(v => params.append('type_vente[]', v));
    if (selectedCategory) params.append('categorie', selectedCategory);

    const response = await fetch('filtrer_articles.php?' + params.toString());
    const articles = await response.json();

    productsContainer.innerHTML = '';
    articles.forEach(article => {
      const div = document.createElement('div');
      div.className = 'product';
      div.innerHTML = `
        <img src="${article.image_principale}" alt="${article.nom}">
        <h4>${article.nom}</h4>
        <div class="type">${article.type_article} | ${article.type_vente}</div>
        <div class="price">${article.prix} €</div>
        <button class="view-btn" data-id="${article.id}">Acheter / Voir</button>
      `;
      productsContainer.appendChild(div);
    });
  };

  // Afficher les détails d’un article
  const showArticleDetails = async (id) => {
    const response = await fetch(`get_articles.php?id=${id}`);
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
        <br><br>
        <button id="retour-btn">⬅ Retour</button>
      </div>
    `;

    document.getElementById('retour-btn').addEventListener('click', loadArticles);
  };

  // Gérer clic sur "Acheter / Voir"
  productsContainer.addEventListener('click', (e) => {
    if (e.target.classList.contains('view-btn')) {
      const articleId = e.target.dataset.id;
      showArticleDetails(articleId);
    }
  });

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
