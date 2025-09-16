window.Modal = {
    /**
     * Open a modal
     * @param {string|Object} nameOrData - Modal name or data object
     * @param {Object} data - Additional data if first param is string
     */
    open(nameOrData, data = {}) {
        if (typeof nameOrData === 'string') {
            data.name = nameOrData;
        } else {
            data = nameOrData;
        }

        // Ensure name is never undefined, set to empty string if not provided
        data.name = data.name || '';

        window.dispatchEvent(new CustomEvent('open-modal', { detail: data }));
    },

    /**
     * Close a modal
     * @param {string} name - Modal name (optional)
     */
    close(name = '') {
        window.dispatchEvent(new CustomEvent('close-modal', { detail: { name: name || '' } }));
    },

    /**
     * Toggle a modal
     * @param {string} name - Modal name (optional)
     */
    toggle(name = '') {
        window.dispatchEvent(new CustomEvent('toggle-modal', { detail: { name: name || '' } }));
    }
};
// Global functions for backward compatibility
window.openModal = window.Modal.open;
window.closeModal = window.Modal.close;
window.toggleModal = window.Modal.toggle;
