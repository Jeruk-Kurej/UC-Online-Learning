// --- Paginated lists that reload over AJAX ---
// Links inside .pagination-ajax raise an `ajax-pagination` event instead of navigating;
// the list page listens for it and fetches the new page.

export default function registerAjaxPagination() {
    document.addEventListener('click', (event) => {
        const link = event.target.closest('.pagination-ajax a');

        if (link) {
            event.preventDefault();
            window.dispatchEvent(new CustomEvent('ajax-pagination', { detail: { url: link.href } }));
        }
    });
}
