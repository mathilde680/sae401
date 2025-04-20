/*import './bootstrap.js';*/
/*
 * Welcome to your app's main JavaScript file!
 *
 * This file will be included onto the page via the importmap() Twig function,
 * which should already be in your base.html.twig.
 */
import './styles/app.scss';
import './js/alert';

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

try {
    const modal = document.getElementById('modalOverlay');
    const modalTitle = document.getElementById('modalTitle');
    const deleteForm = document.getElementById('deleteForm');
    const deleteButton = document.getElementById('delete-button');
    const useGrilleLink = document.getElementById('useGrilleLink');
    const useGrilleUtiliser = document.getElementById('useGrilleUtiliser');
    const modalBody = document.querySelector('.modal-body');

// Ajouter les événements sur tous les liens de grilles
    document.querySelectorAll('.grille-link').forEach(function (link) {
        link.addEventListener('click', function (e) {
            e.preventDefault();

            // Récupérer les données de la grille
            const grilleId = this.getAttribute('data-id');
            const grilleName = this.getAttribute('data-nom');
            //const deleteUrl = this.getAttribute('data-url');
            const criteres = JSON.parse(this.getAttribute('data-criteres'));

            // Mise à jour du titre de la modal
            modalTitle.textContent = 'Grille : ' + grilleName;

            // Mise à jour du titre de la modal
            const deleteUrl = `/grille/${grilleId}/supprime`;
            deleteForm.action = deleteUrl;

            if (useGrilleUtiliser) {
                // Modifier du titre de la modal
                useGrilleUtiliser.href = `/evaluation/ajout/grille/${grilleId}`;
            }


            // Utiliser


            // Vider le contenu actuel de la modal
            modalBody.innerHTML = '';

            // Ajouter les critères à la modal
            criteres.forEach(function (critere) {
                const item = document.createElement('div');
                item.className = 'evaluation-item';

                const itemHeader = document.createElement('div');
                itemHeader.className = 'evaluation-header';

                const nom = document.createElement('p');
                nom.className = 'evaluation-label';
                nom.textContent = critere.nom;

                const note = document.createElement('p');
                note.className = 'evaluation-score';
                note.textContent = '/' + critere.note;

                const commentaire = document.createElement('p');
                commentaire.className = 'evaluation-score';
                commentaire.textContent = critere.commentaire;

                item.appendChild(itemHeader);
                item.appendChild(commentaire);
                itemHeader.appendChild(nom);
                itemHeader.appendChild(note);

                modalBody.appendChild(item);
            });

            // Afficher la modal
            modal.style.display = 'flex';
        });
    });

    // Fermer la modal en cliquant sur le X
    document.querySelector('.close-button').addEventListener('click', function () {
        modal.style.display = 'none';
    });

    // Fermer la modal en cliquant en dehors
    modal.addEventListener('click', function (e) {
        if (e.target === modal) {
            modal.style.display = 'none';
        }
    });
} catch (error) {
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

// Affichage des notes

document.addEventListener('DOMContentLoaded', function () {
    const titresNotes = document.querySelectorAll('.titre_note');

    titresNotes.forEach(titre => {
        const notesMatiereDiv = titre.nextElementSibling;


        notesMatiereDiv.style.height = '0';
        notesMatiereDiv.style.overflow = 'hidden';

        titre.addEventListener('click', function () {
            const notesMatiereDiv = this.nextElementSibling;

            if (notesMatiereDiv.style.height === '0px') {
                // Obtenir la hauteur réelle du contenu
                const height = notesMatiereDiv.scrollHeight + 'px';
                notesMatiereDiv.style.height = height;
                this.querySelector('svg').style.transform = 'rotate(90deg)';
            } else {
                notesMatiereDiv.style.height = '0';
                this.querySelector('svg').style.transform = 'rotate(0deg)';
            }
        });

        titre.style.cursor = 'pointer';
    });
});


// DARKMODE -----------------------------------------------------------------------------

document.addEventListener('DOMContentLoaded', function () {
    const darkModeToggle = document.getElementById('darkmode');
    const html = document.documentElement;
    const header = document.querySelector('header');
    const headerLinks = document.querySelectorAll('header a');
    const footer = document.querySelector('footer');
    const otherElements = document.querySelectorAll('.dark-mode-specific');

    function isDarkModeEnabled() {
        return localStorage.getItem('darkModeEnabled') === 'true';
    }

    function enableDarkMode() {
        html.classList.add('dark');
        header?.classList.add('dark');
        footer?.classList.add('dark');
        headerLinks.forEach(link => link.classList.add('dark'));
        otherElements.forEach(element => element.classList.add('dark'));
        localStorage.setItem('darkModeEnabled', 'true');
    }

    function disableDarkMode() {
        html.classList.remove('dark');
        header?.classList.remove('dark');
        footer?.classList.remove('dark');
        headerLinks.forEach(link => link.classList.remove('dark'));
        otherElements.forEach(element => element.classList.remove('dark'));
        localStorage.setItem('darkModeEnabled', 'false');
    }

    // Appliquer au chargement
    if (isDarkModeEnabled()) {
        enableDarkMode();
    }

    // Attacher l'event uniquement si le bouton est présent
    if (darkModeToggle) {
        darkModeToggle.addEventListener('click', function () {
            if (isDarkModeEnabled()) {
                disableDarkMode();
            } else {
                enableDarkMode();
            }
        });
    }
});
