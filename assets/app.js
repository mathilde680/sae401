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


//gestion des modales
// Sélection des éléments
const modal = document.getElementById("myModal");
const openModalBtn = document.getElementById("openModal");
const closeModalBtn = document.querySelector(".close");

// Ouvrir la modal
openModalBtn.onclick = function() {
    modal.style.display = "flex";
}

// Fermer la modal quand on clique sur (x)
closeModalBtn.onclick = function() {
    modal.style.display = "none";
}

// Fermer la modal si on clique en dehors du contenu
window.onclick = function(event) {
    if (event.target === modal) {
        modal.style.display = "none";
    }
}

