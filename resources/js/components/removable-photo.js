// --- Photo field that can be replaced, removed or restored before saving ---
// x-data="removablePhoto(@js($currentPhotoUrl))"; the file input is x-ref="photoInput".

export default function removablePhoto(initialUrl = '') {
    return {
        initialUrl: initialUrl || '',
        previewUrl: initialUrl || '',
        hasPhoto: Boolean(initialUrl),
        photoDeleted: false,
        newPhotoSelected: false,

        pick(event) {
            const [file] = event.target.files;
            if (file) {
                this.previewUrl = URL.createObjectURL(file);
                this.hasPhoto = true;
                this.photoDeleted = false;
                this.newPhotoSelected = true;
            }
        },

        cancelNew() {
            this.$refs.photoInput.value = '';
            this.newPhotoSelected = false;
            this.hasPhoto = Boolean(this.initialUrl);
            this.photoDeleted = false;
            this.previewUrl = this.initialUrl;
        },
    };
}
