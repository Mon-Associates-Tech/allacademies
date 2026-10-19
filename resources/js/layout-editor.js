/**
 * Layout Editor Module for Mock Exam Preview
 *
 * Provides:
 * - Section drag-and-drop reordering via HTML5 drag API
 * - Image resize handles for attachment images
 * - Save Layout button that POSTs layout_config JSON to the backend
 * - Edit mode toggle that switches between view and edit modes
 *
 * Usage:
 *   import './layout-editor.js';
 *   // Exposes window.LayoutEditor for Alpine.js integration
 */

window.LayoutEditor = {
    /**
     * Collect all layout data from the editable preview and return as JSON.
     * @param {HTMLElement} container - The exam container element
     * @returns {Object} layout_config object
     */
    collectLayoutConfig(container) {
        const config = { sections: {} };
        const sections = container.querySelectorAll('.section-wrap[data-section-id]');

        sections.forEach((sectionEl, index) => {
            const sectionId = sectionEl.dataset.sectionId;
            const sectionConfig = {
                order: index + 1,
            };

            // Header - collect from the contenteditable element inside
            const header = sectionEl.querySelector('.section-header[data-editable="header"]');
            if (header) {
                const headerEditable = header.querySelector('[contenteditable="true"]') || header;
                sectionConfig.header = {
                    content: headerEditable.innerHTML,
                    textAlign: this.getElementStyle(headerEditable, 'text-align'),
                    fontSize: this.getElementStyle(headerEditable, 'font-size'),
                };
            }

            // Instructions - collect from the contenteditable element inside
            const instructions = sectionEl.querySelector('.section-instructions[data-editable="instructions"]');
            if (instructions) {
                const instructionsEditable = instructions.querySelector('[contenteditable="true"]') || instructions;
                sectionConfig.instructions = {
                    content: instructionsEditable.innerHTML,
                    textAlign: this.getElementStyle(instructionsEditable, 'text-align'),
                    fontSize: this.getElementStyle(instructionsEditable, 'font-size'),
                };
            }

            // Questions area
            const questionsArea = sectionEl.querySelector('.questions-area[data-editable="questions"]');
            if (questionsArea) {
                sectionConfig.questions = {
                    columns: parseInt(questionsArea.dataset.columns || '1', 10),
                };
            }

            // Attachment
            const attachment = sectionEl.querySelector('.section-attachment[data-editable="attachment"]');
            if (attachment) {
                const img = attachment.querySelector('img');
                sectionConfig.attachment = {
                    align: this.getElementStyle(attachment, 'text-align') || 'center',
                    width: img ? img.style.width || img.getAttribute('width') || '100%' : '100%',
                    height: img ? img.style.height || img.getAttribute('height') || 'auto' : 'auto',
                };
            }

            // Blank page
            const blankPage = sectionEl.querySelector('.blank-page[data-editable="blankPage"]');
            if (blankPage) {
                const textEl = blankPage.querySelector('.blank-page-text');
                sectionConfig.blankPage = {
                    content: textEl ? textEl.innerHTML : '',
                    textAlign: this.getElementStyle(blankPage, 'text-align') || 'center',
                    verticalAlign: this.getElementStyle(blankPage, 'align-items') || 'middle',
                    fontSize: textEl ? this.getElementStyle(textEl, 'font-size') : '16pt',
                };
            }

            config.sections[sectionId] = sectionConfig;
        });

        return config;
    },

    /**
     * Apply layout config to the preview elements.
     * @param {HTMLElement} container - The exam container element
     * @param {Object} config - layout_config JSON object
     */
    applyLayoutConfig(container, config) {
        if (!config || !config.sections) return;

        Object.entries(config.sections).forEach(([sectionId, sectionConfig]) => {
            const sectionEl = container.querySelector(`.section-wrap[data-section-id="${sectionId}"]`);
            if (!sectionEl) return;

            // Header
            if (sectionConfig.header) {
                const header = sectionEl.querySelector('.section-header[data-editable="header"]');
                if (header) {
                    if (sectionConfig.header.content) header.innerHTML = sectionConfig.header.content;
                    if (sectionConfig.header.textAlign) header.style.textAlign = sectionConfig.header.textAlign;
                    if (sectionConfig.header.fontSize) header.style.fontSize = sectionConfig.header.fontSize;
                }
            }

            // Instructions
            if (sectionConfig.instructions) {
                const instructions = sectionEl.querySelector('.section-instructions[data-editable="instructions"]');
                if (instructions) {
                    if (sectionConfig.instructions.content) instructions.innerHTML = sectionConfig.instructions.content;
                    if (sectionConfig.instructions.textAlign) instructions.style.textAlign = sectionConfig.instructions.textAlign;
                    if (sectionConfig.instructions.fontSize) instructions.style.fontSize = sectionConfig.instructions.fontSize;
                }
            }

            // Questions
            if (sectionConfig.questions) {
                const questionsArea = sectionEl.querySelector('.questions-area[data-editable="questions"]');
                if (questionsArea) {
                    questionsArea.dataset.columns = sectionConfig.questions.columns || '1';
                }
            }

            // Attachment
            if (sectionConfig.attachment) {
                const attachment = sectionEl.querySelector('.section-attachment[data-editable="attachment"]');
                if (attachment) {
                    if (sectionConfig.attachment.align) attachment.style.textAlign = sectionConfig.attachment.align;
                    const img = attachment.querySelector('img');
                    if (img) {
                        if (sectionConfig.attachment.width) img.style.width = sectionConfig.attachment.width;
                        if (sectionConfig.attachment.height) img.style.height = sectionConfig.attachment.height;
                    }
                }
            }

            // Blank page
            if (sectionConfig.blankPage) {
                const blankPage = sectionEl.querySelector('.blank-page[data-editable="blankPage"]');
                if (blankPage) {
                    if (sectionConfig.blankPage.textAlign) blankPage.style.textAlign = sectionConfig.blankPage.textAlign;
                    if (sectionConfig.blankPage.verticalAlign) blankPage.style.alignItems = sectionConfig.blankPage.verticalAlign;
                    const textEl = blankPage.querySelector('.blank-page-text');
                    if (textEl) {
                        if (sectionConfig.blankPage.content) textEl.innerHTML = sectionConfig.blankPage.content;
                        if (sectionConfig.blankPage.fontSize) textEl.style.fontSize = sectionConfig.blankPage.fontSize;
                    }
                }
            }
        });
    },

    /**
     * Save layout config to the backend.
     * @param {string} saveUrl - The POST endpoint URL
     * @param {Object} config - layout_config JSON object
     * @param {Function} onSuccess - Callback on success
     * @param {Function} onError - Callback on error
     */
    saveLayout(saveUrl, config, onSuccess, onError) {
        fetch(saveUrl, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '',
            },
            body: JSON.stringify({ layout_config: config }),
        })
            .then(response => {
                if (!response.ok) throw new Error('Network response was not ok');
                return response.json();
            })
            .then(data => {
                if (onSuccess) onSuccess(data);
            })
            .catch(error => {
                console.error('Save layout error:', error);
                if (onError) onError(error);
            });
    },

    /**
     * Initialize drag-and-drop for section reordering.
     * @param {HTMLElement} container - The exam container element
     */
    initSectionDragDrop(container) {
        const sections = container.querySelectorAll('.section-wrap[data-section-id]');
        let draggedSection = null;

        sections.forEach(section => {
            const dragHandle = section.querySelector('.section-drag-handle');
            if (!dragHandle) return;

            // Only make the drag handle draggable, not the entire section
            dragHandle.draggable = true;

            dragHandle.addEventListener('dragstart', (e) => {
                draggedSection = section;
                e.dataTransfer.effectAllowed = 'move';
                e.dataTransfer.setData('text/plain', section.dataset.sectionId);
                section.style.opacity = '0.5';
                section.classList.add('dragging');
            });

            dragHandle.addEventListener('dragend', () => {
                section.style.opacity = '1';
                section.classList.remove('dragging');
                sections.forEach(s => s.classList.remove('drag-over'));
                draggedSection = null;
            });

            // Only allow drop on drag handles, not the entire section
            dragHandle.addEventListener('dragover', (e) => {
                e.preventDefault();
                e.dataTransfer.dropEffect = 'move';
                section.classList.add('drag-over');
            });

            dragHandle.addEventListener('dragleave', () => {
                section.classList.remove('drag-over');
            });

            dragHandle.addEventListener('drop', (e) => {
                e.preventDefault();
                e.stopPropagation();
                section.classList.remove('drag-over');

                if (!draggedSection || draggedSection === section) return;

                // Reorder sections in DOM
                const allSections = Array.from(container.querySelectorAll('.section-wrap[data-section-id]'));
                const draggedIndex = allSections.indexOf(draggedSection);
                const targetIndex = allSections.indexOf(section);

                if (draggedIndex < targetIndex) {
                    section.after(draggedSection);
                } else {
                    section.before(draggedSection);
                }

                // Update order attributes
                allSections.forEach((s, i) => {
                    s.dataset.order = i + 1;
                });
            });
        });
    },

    /**
     * Initialize image resize handles.
     * @param {HTMLElement} container - The exam container element
     */
    initImageResize(container) {
        const images = container.querySelectorAll('.section-attachment img[data-resizable="true"]');

        images.forEach(img => {
            // Create resize handle
            const handle = document.createElement('div');
            handle.className = 'image-resize-handle';
            handle.style.cssText = `
                position: absolute;
                bottom: -4px;
                right: -4px;
                width: 12px;
                height: 12px;
                background: #3b82f6;
                border: 2px solid white;
                border-radius: 2px;
                cursor: nwse-resize;
                display: none;
                z-index: 100;
            `;

            const wrapper = document.createElement('div');
            wrapper.style.cssText = 'position: relative; display: inline-block;';
            img.parentNode.insertBefore(wrapper, img);
            wrapper.appendChild(img);
            wrapper.appendChild(handle);

            let isResizing = false;
            let startX, startY, startWidth, startHeight;

            handle.addEventListener('mousedown', (e) => {
                e.preventDefault();
                e.stopPropagation();
                isResizing = true;
                startX = e.clientX;
                startY = e.clientY;
                startWidth = parseInt(img.style.width || img.offsetWidth, 10);
                startHeight = parseInt(img.style.height || img.offsetHeight, 10);
                handle.style.display = 'block';

                const onMouseMove = (e) => {
                    if (!isResizing) return;
                    const dx = e.clientX - startX;
                    const dy = e.clientY - startY;

                    const newWidth = Math.max(50, startWidth + dx);
                    // Maintain aspect ratio
                    const aspectRatio = startHeight / startWidth;
                    const newHeight = Math.max(30, newWidth * aspectRatio);

                    img.style.width = `${newWidth}px`;
                    img.style.height = `${newHeight}px`;
                };

                const onMouseUp = () => {
                    isResizing = false;
                    document.removeEventListener('mousemove', onMouseMove);
                    document.removeEventListener('mouseup', onMouseUp);
                };

                document.addEventListener('mousemove', onMouseMove);
                document.addEventListener('mouseup', onMouseUp);
            });

            // Show handle on hover in edit mode
            wrapper.addEventListener('mouseenter', () => {
                if (container.dataset.editMode === 'true') {
                    handle.style.display = 'block';
                }
            });
            wrapper.addEventListener('mouseleave', () => {
                if (!isResizing) {
                    handle.style.display = 'none';
                }
            });
        });
    },

    /**
     * Helper: get computed style property.
     */
    getElementStyle(element, property) {
        return window.getComputedStyle(element).getPropertyValue(property).trim();
    },
};
