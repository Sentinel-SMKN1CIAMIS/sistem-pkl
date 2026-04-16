import './bootstrap';
import Alpine from 'alpinejs';

window.Alpine = Alpine;

// Global Form Helper
Alpine.data('formHelper', () => ({
    loading: false,
    isValid: true,
    submit() {
        if ($el.checkValidity()) {
            this.loading = true;
            $el.submit();
        }
    }
}));

// Global error listener for Lucide icons after dynamic updates
document.addEventListener('alpine:initialized', () => {
    if (window.lucide) {
        window.lucide.createIcons();
    }
});

Alpine.start();
