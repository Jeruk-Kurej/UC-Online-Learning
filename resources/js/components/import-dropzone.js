// --- Drag-and-drop file picker of the import modals ---
// x-data="importDropzone('file-input-id', 'file-name-label-id')"; the input uses @change="showChosenFile".

export default function importDropzone(inputId, labelId) {
    return {
        isDragging: false,

        handleDragOver(event) {
            event.preventDefault();
            this.isDragging = true;
        },

        handleDragLeave() {
            this.isDragging = false;
        },

        handleDrop(event) {
            event.preventDefault();
            this.isDragging = false;

            if (event.dataTransfer.files.length > 0) {
                const file = event.dataTransfer.files[0];
                document.getElementById(inputId).files = event.dataTransfer.files;
                document.getElementById(labelId).textContent = file.name;
            }
        },

        showChosenFile(event) {
            document.getElementById(labelId).textContent = event.target.files[0].name;
        },
    };
}
