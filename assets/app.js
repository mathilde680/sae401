/*import './bootstrap.js';*/
/*
 * Welcome to your app's main JavaScript file!
 *
 * This file will be included onto the page via the importmap() Twig function,
 * which should already be in your base.html.twig.
 */
import './styles/app.scss';

console.log('This log comes from assets/app.js - welcome to AssetMapper! 🎉');


//nom du menu au survol des icons dans la barre de nav
try {
    const nom = document.getElementById('popAccueil');

    nom.addEventListener('hover', () => {
        if (n !== nom && n.classList.contains('active')) {
            n.classList.remove('active');
        }
    });
    nom.classList.toggle('active');

} catch (error) {
    console.error('Erreur détectée :', error);
}

try{
    const modal = document.getElementById('modalOverlay');
    const modalTitle = document.getElementById('modalTitle');
    const deleteForm = document.getElementById('deleteForm');
    const useGrilleLink = document.getElementById('useGrilleLink');

    // Ajouter les événements sur tous les liens de grilles
    document.querySelectorAll('.grille-link').forEach(function(link) {
        link.addEventListener('click', function(e) {
            e.preventDefault();

            // Récupérer les données de la grille
            const grilleId = this.getAttribute('data-id');
            const grilleName = this.getAttribute('data-nom');

            // Mise à jour du titre de la modal
            modalTitle.textContent = 'Grille : ' + grilleName;

            // Mise à jour des actions de la modal
            deleteForm.action = '{{ path("app_grille_supprimer", {"id": "GRILLE_ID"}) }}'.replace('GRILLE_ID', grilleId);
            useGrilleLink.href = '{{ path("app_fiche_grille", {"id": "GRILLE_ID"}) }}'.replace('GRILLE_ID', grilleId);

            // Afficher la modal
            modal.style.display = 'flex';

            // Charger les critères d'évaluation (AJAX)
            // Cette partie pourrait être implémentée pour charger dynamiquement
            // les critères spécifiques à la grille sélectionnée
        });
    });

    // Fermer la modal en cliquant sur le X
    document.querySelector('.close-button').addEventListener('click', function() {
        modal.style.display = 'none';
    });

    // Fermer la modal en cliquant en dehors
    modal.addEventListener('click', function(e) {
        if (e.target === modal) {
            modal.style.display = 'none';
        }
    });
}catch (error) {
    console.error('Erreur détectée :', error);
}



//----------------GESTION CRITERES ------------------------
document
    .querySelectorAll('.add_item_link')
    .forEach(btn => {
        btn.addEventListener("click", addFormToCollection)
    });

document
    .querySelectorAll('.criteres')
    .forEach(collection => {
        collection.addEventListener("click", function (e) {
            if (e.target.classList.contains("remove-item")) {
                e.target.closest('.critere-item').remove();
            }
        });
    });

function addFormToCollection(e) {
    const collectionHolder = document.querySelector('.' + e.currentTarget.dataset.collectionHolderClass);
    const item = document.createElement('div');

    item.classList.add("critere-item");

    item.innerHTML = collectionHolder
            .dataset
            .prototype
            .replace(/__name__/g, collectionHolder.dataset.index) +
        '<button type="button" class="remove-item">X</button>';

    collectionHolder.appendChild(item);
    collectionHolder.dataset.index++;
}






