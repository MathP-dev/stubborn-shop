// Main JavaScript file for Stubborn Shop

document.addEventListener('DOMContentLoaded', function() {
    // Handle image loading errors with data-fallback attribute
    const images = document.querySelectorAll('img[data-fallback]');
    images.forEach(img => {
        img.addEventListener('error', function() {
            const fallback = this.getAttribute('data-fallback');
            if (fallback && this.src !== fallback) {
                this.src = fallback;
            }
        });
    });

    // Handle cart removal confirmations
    const cartRemoveForms = document.querySelectorAll('.cart-remove-form');
    cartRemoveForms.forEach(form => {
        form.addEventListener('submit', function(e) {
            if (!confirm('Êtes-vous sûr de vouloir retirer cet article ?')) {
                e.preventDefault();
            }
        });
    });

    // Handle product deletion confirmations
    const productDeleteForms = document.querySelectorAll('.product-delete-form');
    productDeleteForms.forEach(form => {
        form.addEventListener('submit', function(e) {
            if (!confirm('Êtes-vous sûr de vouloir supprimer ce produit ?')) {
                e.preventDefault();
            }
        });
    });
});

// Helper functions for manual confirmation (if needed)
function confirmAction(message) {
    return confirm(message || 'Êtes-vous sûr de vouloir effectuer cette action ?');
}

function confirmCartRemoval() {
    return confirm('Êtes-vous sûr de vouloir retirer cet article ?');
}

function confirmProductDeletion() {
    return confirm('Êtes-vous sûr de vouloir supprimer ce produit ?');
}

