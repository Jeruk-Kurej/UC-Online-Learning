// --- Activity documentation files: mark existing ones for deletion, preview new ones ---
// x-data="activityFiles(@js($user->activity_files))"; the file input is x-ref="activityInput".
// Used by the admin user edit form and the profile page.

export default function activityFiles(existingFiles = []) {
    return {
        existingFiles,
        deletedFiles: [],
        newFiles: [],

        deleteFile(url) {
            if (!this.deletedFiles.includes(url)) {
                this.deletedFiles.push(url);
            }
        },

        undoDeleteFile(url) {
            this.deletedFiles = this.deletedFiles.filter(item => item !== url);
        },

        isDeleted(url) {
            return this.deletedFiles.includes(url);
        },

        pickFiles() {
            this.$refs.activityInput.click();
        },

        clearNewFiles() {
            this.newFiles = [];
            this.$refs.activityInput.value = '';
        },

        handleNewFiles(event) {
            const files = Array.from(event.target.files);
            this.newFiles = [];
            files.forEach(file => {
                const fileObj = {
                    name: file.name,
                    size: (file.size / 1024 / 1024).toFixed(2) + ' MB',
                    type: file.type,
                    preview: null
                };
                if (file.type.startsWith('image/')) {
                    const reader = new FileReader();
                    reader.onload = (e) => {
                        fileObj.preview = e.target.result;
                    };
                    reader.readAsDataURL(file);
                }
                this.newFiles.push(fileObj);
            });
        },
    };
}
