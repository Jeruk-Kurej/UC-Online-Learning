// --- File field shown as a drop box until a file is picked, then as a preview that can be cleared ---
// x-data="fileDrop()"; the file input is x-ref="fileInput".

export default function fileDrop() {
    return {
        selected: false,
        fileName: '',
        fileUrl: '',

        pick(event) {
            const [file] = event.target.files;
            if (file) {
                this.selected = true;
                this.fileName = file.name;
                this.fileUrl = URL.createObjectURL(file);
            }
        },

        clear() {
            this.$refs.fileInput.value = '';
            this.selected = false;
            this.fileName = '';
            this.fileUrl = '';
        },
    };
}
