// assets/controllers/story-image-preview_controller.js
import { Controller } from '@hotwired/stimulus';

export default class extends Controller {
    static targets = ['input', 'preview', 'image'];

    connect() {
        // Initialisation au chargement du contrôleur
    }

    preview() {
        const file = this.inputTarget.files[0];

        if (!file) {
            this.previewTarget.classList.add('hidden');
            return;
        }

        const reader = new FileReader();

        reader.onload = (e) => {
            this.imageTarget.src = e.target.result;
            this.previewTarget.classList.remove('hidden');
        };

        reader.readAsDataURL(file);
    }
}