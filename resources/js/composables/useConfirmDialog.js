import { ref } from 'vue'

const confirmDialogRef = ref(null)

export const useConfirmDialog = () => {
    const setDialogRef = (ref) => {
        confirmDialogRef.value = ref
    }

    const confirm = async (options = {}) => {
        if (!confirmDialogRef.value) {
            console.error('Confirm dialog component not found. Make sure to add it to your App.vue')
            return false
        }

        // Pass options directly to the open method
        return await confirmDialogRef.value.open(options)
    }

    return {
        setDialogRef,
        confirm
    }
}