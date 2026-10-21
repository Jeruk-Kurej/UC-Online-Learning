// --- Photo field that can be replaced, removed or restored before saving ---
// x-data="removablePhoto(@js($currentPhotoUrl), @js($hasRealPhoto))"; the file input is x-ref="photoInput".
// The second argument covers URLs that are only a generated placeholder (they start as "no photo").

export default function removablePhoto(initialUrl = '', hasInitialPhoto = null) {
    return {
        initialUrl: initialUrl || '',
        initialHasPhoto: hasInitialPhoto ?? Boolean(initialUrl),
        previewUrl: initialUrl || '',
        hasPhoto: hasInitialPhoto ?? Boolean(initialUrl),
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
            this.hasPhoto = this.initialHasPhoto;
            this.photoDeleted = false;
            this.previewUrl = this.initialUrl;
        },
    };
}
