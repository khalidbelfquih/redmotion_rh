// Variables globales
let articleCounter = 0;
let articlesData = [];
let fournisseursData = [];

// Initialisation au chargement de la page
document.addEventListener('DOMContentLoaded', function() {
    console.log('Initialisation de la page commande...');
    chargerFournisseurs();
    chargerArticles();
    
    // Ajouter le premier article après un délai pour s'assurer que les données sont chargées
    setTimeout(() => {
        ajouterArticle();
    }, 1000);
    
    // Gérer la soumission du formulaire fournisseur
    const formFournisseur = document.getElementById('formFournisseur');
    if (formFournisseur) {
        formFournisseur.addEventListener('submit', function(e) {
            e.preventDefault();
            soumettreFormulaireFournisseur();
        });
    }
});

// Fonctions pour les modals
function ouvrirModalFournisseur() {
    document.getElementById('modalFournisseur').classList.add('active');
    document.body.style.overflow = 'hidden';
    
    // Réinitialiser le formulaire
    document.getElementById('formFournisseur').reset();
}

function ouvrirModalListeFournisseurs() {
    document.getElementById('modalListeFournisseurs').classList.add('active');
    document.body.style.overflow = 'hidden';
    chargerListeFournisseurs();
}

function fermerModal(modalId) {
    document.getElementById(modalId).classList.remove('active');
    document.body.style.overflow = 'auto';
}

// Fermer modal en cliquant en dehors
window.onclick = function(event) {
    if (event.target.classList.contains('modal')) {
        event.target.classList.remove('active');
        document.body.style.overflow = 'auto';
    }
}

// Fermer modal avec la touche Escape
document.addEventListener('keydown', function(event) {
    if (event.key === 'Escape') {
        const modalsActifs = document.querySelectorAll('.modal.active');
        modalsActifs.forEach(modal => {
            modal.classList.remove('active');
        });
        document.body.style.overflow = 'auto';
    }
});

// Charger les fournisseurs
async function chargerFournisseurs() {
    console.log('Chargement des fournisseurs...');
    
    try {
        const response = await fetch('../model/getFournisseurs.php', {
            method: 'GET',
            headers: {
                'Content-Type': 'application/json',
            }
        });
        
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        
        const data = await response.json();
        console.log('Réponse fournisseurs:', data);
        
        if (data.success) {
            fournisseursData = data.fournisseurs;
            const select = document.getElementById('id_fournisseur');
            
            if (select) {
                select.innerHTML = '<option value="">Sélectionnez un fournisseur</option>';
                
                fournisseursData.forEach(fournisseur => {
                    const nom = fournisseur.societe ? 
                        `${fournisseur.societe} (${fournisseur.nom} ${fournisseur.prenom})` : 
                        `${fournisseur.nom} ${fournisseur.prenom}`;
                    const option = new Option(nom, fournisseur.id);
                    select.appendChild(option);
                });
                
                console.log(`${fournisseursData.length} fournisseurs chargés`);
            }
        } else {
            console.error('Erreur lors du chargement des fournisseurs:', data.message);
            const select = document.getElementById('id_fournisseur');
            if (select) {
                select.innerHTML = '<option value="">Aucun fournisseur disponible</option>';
            }
        }
    } catch (error) {
        console.error('Erreur lors du chargement des fournisseurs:', error);
        const select = document.getElementById('id_fournisseur');
        if (select) {
            select.innerHTML = '<option value="">Erreur de chargement</option>';
        }
    }
}

// Charger les articles
async function chargerArticles() {
    console.log('Chargement des articles...');
    
    try {
        const response = await fetch('../model/getArticles.php', {
            method: 'GET',
            headers: {
                'Content-Type': 'application/json',
            }
        });
        
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        
        const data = await response.json();
        console.log('Réponse articles:', data);
        
        if (data.success) {
            articlesData = data.articles;
            console.log(`${articlesData.length} articles chargés`);
        } else {
            console.error('Erreur lors du chargement des articles:', data.message);
            articlesData = [];
        }
    } catch (error) {
        console.error('Erreur lors du chargement des articles:', error);
        articlesData = [];
    }
}

