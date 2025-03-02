import { Controller } from '@hotwired/stimulus';

export default class extends Controller {
    static targets = ['searchInput', 'dropdown', 'selectedLabels'];

    connect() {
        // Ajout d'un gestionnaire pour les clics à l'extérieur du dropdown
        document.addEventListener('click', this.handleOutsideClick.bind(this));

        // Styliser les options existantes
        this.styleOptions();

        // Initialiser l'affichage des options sélectionnées
        this.updateSelectedLabels();
    }

    disconnect() {
        document.removeEventListener('click', this.handleOutsideClick.bind(this));
    }

    handleOutsideClick(event) {
        if (!this.element.contains(event.target)) {
            this.close();
        }
    }

    toggle() {
        if (this.dropdownTarget.classList.contains('hidden')) {
            this.open();
        } else {
            this.close();
        }
    }

    open() {
        this.dropdownTarget.classList.remove('hidden');
        this.searchInputTarget.focus();
    }

    close() {
        this.dropdownTarget.classList.add('hidden');
    }

    search() {
        const searchTerm = this.searchInputTarget.value.toLowerCase();
        const options = this.dropdownTarget.querySelectorAll('div');

        options.forEach(option => {
            const label = option.querySelector('label');
            if (label) {
                const text = label.textContent.toLowerCase();
                option.style.display = text.includes(searchTerm) ? 'flex' : 'none';
            }
        });
    }

    styleOptions() {
        // Trouver tous les div qui contiennent des checkboxes et labels
        const options = this.dropdownTarget.querySelectorAll('div');

        options.forEach(option => {
            // Ajouter des styles aux conteneurs d'options
            option.classList.add('flex', 'items-center', 'p-2', 'hover:bg-gray-100', 'cursor-pointer');

            // Récupérer les inputs et labels
            const input = option.querySelector('input[type="checkbox"]');
            const label = option.querySelector('label');

            if (input && label) {
                // Ajouter l'action de mise à jour lors du clic
                option.dataset.action = 'click->genre-dropdown#handleOptionClick';

                // Styler l'input
                input.classList.add('mr-2');

                // Ajouter un gestionnaire d'événements pour mettre à jour les options sélectionnées
                input.addEventListener('change', () => this.updateSelectedLabels());
            }
        });
    }

    handleOptionClick(event) {
        // Empêcher la propagation si on clique sur l'input directement
        if (event.target.tagName === 'INPUT') {
            event.stopPropagation();
            return;
        }

        // Toggle le checkbox si on clique sur le div ou le label
        const option = event.currentTarget;
        const checkbox = option.querySelector('input[type="checkbox"]');
        if (checkbox) {
            checkbox.checked = !checkbox.checked;

            // Déclencher l'événement change manuellement
            checkbox.dispatchEvent(new Event('change'));

            // Mettre à jour les labels sélectionnés
            this.updateSelectedLabels();
        }
    }

    updateSelectedLabels() {
        // Effacer les labels existants
        this.selectedLabelsTarget.innerHTML = '';

        // Obtenir toutes les checkboxes cochées
        const checkedOptions = this.dropdownTarget.querySelectorAll('input[type="checkbox"]:checked');

        // Créer un tag pour chaque option sélectionnée
        checkedOptions.forEach(checkbox => {
            const label = checkbox.nextElementSibling.textContent.trim();
            const value = checkbox.value;

            const tag = document.createElement('div');
            tag.className = 'px-2 py-1 bg-blue-100 rounded-md flex items-center';
            tag.dataset.value = value;
            tag.innerHTML = `
                <span>${label}</span>
                <button type="button" class="ml-1 text-gray-500 hover:text-gray-700" 
                        data-value="${value}"
                        data-action="click->genre-dropdown#removeSelection">×</button>
            `;

            this.selectedLabelsTarget.appendChild(tag);
        });

        // Mettre à jour le placeholder de l'input
        this.updateInputPlaceholder();
    }

    removeSelection(event) {
        event.preventDefault();
        event.stopPropagation();

        const value = event.currentTarget.dataset.value;
        const checkbox = this.dropdownTarget.querySelector(`input[value="${value}"]`);

        if (checkbox) {
            checkbox.checked = false;
            checkbox.dispatchEvent(new Event('change'));
            this.updateSelectedLabels();
        }
    }

    updateInputPlaceholder() {
        const selectedCount = this.selectedLabelsTarget.children.length;

        if (selectedCount === 0) {
            this.searchInputTarget.placeholder = 'Sélectionner le genre...';
        } else if (selectedCount === 1) {
            this.searchInputTarget.placeholder = '1 genre sélectionné';
        } else {
            this.searchInputTarget.placeholder = `${selectedCount} genres sélectionnés`;
        }
    }
}