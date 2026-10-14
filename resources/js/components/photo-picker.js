// --- Single photo field with an instant preview ---
// x-data="photoPicker(@js($currentPhotoUrl))"; the hidden input is #photo.

export default function photoPicker(initialUrl = '') {
    return {
        previewUrl: initialUrl,
        hasPhoto: Boolean(initialUrl),

        handleFileChange(event) {
            const file = event.target.files[0];
            if (file) {
                this.previewUrl = URL.createObjectURL(file);
                this.hasPhoto = true;
            }
        },

        triggerUpload() {
            document.getElementById('photo').click();
        },
    };
}