// Charger la liste des fournisseurs pour le modal
async function chargerListeFournisseurs() {
    const content = document.getElementById('listeFournisseursContent');
    content.innerHTML = `
        <div class="loading">
            <div class="spinner"></div>
            Chargement des fournisseurs...
        </div>
    `;

    try {
        const response = await fetch('../model/getFournisseurs.php');
        const data = await response.json();
        
        if (data.success && data.fournisseurs.length > 0) {
            let tableHTML = `
                <table class="modal-table">
                    <thead>
                        <tr>
                            <th>Société</th>
                            <th>Nom</th>
                            <th>Téléphone</th>
                            <th>Email</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
            `;
            
            data.fournisseurs.forEach(f => {
                tableHTML += `
                    <tr>
                        <td>${f.societe || '-'}</td>
                        <td>${f.nom} ${f.prenom}</td>
                        <td>${f.telephone}</td>
                        <td>${f.email || '-'}</td>
                        <td>
                            <button class="action-btn edit" onclick="modifierFournisseur(${f.id})" title="Modifier">
                                <i class='bx bx-edit-alt'></i>
                            </button>
                        </td>
                    </tr>
                `;
            });
            
            tableHTML += `
                    </tbody>
                </table>
            `;
            
            content.innerHTML = tableHTML;
        } else {
            content.innerHTML = '<p style="text-align: center; color: #666; padding: 40px;">Aucun fournisseur trouvé</p>';
        }
    } catch (error) {
        content.innerHTML = '<p style="text-align: center; color: #e74c3c; padding: 40px;">Erreur lors du chargement</p>';
    }
}

// Soumettre le formulaire fournisseur via AJAX
async function soumettreFormulaireFournisseur() {
    const form = document.getElementById('formFournisseur');
    const formData = new FormData(form);
    
    try {
        const response = await fetch('../model/ajoutFournisseur.php', {
            method: 'POST',
            body: formData
        });
        
        if (response.ok) {
            // Fermer le modal
            fermerModal('modalFournisseur');
            
            // Recharger les fournisseurs
            await chargerFournisseurs();
            
            // Afficher un message de succès
            afficherMessage('Fournisseur ajouté avec succès!', 'success');
            
            // Réinitialiser le formulaire
            form.reset();
        } else {
            throw new Error('Erreur lors de l\'ajout du fournisseur');
        }
    } catch (error) {
        console.error('Erreur:', error);
        afficherMessage('Erreur lors de l\'ajout du fournisseur', 'error');
    }
}

// Ajouter un article à la commande
function ajouterArticle() {
    articleCounter++;
    const container = document.getElementById('articles-container');
    
    const articleDiv = document.createElement('div');
    articleDiv.className = 'article-item';
    articleDiv.id = `article-${articleCounter}`;
    
    articleDiv.innerHTML = `
        <div class="article-item-header">
            <span class="article-number">Article ${articleCounter}</span>
            <button type="button" class="btn-remove-article" onclick="supprimerArticle(${articleCounter})">
                <i class='bx bx-x'></i>
            </button>
        </div>
        <div class="article-row">
            <div class="form-group">
                <label for="article_${articleCounter}">Article <span class="required">*</span></label>
                <select name="articles[${articleCounter}][id_article]" id="article_${articleCounter}" required onchange="updatePrix(${articleCounter})">
                    <option value="">Sélectionnez un article</option>
                </select>
            </div>
            <div class="form-group">
                <label for="quantite_${articleCounter}">Quantité <span class="required">*</span></label>
                <input type="number" name="articles[${articleCounter}][quantite]" id="quantite_${articleCounter}" 
                       min="1" required onchange="updatePrix(${articleCounter})">
            </div>
            <div class="form-group">
                <label for="prix_unitaire_${articleCounter}">Prix unitaire (DH)</label>
                <input type="number" name="articles[${articleCounter}][prix_unitaire]" id="prix_unitaire_${articleCounter}" 
                       step="0.01" readonly>
            </div>
            <div class="form-group">
                <label for="prix_total_${articleCounter}">Prix total (DH)</label>
                <input type="number" name="articles[${articleCounter}][prix_total]" id="prix_total_${articleCounter}" 
                       step="0.01" readonly>
            </div>
        </div>
    `;
    
    container.appendChild(articleDiv);
    
    // Remplir les options d'articles
    const selectArticle = document.getElementById(`article_${articleCounter}`);
    if (articlesData.length > 0) {
        articlesData.forEach(article => {
            const nom = `${article.nom_article} - ${article.marque || ''} (${article.quantite} dispo)`;
            const option = new Option(nom, article.id);
            option.dataset.prix = article.prix_unitaire;
            option.dataset.dispo = article.quantite;
            selectArticle.appendChild(option);
        });
    } else {
        selectArticle.innerHTML = '<option value="">Aucun article disponible</option>';
    }
    
    updateTotaux();
}

