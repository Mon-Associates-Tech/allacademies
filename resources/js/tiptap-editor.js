/**
 * TipTap Editor Module for Mock Exam Layout Editor
 *
 * Provides a floating toolbar with alignment, font size, bold, italic, and strikethrough
 * for contenteditable elements in the exam preview editor.
 *
 * Usage:
 *   import './tiptap-editor.js';
 *   // Exposes window.initTipTapEditor() for Alpine.js integration
 */

import { Editor } from '@tiptap/core';
import StarterKit from '@tiptap/starter-kit';
import TextAlign from '@tiptap/extension-text-align';
import FloatingMenu from '@tiptap/extension-floating-menu';

// ─── Custom Font Size Extension ─────────────────────────────────────────────

const FontSize = {
    name: 'fontSize',

    addOptions() {
        return {
            types: ['textStyle'],
        };
    },

    addGlobalAttributes() {
        return [
            {
                types: this.options.types,
                attributes: {
                    fontSize: {
                        type: String,
                        default: null,
                    },
                },
            },
        ];
    },
};

// ─── Editor Instance Management ─────────────────────────────────────────────

let currentEditor = null;
let floatingMenuEl = null;
let allEditors = [];

/**
 * Initialize the TipTap editor on a contenteditable element.
 * Called from Alpine.js when edit mode is activated.
 *
 * @param {HTMLElement} element - The contenteditable element
 * @param {Object} callbacks - { onContentChange: (html, elementId) => void }
 */
export function initTipTapEditor(element, callbacks = {}) {
    console.log('[TipTap] initTipTapEditor called on element:', element, 'tag:', element.tagName, 'class:', element.className);
    if (!element) {
        console.warn('[TipTap] Element is null, skipping initialization');
        return null;
    }

    // Destroy existing editor on this element
    if (element._tiptapEditor) {
        console.log('[TipTap] Destroying existing editor on element');
        element._tiptapEditor.destroy();
    }

    // IMPORTANT: Remove contenteditable attribute so TipTap can manage it
    const hadContentEditable = element.getAttribute('contenteditable');
    if (hadContentEditable) {
        element.removeAttribute('contenteditable');
        console.log('[TipTap] Removed pre-set contenteditable attribute');
    }

    const initialContent = element.innerHTML || '';
    const elementId = element.dataset.sectionId || element.id || '';
    console.log('[TipTap] Initial content:', initialContent.substring(0, 100));

    let editor;
    try {
        editor = new Editor({
            element: element,
            content: initialContent,
            editable: true,
            extensions: [
                StarterKit.configure({
                    // Disable heading, horizontal rule, etc. for exam context
                    heading: false,
                    horizontalRule: false,
                    blockquote: false,
                    codeBlock: false,
                }),
                TextAlign.configure({
                    types: ['paragraph'],
                    defaultAlign: 'left',
                }),
                FontSize,
            ],
            editorProps: {
                attributes: {
                    class: 'layout-editor-content',
                },
            },
            onUpdate: ({ editor }) => {
                if (callbacks.onContentChange) {
                    callbacks.onContentChange(editor.getHTML(), elementId);
                }
            },
        });
        console.log('[TipTap] Editor created successfully for element:', elementId || element.className);
        console.log('[TipTap] Editor isEditable:', editor.isEditable, 'isFocused:', editor.isFocused);
    } catch (err) {
        console.error('[TipTap] Failed to create editor:', err);
        // Restore contenteditable if it was there
        if (hadContentEditable) {
            element.setAttribute('contenteditable', hadContentEditable);
        }
        return null;
    }

    element._tiptapEditor = editor;
    allEditors.push(editor);
    currentEditor = editor;

    // Create a single shared floating menu for all editors
    if (!floatingMenuEl) {
        console.log('[TipTap] Creating shared floating menu...');
        createSharedFloatingMenu();
        console.log('[TipTap] Floating menu created:', floatingMenuEl);
    }

    return editor;
}

/**
 * Find the active editor based on current selection.
 */
function getActiveEditor() {
    const selection = window.getSelection();
    if (!selection || selection.rangeCount === 0) return null;

    const node = selection.anchorNode;
    const element = node.nodeType === Node.ELEMENT_NODE ? node : node.parentElement;
    if (!element) return null;

    // Walk up to find the TipTap editor element
    let current = element;
    while (current) {
        if (current._tiptapEditor && current._tiptapEditor.isFocused) {
            return current._tiptapEditor;
        }
        current = current.parentElement;
    }

    // Fallback: find any focused editor
    for (const editor of allEditors) {
        if (editor.isFocused) return editor;
    }
    return null;
}

/**
 * Create a single shared floating menu for all editors.
 */
