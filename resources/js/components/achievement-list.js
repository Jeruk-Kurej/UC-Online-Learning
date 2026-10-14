// --- Achievements on a work profile, deletable by their owner ---
// x-data="achievementList(@js($achievements), @js($deleteUrl))"

export default function achievementList(achievements, deleteUrl) {
    return {
        achievements,
        loading: false,

        deleteAchievement(index) {
            if (!confirm('Are you sure you want to delete this achievement?')) return;
            this.loading = true;
            fetch(deleteUrl, {
                method: 'DELETE',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ index: index })
            })
            .then(response => {
                if (!response.ok) {
                    return response.json().then(err => { throw err; });
                }
                return response.json();
            })
            .then(data => {
                if (data.success) {
                    this.achievements = data.achievements;
                    this.$dispatch('notify', { message: 'Achievement deleted successfully!', type: 'success' });
                } else {
                    this.$dispatch('notify', { message: data.message || 'Failed to delete achievement.', type: 'error' });
                }
            })
            .catch(error => {
                console.error(error);
                this.$dispatch('notify', { message: 'An error occurred while deleting.', type: 'error' });
            })
            .finally(() => {
                this.loading = false;
            });
        },
    };
}