// Supprimer un article
function supprimerArticle(id) {
    if (confirm('Voulez-vous vraiment supprimer cet article?')) {
        const articleDiv = document.getElementById(`article-${id}`);
        if (articleDiv) {
            articleDiv.remove();
            updateTotaux();
        }
    }
}

// Mettre à jour les prix
function updatePrix(articleId) {
    const articleSelect = document.getElementById(`article_${articleId}`);
    const quantiteInput = document.getElementById(`quantite_${articleId}`);
    const prixUnitaireInput = document.getElementById(`prix_unitaire_${articleId}`);
    const prixTotalInput = document.getElementById(`prix_total_${articleId}`);
    
    if (articleSelect && quantiteInput && prixUnitaireInput && prixTotalInput) {
        const selectedOption = articleSelect.options[articleSelect.selectedIndex];
        const prixUnitaire = selectedOption ? parseFloat(selectedOption.dataset.prix) || 0 : 0;
        const quantite = parseInt(quantiteInput.value) || 0;
        const prixTotal = prixUnitaire * quantite;
        
        prixUnitaireInput.value = prixUnitaire.toFixed(2);
        prixTotalInput.value = prixTotal.toFixed(2);
        
        updateTotaux();
    }
}

// Mettre à jour les totaux
function updateTotaux() {
    const articles = document.querySelectorAll('.article-item');
    let totalArticles = articles.length;
    let totalQuantite = 0;
    let totalCommande = 0;
    
    articles.forEach(article => {
        const quantiteInput = article.querySelector('input[name*="[quantite]"]');
        const prixTotalInput = article.querySelector('input[name*="[prix_total]"]');
        
        if (quantiteInput && prixTotalInput) {
            totalQuantite += parseInt(quantiteInput.value) || 0;
            totalCommande += parseFloat(prixTotalInput.value) || 0;
        }
    });
    
    // Mettre à jour l'affichage
    const totalArticlesElement = document.getElementById('total-articles');
    const totalQuantiteElement = document.getElementById('total-quantite');
    const totalCommandeElement = document.getElementById('total-commande');
    
    if (totalArticlesElement) totalArticlesElement.textContent = totalArticles;
    if (totalQuantiteElement) totalQuantiteElement.textContent = totalQuantite;
    if (totalCommandeElement) totalCommandeElement.textContent = totalCommande.toFixed(2) + ' DH';
}

// Afficher un message
function afficherMessage(message, type) {
    // Créer l'élément de message
    const messageDiv = document.createElement('div');
    messageDiv.className = `alert ${type === 'success' ? 'success' : 'danger'}`;
    messageDiv.innerHTML = `
        <i class='bx bx-info-circle'></i> ${message}
    `;
    
    // Ajouter le message au début du premier box
    const firstBox = document.querySelector('.box');
    if (firstBox) {
        firstBox.insertBefore(messageDiv, firstBox.firstChild);
        
        // Supprimer le message après 5 secondes
        setTimeout(() => {
            if (messageDiv.parentNode) {
                messageDiv.parentNode.removeChild(messageDiv);
            }
        }, 5000);
    }
}

// Validation du formulaire de commande
document.addEventListener('DOMContentLoaded', function() {
    const commandeForm = document.getElementById('commandeForm');
    if (commandeForm) {
        commandeForm.addEventListener('submit', function(e) {
            const articles = document.querySelectorAll('.article-item');
            if (articles.length === 0) {
                e.preventDefault();
                afficherMessage('Veuillez ajouter au moins un article à la commande.', 'error');
                return false;
            }
            
            // Vérifier que tous les articles ont des valeurs
            let valid = true;
            let messageErreur = '';
            
            articles.forEach((article, index) => {
                const articleSelect = article.querySelector('select[name*="[id_article]"]');
                const quantiteInput = article.querySelector('input[name*="[quantite]"]');
                
                if (!articleSelect.value) {
                    valid = false;
                    messageErreur = `Veuillez sélectionner un article pour la ligne ${index + 1}`;
                } else if (!quantiteInput.value || quantiteInput.value <= 0) {
                    valid = false;
                    messageErreur = `Veuillez saisir une quantité valide pour la ligne ${index + 1}`;
                }
            });
            
            if (!valid) {
                e.preventDefault();
                afficherMessage(messageErreur, 'error');
                return false;
            }
        });
    }
});

// Fonction pour modifier un fournisseur (à implémenter)
function modifierFournisseur(id) {
    // Cette fonction peut être développée pour permettre la modification
    console.log('Modification du fournisseur ID:', id);
    afficherMessage('Fonction de modification à implémenter', 'info');
}