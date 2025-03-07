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

