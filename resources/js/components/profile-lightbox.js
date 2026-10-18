// --- Full-screen image viewer of the user profile page ---
// x-data="profileLightbox()" on the page; photos call open(url), the viewer reads showLightbox and lightboxUrl.

export default function profileLightbox() {
    return {
        showLightbox: false,
        lightboxUrl: '',

        open(url) {
            this.lightboxUrl = url;
            this.showLightbox = true;
        },
    };
}
