import { Controller } from '@hotwired/stimulus';

export default class extends Controller {
    static targets = ['input', 'preview', 'image', 'fileName'];

    connect() {
        // Si on a déjà une image (en mode édition par exemple)
        if (this.inputTarget.dataset.preview) {
            this.previewTarget.classList.remove('hidden');
            this.imageTarget.src = this.inputTarget.dataset.preview;
        }
    }

    preview() {
        const file = this.inputTarget.files[0];

        // Mettre à jour le nom du fichier affiché
        if (this.hasFileNameTarget && file) {
            this.fileNameTarget.textContent = file.name;
        } else if (this.hasFileNameTarget) {
            this.fileNameTarget.textContent = 'Aucun fichier choisi';
        }

        // S'il y a un fichier sélectionné, afficher l'aperçu
        if (file) {
            const reader = new FileReader();

            reader.onload = (e) => {
                this.previewTarget.classList.remove('hidden');
                this.imageTarget.src = e.target.result;
            };

            reader.readAsDataURL(file);
        } else {
            this.previewTarget.classList.add('hidden');
            this.imageTarget.src = '#';
        }
    }
}