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
        <button onclick="window.location.href='article.php?id=${article.id}'">Acheter / Voir</button>
      `;
      productsContainer.appendChild(div);
    });
  };

  filterBtn.addEventListener('click', loadArticles);

  document.querySelectorAll('.top-bar a').forEach(link => {
    link.addEventListener('click', e => {
      e.preventDefault();
      selectedCategory = link.textContent.trim();
      loadArticles();
    });
  });

  // Chargement initial sans filtres
  loadArticles();
});
