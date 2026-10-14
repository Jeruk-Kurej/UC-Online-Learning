// --- Admin testimonies: star button toggles whether a testimony is featured on the home page ---
// <button data-testimony-featured-toggle="{user id}">

function toggleFeatured(userId, buttonElement) {
    if (buttonElement.disabled) return;
    buttonElement.disabled = true;

    const token = document.querySelector('meta[name="csrf-token"]').content;
    const url = `/admin/testimonies/${userId}/toggle-featured`;

    fetch(url, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': token,
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => {
        if (!response.ok) {
            return response.json().then(err => { throw err; });
        }
        return response.json();
    })
    .then(data => {
        if (data.success) {
            const isFeatured = data.is_featured;
            buttonElement.setAttribute('data-featured', isFeatured ? '1' : '0');
            buttonElement.title = isFeatured ? 'Remove from featured' : 'Add to featured';

            // Adjust classes
            const icon = buttonElement.querySelector('i');
            if (isFeatured) {
                buttonElement.className = 'w-10 h-10 rounded-full inline-flex items-center justify-center transition-all duration-300 border shadow-md focus:outline-none bg-[#ff8a00] border-[#ff8a00] text-white hover:bg-orange-600';
                icon.className = 'bi bi-star-fill text-lg leading-none';
            } else {
                buttonElement.className = 'w-10 h-10 rounded-full inline-flex items-center justify-center transition-all duration-300 border shadow-md focus:outline-none bg-white/90 border-gray-200 text-gray-400 hover:text-[#ff8a00] hover:border-[#ff8a00]';
                icon.className = 'bi bi-star text-lg leading-none';
            }

            // Update stats counters dynamically
            const statFeatured = document.getElementById('stat-featured-testimonies');
            if (statFeatured) {
                let currentCount = parseInt(statFeatured.textContent, 10);
                statFeatured.textContent = isFeatured ? currentCount + 1 : Math.max(0, currentCount - 1);
            }

            if (window.showToast) {
                window.showToast(data.message, 'success');
            }
        } else {
            if (window.showToast) {
                window.showToast(data.message || 'An error occurred.', 'error');
            } else {
                alert(data.message || 'An error occurred.');
            }
        }
    })
    .catch(err => {
        console.error('Error toggling featured status:', err);
        const errMsg = err.message || 'An error occurred. Please try again.';
        if (window.showToast) {
            window.showToast(errMsg, 'error');
        } else {
            alert(errMsg);
        }
    })
    .finally(() => {
        buttonElement.disabled = false;
    });
}

export default function initTestimonyAdmin() {
    document.addEventListener('click', (event) => {
        const button = event.target.closest('[data-testimony-featured-toggle]');

        if (button) {
            toggleFeatured(button.dataset.testimonyFeaturedToggle, button);
        }
    });
}
