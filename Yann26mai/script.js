fetch('get_articles.php') // pas de sous-dossier
  .then(response => response.json())
  .then(data => {
    const container = document.getElementById('products-container');
    data.forEach(article => {
      const div = document.createElement('div');
      div.className = 'product';
      div.innerHTML = `
        <img src="${article.image_principale}" alt="${article.nom}">
        <h4>${article.nom}</h4>
        <div class="type">${article.type_article} | ${article.type_vente}</div>
        <div class="price">${article.prix}€</div>
        <button>${article.type_vente === 'enchere' ? 'Enchérir' : 'Acheter'}</button>
      `;
      container.appendChild(div);
    });
  })
  .catch(err => {
    console.error("Erreur de chargement des articles :", err);
  });
