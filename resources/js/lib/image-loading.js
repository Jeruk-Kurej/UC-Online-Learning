// --- Thumbnails still downloading get a grey pulse (data-loading), so "loading" is not mistaken for "no photo", which keeps its orange letter ---

const THUMB = 'img[data-thumb]';

export default function registerImageLoading() {
    const markWhilePending = (img) => {
        if (img.complete) {
            return;
        }

        const done = () => img.removeAttribute('data-loading');

        img.setAttribute('data-loading', '');
        img.addEventListener('load', done, { once: true });
        img.addEventListener('error', done, { once: true });
    };

    const scan = (root) => {
        if (root.matches(THUMB)) {
            markWhilePending(root);
        }

        root.querySelectorAll(THUMB).forEach(markWhilePending);
    };

    scan(document.body);

    new MutationObserver((records) => {
        records.forEach((record) => record.addedNodes.forEach((node) => node instanceof Element && scan(node)));
    }).observe(document.body, { childList: true, subtree: true });
}
