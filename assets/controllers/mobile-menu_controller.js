import { Controller } from '@hotwired/stimulus';

export default class extends Controller {

    static targets = ["menu", "icon"]

    connect() {
        this.isOpen = false;
        document.addEventListener('click', this.closeOnClickOutside.bind(this));
        window.addEventListener('resize', this.handleResize.bind(this));
    }

    disconnect() {
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
        this.iconTarget.classList.add('active');
        this.menuTarget.classList.add('active');

        document.body.style.overflow = 'hidden';
    }

    closeMenu() {
        this.iconTarget.classList.remove('active');
        this.menuTarget.classList.remove('active');

        document.body.style.overflow = '';
    }

    closeOnClickOutside(event) {
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