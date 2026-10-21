// --- Achievements on a work profile, deletable by their owner ---
// x-data="achievementList(@js($achievements), @js($deleteUrl))"

import { readJsonOrThrow, sendJson } from '../lib/json-request';

export default function achievementList(achievements, deleteUrl) {
    return {
        achievements,
        loading: false,

        deleteAchievement(index) {
            if (!confirm('Are you sure you want to delete this achievement?')) return;
            this.loading = true;
            sendJson(deleteUrl, 'DELETE', { index: index, text: this.achievements[index] })
                .then(readJsonOrThrow)
                .then((data) => {
                    if (data.success) {
                        this.achievements = data.achievements;
                        this.$dispatch('notify', { message: 'Achievement deleted successfully!', type: 'success' });
                    } else {
                        if (Array.isArray(data.achievements)) {
                            this.achievements = data.achievements;
                        }
                        this.$dispatch('notify', {
                            message: data.message || 'Failed to delete achievement.',
                            type: 'error',
                        });
                    }
                })
                .catch((error) => {
                    console.error(error);
                    this.$dispatch('notify', { message: 'An error occurred while deleting.', type: 'error' });
                })
                .finally(() => {
                    this.loading = false;
                });
        },
    };
}
