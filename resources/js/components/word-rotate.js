// --- Word that swaps for the next one every 2.3 s, with a short exit animation first ---
// x-data="wordRotate(words)"; the markup reads words[currentWord] and isAnimating.

export default function wordRotate(words) {
    return {
        words,
        currentWord: 0,
        isAnimating: false,

        init() {
            setInterval(() => {
                this.isAnimating = true;
                setTimeout(() => {
                    this.currentWord = (this.currentWord + 1) % this.words.length;
                    this.isAnimating = false;
                }, 300);
            }, 2300);
        },
    };
}