function createSharedFloatingMenu() {
    // Remove existing floating menu
    if (floatingMenuEl) {
        floatingMenuEl.remove();
        floatingMenuEl = null;
    }

    const menu = document.createElement('div');
    menu.className = 'tiptap-floating-menu';
    menu.style.cssText = `
        position: absolute;
        display: none;
        gap: 2px;
        padding: 4px;
        background: #1e293b;
        border-radius: 6px;
        box-shadow: 0 4px 12px rgba(0,0,0,0.3);
        z-index: 10000;
        align-items: center;
    `;

    // Button template factory
    const createBtn = (icon, title, action) => {
        const btn = document.createElement('button');
        btn.type = 'button';
        btn.className = 'tip-btn';
        btn.title = title;
        btn.innerHTML = icon;
        btn.style.cssText = `
            width: 28px;
            height: 28px;
            display: flex;
            align-items: center;
            justify-content: center;
            border: none;
            background: transparent;
            color: #cbd5e1;
            border-radius: 4px;
            cursor: pointer;
            font-size: 13px;
            transition: all 0.15s;
        `;
        btn.onmouseenter = () => { btn.style.background = '#334155'; btn.style.color = '#f1f5f9'; };
        btn.onmouseleave = () => {
            if (!btn.classList.contains('tip-btn-active')) {
                btn.style.background = 'transparent';
                btn.style.color = '#cbd5e1';
            }
        };
        btn.onclick = (e) => {
            e.preventDefault();
            const activeEditor = getActiveEditor();
            if (activeEditor) {
                action(activeEditor);
            }
        };
        return btn;
    };

    // Bold
    menu.appendChild(createBtn(
        '<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><path d="M6 4h8a4 4 0 0 1 4 4 4 4 0 0 1-4 4H6z"/><path d="M6 12h9a4 4 0 0 1 4 4 4 4 0 0 1-4 4H6z"/></svg>',
        'Bold (Ctrl+B)',
        (ed) => ed.chain().focus().toggleBold().run()
    ));

    // Italic
    menu.appendChild(createBtn(
        '<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="19" y1="4" x2="10" y2="4"/><line x1="14" y1="20" x2="5" y2="20"/><line x1="15" y1="4" x2="9" y2="20"/></svg>',
        'Italic (Ctrl+I)',
        (ed) => ed.chain().focus().toggleItalic().run()
    ));

    // Strikethrough
    menu.appendChild(createBtn(
        '<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="4" y1="12" x2="20" y2="12"/><path d="M17.5 7.5c0-2-1.5-3.5-5.5-3.5S6 5.5 6 7.5c0 4 12 4 12 8 0 2-2 3.5-6 3.5s-6-1.5-6-3.5"/></svg>',
        'Strikethrough',
        (ed) => ed.chain().focus().toggleStrike().run()
    ));

    // Separator
    const sep1 = document.createElement('div');
    sep1.style.cssText = 'width: 1px; height: 20px; background: #475569; margin: 0 2px;';
    menu.appendChild(sep1);

    // Align Left
    menu.appendChild(createBtn(
        '<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="3" y1="6" x2="21" y2="6"/><line x1="3" y1="12" x2="15" y2="12"/><line x1="3" y1="18" x2="18" y2="18"/></svg>',
        'Align Left',
        (ed) => ed.chain().focus().setTextAlign('left').run()
    ));

    // Align Center
    menu.appendChild(createBtn(
        '<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="2" y1="6" x2="22" y2="6"/><line x1="5" y1="12" x2="19" y2="12"/><line x1="4" y1="18" x2="20" y2="18"/></svg>',
        'Align Center',
        (ed) => ed.chain().focus().setTextAlign('center').run()
    ));

    // Align Right
    menu.appendChild(createBtn(
        '<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="3" y1="6" x2="21" y2="6"/><line x1="9" y1="12" x2="21" y2="12"/><line x1="6" y1="18" x2="21" y2="18"/></svg>',
        'Align Right',
        (ed) => ed.chain().focus().setTextAlign('right').run()
    ));

    // Align Justify
    menu.appendChild(createBtn(
        '<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="3" y1="6" x2="21" y2="6"/><line x1="3" y1="12" x2="21" y2="12"/><line x1="3" y1="18" x2="21" y2="18"/></svg>',
        'Align Justify',
        (ed) => ed.chain().focus().setTextAlign('justify').run()
    ));

    // Separator
    const sep2 = document.createElement('div');
    sep2.style.cssText = 'width: 1px; height: 20px; background: #475569; margin: 0 2px;';
    menu.appendChild(sep2);

    // Font size dropdown
    const fontSizeWrapper = document.createElement('div');
    fontSizeWrapper.style.cssText = 'position: relative;';

    const fontSizeBtn = document.createElement('button');
    fontSizeBtn.type = 'button';
    fontSizeBtn.innerHTML = '<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 7V4h16v3"/><line x1="12" y1="4" x2="12" y2="20"/><line x1="8" y1="20" x2="16" y2="20"/></svg>';
    fontSizeBtn.title = 'Font Size';
    fontSizeBtn.style.cssText = `
        width: 28px; height: 28px; display: flex; align-items: center; justify-content: center;
        border: none; background: transparent; color: #cbd5e1; border-radius: 4px;
        cursor: pointer; font-size: 13px;
    `;

    const fontSizeDropdown = document.createElement('div');
    fontSizeDropdown.style.cssText = `
        position: absolute; top: 100%; left: 0; display: none; flex-direction: column;
        padding: 4px; background: #1e293b; border-radius: 6px; box-shadow: 0 4px 12px rgba(0,0,0,0.3);
        z-index: 10001; min-width: 80px;
    `;

    const fontSizes = ['8pt', '9pt', '10pt', '10.5pt', '11pt', '12pt', '13pt', '14pt', '16pt', '18pt', '20pt', '22pt'];
    fontSizes.forEach(size => {
        const sizeBtn = document.createElement('button');
        sizeBtn.type = 'button';
        sizeBtn.textContent = size;
        sizeBtn.style.cssText = `
            padding: 4px 8px; border: none; background: transparent; color: #cbd5e1;
            text-align: left; cursor: pointer; font-size: 12px; border-radius: 3px;
        `;
        sizeBtn.onmouseenter = () => { sizeBtn.style.background = '#334155'; };
        sizeBtn.onmouseleave = () => { sizeBtn.style.background = 'transparent'; };
        sizeBtn.onclick = (e) => {
            e.stopPropagation();
            const activeEditor = getActiveEditor();
            if (activeEditor) {
                activeEditor.chain().focus().setTextStyle({ fontSize: size }).run();
            }
            fontSizeDropdown.style.display = 'none';
        };
        fontSizeDropdown.appendChild(sizeBtn);
    });

    fontSizeBtn.onclick = (e) => {
        e.stopPropagation();
        fontSizeDropdown.style.display = fontSizeDropdown.style.display === 'flex' ? 'none' : 'flex';
    };

    fontSizeWrapper.appendChild(fontSizeBtn);
    fontSizeWrapper.appendChild(fontSizeDropdown);
    menu.appendChild(fontSizeWrapper);

    document.body.appendChild(menu);
    floatingMenuEl = menu;

    // Position the floating menu on selection change (shared across all editors)
    const updateMenuPosition = () => {
        const activeEditor = getActiveEditor();
        if (!activeEditor) {
            menu.style.display = 'none';
            return;
        }

        const { state } = activeEditor;
        const { empty } = state.selection;

        if (empty) {
            menu.style.display = 'none';
            return;
        }

        // Get selection bounds
        const selection = window.getSelection();
        if (selection && selection.rangeCount > 0) {
            const range = selection.getRangeAt(0);
            const rect = range.getBoundingClientRect();

            menu.style.display = 'flex';
            menu.style.top = `${rect.top - menu.offsetHeight - 8 + window.scrollY}px`;
            menu.style.left = `${rect.left + (rect.width / 2) - (menu.offsetWidth / 2)}px`;
        }
    };

    // Listen for selection changes (single listener for all editors)
    document.addEventListener('selectionchange', updateMenuPosition);
    console.log('[TipTap] selectionchange listener attached');

    // Also listen on window level for iframe compatibility
    window.addEventListener('selectionchange', updateMenuPosition);
    console.log('[TipTap] window selectionchange listener attached');

    // Close dropdown on outside click
    document.addEventListener('click', (e) => {
        if (!fontSizeWrapper.contains(e.target)) {
            fontSizeDropdown.style.display = 'none';
        }
    });
}

/**
 * Destroy all TipTap editors in the current container.
 */
export function destroyTipTapEditors(container) {
    // Destroy all tracked editors
    allEditors.forEach(editor => {
        if (editor && !editor.isDestroyed) {
            editor.destroy();
        }
    });
    allEditors = [];

    // Clean up element references
    const editableElements = container.querySelectorAll('[contenteditable="true"]');
    editableElements.forEach(el => {
        if (el._tiptapEditor) {
            el._tiptapEditor = null;
        }
    });

    if (floatingMenuEl) {
        floatingMenuEl.remove();
        floatingMenuEl = null;
    }
    currentEditor = null;
}

/**
 * Make functions available globally for Alpine.js integration.
 */
window.initTipTapEditor = initTipTapEditor;
window.destroyTipTapEditors = destroyTipTapEditors;
