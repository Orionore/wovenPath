import { Controller } from '@hotwired/stimulus';

export default class extends Controller {

    static targets = ["menu", "icon"]

    connect() {
        // Initialisation - le menu est fermé par défaut
        this.isOpen = false;

        // Ajout d'un listener pour fermer le menu si on clique ailleurs
        document.addEventListener('click', this.closeOnClickOutside.bind(this));

        // Fermer le menu si on redimensionne l'écran (passe en desktop)
        window.addEventListener('resize', this.handleResize.bind(this));
    }

    disconnect() {
        // Nettoyer les listeners quand le contrôleur est déconnecté
        document.removeEventListener('click', this.closeOnClickOutside.bind(this));
        window.removeEventListener('resize', this.handleResize.bind(this));
    }

    toggle() {
        this.isOpen = !this.isOpen;

        if (this.isOpen) {
            this.openMenu();
        } else {
            this.closeMenu();
        }
    }

    openMenu() {
        // Animation de l'icône
        this.iconTarget.classList.add('active');

        // Afficher le menu
        this.menuTarget.classList.add('active');

        // Bloquer le scroll de la page
        document.body.style.overflow = 'hidden';
    }

    closeMenu() {
        // Animation de l'icône
        this.iconTarget.classList.remove('active');

        // Cacher le menu
        this.menuTarget.classList.remove('active');

        // Réactiver le scroll
        document.body.style.overflow = '';
    }

    closeOnClickOutside(event) {
        // Ne pas déclencher si on clique sur le menu ou l'icône
        if (this.isOpen &&
            !this.menuTarget.contains(event.target) &&
            !this.iconTarget.contains(event.target)) {
            this.toggle();
        }
    }

    handleResize() {
        // Fermer le menu si on passe en desktop
        if (window.innerWidth >= 768 && this.isOpen) {
            this.toggle();
        }
    }
}