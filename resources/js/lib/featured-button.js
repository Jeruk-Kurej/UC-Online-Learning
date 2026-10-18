// --- Redraws a featured button (components/admin/featured-button) after the server changed the state ---

export default function showFeatured(button, isFeatured) {
    button.classList.toggle('btn-primary', isFeatured);
    button.classList.toggle('btn-secondary', !isFeatured);
    button.setAttribute('aria-pressed', isFeatured ? 'true' : 'false');
    button.querySelector('i').className = `bi ${isFeatured ? 'bi-star-fill' : 'bi-star'}`;
    button.querySelector('[data-featured-label]').textContent = isFeatured ? 'Featured' : 'Feature';
}
