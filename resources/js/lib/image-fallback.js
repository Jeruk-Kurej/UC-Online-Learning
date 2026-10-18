// --- Thumbnails whose file is missing or broken drop the image so the initial behind it shows ---

export default function registerImageFallback() {
    const dropIfBroken = (img) => {
        if (img.complete && img.naturalWidth === 0) {
            img.remove();
        }
    };

    document.addEventListener(
        'error',
        (event) => {
            if (event.target instanceof HTMLImageElement && event.target.hasAttribute('data-thumb')) {
                event.target.remove();
            }
        },
        true,
    );

    document.querySelectorAll('img[data-thumb]').forEach(dropIfBroken);
}
