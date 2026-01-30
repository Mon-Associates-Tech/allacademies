@once
    @push('head')
        <script src="{{ asset('js/tinymce/tinymce.js') }}" referrerpolicy="origin"></script>
        <script>
            // Define the function globally before Alpine loads
            window.markdownEditor = function(initialMarkdown, editorId, height, wireName) {
                return {
                    preview: false,
                    markdown: initialMarkdown,
                    previewHtml: '',
                    editor: null,
                    editorId: editorId,
                    wireName: wireName,
                    initialized: false,
                    isInitializing: true,

                    initEditor() {
                        if (this.initialized || this.editor) return;

                        // Wait for TinyMCE to be available
                        if (typeof tinymce === 'undefined') {
                            console.error('TinyMCE not loaded');
                            return;
                        }

                        this.$nextTick(() => {
                            const editorElement = document.getElementById(this.editorId);
                            if (!editorElement) {
                                console.error('Editor element not found:', this.editorId);
                                return;
                            }

                            // Detect dark mode
                            const isDarkMode = document.documentElement.classList.contains('dark');

                            tinymce.init({
                                selector: '#' + this.editorId,
                                height: height,
                                menubar: false,
                                plugins: 'lists link image code autoresize',
                                toolbar: 'undo redo | bold italic strikethrough | blocks | bullist numlist | link image code | mathsymbols',
                                toolbar_mode: 'sliding',
                                block_formats: 'Paragraph=p; Heading 1=h1; Heading 2=h2; Heading 3=h3',
                                forced_root_block: 'p',
                                skin: isDarkMode ? 'oxide-dark' : 'oxide',
                                content_css: isDarkMode ? 'dark' : 'default',
                                content_style: isDarkMode
                                    ? 'body { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Helvetica, Arial, sans-serif; font-size: 14px; line-height: 1.5; background-color: #1f2937; color: #f3f4f6; } p { margin: 0 0 16px; } img { max-width: 100%; height: auto; }'
                                    : 'body { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Helvetica, Arial, sans-serif; font-size: 14px; line-height: 1.5; } p { margin: 0 0 16px; } img { max-width: 100%; height: auto; }',
                                paste_as_text: false,
                                promotion: false,
                                branding: false,
                                relative_urls: false,
                                remove_script_host: false,
                                setup: (editor) => {
                                    // Math symbols organized by category
                                    const mathSymbols = {
                                        'Basic Operations': [
                                            { text: '+ Plus', value: '+' },
                                            { text: '− Minus', value: '−' },
                                            { text: '× Multiply', value: '×' },
                                            { text: '÷ Divide', value: '÷' },
                                            { text: '± Plus-Minus', value: '±' },
                                            { text: '∓ Minus-Plus', value: '∓' },
                                            { text: '= Equals', value: '=' },
                                            { text: '≠ Not Equal', value: '≠' },
                                            { text: '≈ Approximately', value: '≈' },
                                            { text: '≡ Identical', value: '≡' },
                                            { text: '≢ Not Identical', value: '≢' },
                                            { text: '∝ Proportional', value: '∝' },
                                        ],
                                        'Comparison': [
                                            { text: '< Less Than', value: '<' },
                                            { text: '> Greater Than', value: '>' },
                                            { text: '≤ Less or Equal', value: '≤' },
                                            { text: '≥ Greater or Equal', value: '≥' },
                                            { text: '≪ Much Less', value: '≪' },
                                            { text: '≫ Much Greater', value: '≫' },
                                            { text: '≮ Not Less', value: '≮' },
                                            { text: '≯ Not Greater', value: '≯' },
                                            { text: '≰ Not Less or Equal', value: '≰' },
                                            { text: '≱ Not Greater or Equal', value: '≱' },
                                        ],
                                        'Algebra': [
                                            { text: '√ Square Root', value: '√' },
                                            { text: '∛ Cube Root', value: '∛' },
                                            { text: '∜ Fourth Root', value: '∜' },
                                            { text: '² Squared', value: '²' },
                                            { text: '³ Cubed', value: '³' },
                                            { text: 'ⁿ Power n', value: 'ⁿ' },
                                            { text: '⁰ Power 0', value: '⁰' },
                                            { text: '¹ Power 1', value: '¹' },
                                            { text: '⁴ Power 4', value: '⁴' },
                                            { text: '⁵ Power 5', value: '⁵' },
                                            { text: '⁶ Power 6', value: '⁶' },
                                            { text: '⁷ Power 7', value: '⁷' },
                                            { text: '⁸ Power 8', value: '⁸' },
                                            { text: '⁹ Power 9', value: '⁹' },
                                            { text: '₀ Subscript 0', value: '₀' },
                                            { text: '₁ Subscript 1', value: '₁' },
                                            { text: '₂ Subscript 2', value: '₂' },
                                            { text: '₃ Subscript 3', value: '₃' },
                                            { text: '₄ Subscript 4', value: '₄' },
                                            { text: '₅ Subscript 5', value: '₅' },
                                            { text: '₆ Subscript 6', value: '₆' },
                                            { text: '₇ Subscript 7', value: '₇' },
                                            { text: '₈ Subscript 8', value: '₈' },
                                            { text: '₉ Subscript 9', value: '₉' },
                                            { text: '| Absolute', value: '|' },
                                            { text: '‖ Norm', value: '‖' },
                                        ],
                                        'Calculus': [
                                            { text: '∫ Integral', value: '∫' },
                                            { text: '∬ Double Integral', value: '∬' },
                                            { text: '∭ Triple Integral', value: '∭' },
                                            { text: '∮ Contour Integral', value: '∮' },
                                            { text: '∯ Surface Integral', value: '∯' },
                                            { text: '∰ Volume Integral', value: '∰' },
                                            { text: '∂ Partial', value: '∂' },
                                            { text: '∇ Nabla/Del', value: '∇' },
                                            { text: '∆ Delta/Laplacian', value: '∆' },
                                            { text: 'ℓ Script l', value: 'ℓ' },
                                            { text: '∞ Infinity', value: '∞' },
                                            { text: 'lim Limit', value: 'lim' },
                                            { text: '′ Prime', value: '′' },
                                            { text: '″ Double Prime', value: '″' },
                                            { text: '‴ Triple Prime', value: '‴' },
                                            { text: 'ḟ f dot', value: 'ḟ' },
                                            { text: 'ẍ x double dot', value: 'ẍ' },
                                        ],
                                        'Set Theory': [
                                            { text: '∈ Element of', value: '∈' },
                                            { text: '∉ Not Element', value: '∉' },
                                            { text: '∋ Contains', value: '∋' },
                                            { text: '∌ Not Contains', value: '∌' },
                                            { text: '⊂ Subset', value: '⊂' },
                                            { text: '⊃ Superset', value: '⊃' },
                                            { text: '⊆ Subset or Equal', value: '⊆' },
                                            { text: '⊇ Superset or Equal', value: '⊇' },
                                            { text: '⊄ Not Subset', value: '⊄' },
                                            { text: '⊅ Not Superset', value: '⊅' },
                                            { text: '∪ Union', value: '∪' },
                                            { text: '∩ Intersection', value: '∩' },
                                            { text: '∅ Empty Set', value: '∅' },
                                            { text: '∖ Set Minus', value: '∖' },
                                            { text: '⊕ XOR/Direct Sum', value: '⊕' },
                                            { text: '⊗ Tensor Product', value: '⊗' },
                                            { text: '⊖ Symmetric Diff', value: '⊖' },
                                        ],
                                        'Logic': [
                                            { text: '∧ And', value: '∧' },
                                            { text: '∨ Or', value: '∨' },
                                            { text: '¬ Not', value: '¬' },
                                            { text: '⊻ XOR', value: '⊻' },
                                            { text: '⊼ NAND', value: '⊼' },
                                            { text: '⊽ NOR', value: '⊽' },
                                            { text: '→ Implies', value: '→' },
                                            { text: '← Implied by', value: '←' },
                                            { text: '↔ If and only if', value: '↔' },
                                            { text: '⇒ Double Implies', value: '⇒' },
                                            { text: '⇐ Double Implied', value: '⇐' },
                                            { text: '⇔ Equivalent', value: '⇔' },
                                            { text: '∀ For All', value: '∀' },
                                            { text: '∃ Exists', value: '∃' },
                                            { text: '∄ Not Exists', value: '∄' },
                                            { text: '∴ Therefore', value: '∴' },
                                            { text: '∵ Because', value: '∵' },
                                            { text: '⊢ Proves', value: '⊢' },
                                            { text: '⊨ Models', value: '⊨' },
                                            { text: '⊤ True', value: '⊤' },
                                            { text: '⊥ False/Perpendicular', value: '⊥' },
                                        ],
                                        'Greek Letters': [
                                            { text: 'α Alpha', value: 'α' },
                                            { text: 'β Beta', value: 'β' },
                                            { text: 'γ Gamma', value: 'γ' },
                                            { text: 'δ Delta', value: 'δ' },
                                            { text: 'ε Epsilon', value: 'ε' },
                                            { text: 'ζ Zeta', value: 'ζ' },
                                            { text: 'η Eta', value: 'η' },
                                            { text: 'θ Theta', value: 'θ' },
                                            { text: 'ι Iota', value: 'ι' },
                                            { text: 'κ Kappa', value: 'κ' },
                                            { text: 'λ Lambda', value: 'λ' },
                                            { text: 'μ Mu', value: 'μ' },
                                            { text: 'ν Nu', value: 'ν' },
                                            { text: 'ξ Xi', value: 'ξ' },
                                            { text: 'ο Omicron', value: 'ο' },
                                            { text: 'π Pi', value: 'π' },
                                            { text: 'ρ Rho', value: 'ρ' },
                                            { text: 'σ Sigma', value: 'σ' },
                                            { text: 'τ Tau', value: 'τ' },
                                            { text: 'υ Upsilon', value: 'υ' },
                                            { text: 'φ Phi', value: 'φ' },
                                            { text: 'χ Chi', value: 'χ' },
                                            { text: 'ψ Psi', value: 'ψ' },
                                            { text: 'ω Omega', value: 'ω' },
                                            { text: 'Α Alpha (upper)', value: 'Α' },
                                            { text: 'Β Beta (upper)', value: 'Β' },
                                            { text: 'Γ Gamma (upper)', value: 'Γ' },
                                            { text: 'Δ Delta (upper)', value: 'Δ' },
                                            { text: 'Ε Epsilon (upper)', value: 'Ε' },
                                            { text: 'Ζ Zeta (upper)', value: 'Ζ' },
                                            { text: 'Η Eta (upper)', value: 'Η' },
                                            { text: 'Θ Theta (upper)', value: 'Θ' },
                                            { text: 'Ι Iota (upper)', value: 'Ι' },
                                            { text: 'Κ Kappa (upper)', value: 'Κ' },
                                            { text: 'Λ Lambda (upper)', value: 'Λ' },
                                            { text: 'Μ Mu (upper)', value: 'Μ' },
                                            { text: 'Ν Nu (upper)', value: 'Ν' },
                                            { text: 'Ξ Xi (upper)', value: 'Ξ' },
                                            { text: 'Ο Omicron (upper)', value: 'Ο' },
                                            { text: 'Π Pi (upper)', value: 'Π' },
                                            { text: 'Ρ Rho (upper)', value: 'Ρ' },
                                            { text: 'Σ Sigma (upper)', value: 'Σ' },
                                            { text: 'Τ Tau (upper)', value: 'Τ' },
                                            { text: 'Υ Upsilon (upper)', value: 'Υ' },
                                            { text: 'Φ Phi (upper)', value: 'Φ' },
                                            { text: 'Χ Chi (upper)', value: 'Χ' },
                                            { text: 'Ψ Psi (upper)', value: 'Ψ' },
                                            { text: 'Ω Omega (upper)', value: 'Ω' },
                                        ],
                                        'Geometry': [
                                            { text: '° Degree', value: '°' },
                                            { text: '∠ Angle', value: '∠' },
                                            { text: '∟ Right Angle', value: '∟' },
                                            { text: '⊾ Right Angle Arc', value: '⊾' },
                                            { text: '∡ Measured Angle', value: '∡' },
                                            { text: '∢ Spherical Angle', value: '∢' },
                                            { text: '⊥ Perpendicular', value: '⊥' },
                                            { text: '∥ Parallel', value: '∥' },
                                            { text: '∦ Not Parallel', value: '∦' },
                                            { text: '≅ Congruent', value: '≅' },
                                            { text: '∼ Similar', value: '∼' },
                                            { text: '≁ Not Similar', value: '≁' },
                                            { text: '△ Triangle', value: '△' },
                                            { text: '▲ Filled Triangle', value: '▲' },
                                            { text: '□ Square', value: '□' },
                                            { text: '■ Filled Square', value: '■' },
                                            { text: '○ Circle', value: '○' },
                                            { text: '● Filled Circle', value: '●' },
                                            { text: '◇ Diamond', value: '◇' },
                                            { text: '◆ Filled Diamond', value: '◆' },
                                            { text: '⬡ Hexagon', value: '⬡' },
                                            { text: '⬢ Filled Hexagon', value: '⬢' },
                                        ],
                                        'Arrows': [
                                            { text: '→ Right Arrow', value: '→' },
                                            { text: '← Left Arrow', value: '←' },
                                            { text: '↑ Up Arrow', value: '↑' },
                                            { text: '↓ Down Arrow', value: '↓' },
                                            { text: '↔ Left-Right', value: '↔' },
                                            { text: '↕ Up-Down', value: '↕' },
                                            { text: '↗ NE Arrow', value: '↗' },
                                            { text: '↘ SE Arrow', value: '↘' },
                                            { text: '↙ SW Arrow', value: '↙' },
                                            { text: '↖ NW Arrow', value: '↖' },
                                            { text: '⇒ Double Right', value: '⇒' },
                                            { text: '⇐ Double Left', value: '⇐' },
                                            { text: '⇑ Double Up', value: '⇑' },
                                            { text: '⇓ Double Down', value: '⇓' },
                                            { text: '⇔ Double LR', value: '⇔' },
                                            { text: '⇕ Double UD', value: '⇕' },
                                            { text: '↦ Maps To', value: '↦' },
                                            { text: '↤ Maps From', value: '↤' },
                                            { text: '⟶ Long Right', value: '⟶' },
                                            { text: '⟵ Long Left', value: '⟵' },
                                            { text: '⟷ Long LR', value: '⟷' },
                                            { text: '⟹ Long Double R', value: '⟹' },
                                            { text: '⟸ Long Double L', value: '⟸' },
                                            { text: '⟺ Long Double LR', value: '⟺' },
                                        ],
                                        'Summation & Products': [
                                            { text: '∑ Summation', value: '∑' },
                                            { text: '∏ Product', value: '∏' },
                                            { text: '∐ Coproduct', value: '∐' },
                                            { text: '⋀ N-ary And', value: '⋀' },
                                            { text: '⋁ N-ary Or', value: '⋁' },
                                            { text: '⋂ N-ary Intersection', value: '⋂' },
                                            { text: '⋃ N-ary Union', value: '⋃' },
                                        ],
                                        'Number Sets': [
                                            { text: 'ℕ Natural Numbers', value: 'ℕ' },
                                            { text: 'ℤ Integers', value: 'ℤ' },
                                            { text: 'ℚ Rationals', value: 'ℚ' },
                                            { text: 'ℝ Real Numbers', value: 'ℝ' },
                                            { text: 'ℂ Complex Numbers', value: 'ℂ' },
                                            { text: 'ℍ Quaternions', value: 'ℍ' },
                                            { text: 'ℙ Primes', value: 'ℙ' },
                                            { text: 'ℵ Aleph', value: 'ℵ' },
                                            { text: 'ℶ Beth', value: 'ℶ' },
                                            { text: 'ℷ Gimel', value: 'ℷ' },
                                        ],
                                        'Fractions': [
                                            { text: '½ One Half', value: '½' },
                                            { text: '⅓ One Third', value: '⅓' },
                                            { text: '⅔ Two Thirds', value: '⅔' },
                                            { text: '¼ One Quarter', value: '¼' },
                                            { text: '¾ Three Quarters', value: '¾' },
                                            { text: '⅕ One Fifth', value: '⅕' },
                                            { text: '⅖ Two Fifths', value: '⅖' },
                                            { text: '⅗ Three Fifths', value: '⅗' },
                                            { text: '⅘ Four Fifths', value: '⅘' },
                                            { text: '⅙ One Sixth', value: '⅙' },
                                            { text: '⅚ Five Sixths', value: '⅚' },
                                            { text: '⅛ One Eighth', value: '⅛' },
                                            { text: '⅜ Three Eighths', value: '⅜' },
                                            { text: '⅝ Five Eighths', value: '⅝' },
                                            { text: '⅞ Seven Eighths', value: '⅞' },
                                        ],
                                        'Miscellaneous': [
                                            { text: '⋅ Dot Operator', value: '⋅' },
                                            { text: '∘ Ring Operator', value: '∘' },
                                            { text: '⋆ Star Operator', value: '⋆' },
                                            { text: '∗ Asterisk', value: '∗' },
                                            { text: '† Dagger', value: '†' },
                                            { text: '‡ Double Dagger', value: '‡' },
                                            { text: '⌈ Left Ceiling', value: '⌈' },
                                            { text: '⌉ Right Ceiling', value: '⌉' },
                                            { text: '⌊ Left Floor', value: '⌊' },
                                            { text: '⌋ Right Floor', value: '⌋' },
                                            { text: '⟨ Left Angle Bracket', value: '⟨' },
                                            { text: '⟩ Right Angle Bracket', value: '⟩' },
                                            { text: '‰ Per Mille', value: '‰' },
                                            { text: '‱ Per Ten Thousand', value: '‱' },
                                            { text: 'ℏ h-bar', value: 'ℏ' },
                                            { text: 'ℑ Imaginary Part', value: 'ℑ' },
                                            { text: 'ℜ Real Part', value: 'ℜ' },
                                            { text: '℘ Weierstrass p', value: '℘' },
                                            { text: '⊙ Circled Dot', value: '⊙' },
                                            { text: '⊚ Circled Ring', value: '⊚' },
                                            { text: '⊛ Circled Asterisk', value: '⊛' },
                                            { text: '⊘ Circled Slash', value: '⊘' },
                                            { text: '⊝ Circled Dash', value: '⊝' },
                                            { text: '⋄ Diamond Operator', value: '⋄' },
                                            { text: '∎ End of Proof', value: '∎' },
                                        ],
                                    };

                                    // Build menu items from categories
                                    const menuItems = Object.keys(mathSymbols).map(category => ({
                                        type: 'nestedmenuitem',
                                        text: category,
                                        getSubmenuItems: () => mathSymbols[category].map(symbol => ({
                                            type: 'menuitem',
                                            text: symbol.text,
                                            onAction: () => editor.insertContent(symbol.value)
                                        }))
                                    }));

                                    // Register the math symbols button
                                    editor.ui.registry.addMenuButton('mathsymbols', {
                                        text: 'Math ∑',
                                        tooltip: 'Insert Mathematical Symbol',
                                        fetch: (callback) => callback(menuItems)
                                    });
                                    this.editor = editor;

                                    editor.on('init', () => {
                                        this.initialized = true;
                                        if (this.markdown) {
                                            editor.setContent(this.markdown);
                                        }
                                        // Mark initialization complete after a short delay
                                        setTimeout(() => {
                                            this.isInitializing = false;
                                        }, 300);
                                        console.log('TinyMCE initialized successfully');
                                    });

                                    editor.on('input change blur', () => {
                                        // Only update local markdown, don't sync with Livewire automatically
                                        if (!this.isInitializing) {
                                            const content = editor.getContent();
                                            this.markdown = content;
                                            // Sync to Livewire if wireName is set and $wire is available
                                            if (this.wireName && typeof this.$wire !== 'undefined') {
                                                this.$wire.set(this.wireName, content);
                                            }
                                        }
                                    });

                                    // Sync content to hidden input on form submit
                                    const form = editorElement.closest('form');
                                    if (form) {
                                        form.addEventListener('submit', (e) => {
                                            // Get the latest content from TinyMCE
                                            const content = editor.getContent();
                                            // Update the Alpine markdown property which updates the hidden input
                                            this.markdown = content;
                                            // Also update the hidden input directly as a fallback
                                            const hiddenInput = this.$refs.hiddenInput;
                                            if (hiddenInput) {
                                                hiddenInput.value = content;
                                            }
                                            console.log('Form submit - syncing TinyMCE content:', content.substring(0, 100));
                                        }, true); // Use capture phase to ensure this runs first
                                    }
                                },
                                init_instance_callback: (editor) => {
                                    console.log('TinyMCE instance created:', editor.id);
                                },
                                statusbar: false
                            }).catch(error => {
                                console.error('TinyMCE initialization failed:', error);
                            });
                        });
                    },

updatePreview() {
                        this.previewHtml = this.markdown || '<p class="text-gray-400">No content to preview</p>';

                        // Render math after preview updates
                        this.$nextTick(() => {
                            const previewEl = this.$el.querySelector('.markdown-preview');
                            if (previewEl && typeof window.renderMathInElement !== 'undefined') {
                                try {
                                    window.renderMathInElement(previewEl, {
                                        delimiters: [
                                            {left: '$$', right: '$$', display: true},
                                            {left: '$', right: '$', display: false},
                                            {left: '\\[', right: '\\]', display: true},
                                            {left: '\\(', right: '\\)', display: false}
                                        ],
                                        throwOnError: false,
                                        errorColor: '#cc0000',
                                        strict: false,
                                        trust: true
                                    });
                                } catch(e) {
                                    console.error('KaTeX preview rendering error:', e);
                                }
                            }
                        });
                    },

                    // Update editor content from external source (e.g., Livewire)
                    setContent(newContent) {
                        this.markdown = newContent || '';
                        if (this.editor && this.initialized) {
                            this.isInitializing = true;
                            this.editor.setContent(this.markdown);
                            setTimeout(() => {
                                this.isInitializing = false;
                            }, 300);
                        }
                    }
                }
            };
        </script>
    @endpush
@endonce

@props(['name', 'value' => null, 'label' => null, 'height' => 400, 'info' => null, 'required' => false, 'wireModel' => null])

@php
    $markdown = old($name, $value);
    // Generate a unique ID and replace dots with underscores to make it a valid CSS selector
    $uniqueId = str_replace('.', '_', uniqid());
    $editorId = 'markdown-editor-' . str_replace(['[', ']', '.'], ['_', '_', '_'], $name) . '_' . $uniqueId;
    // Use wireModel prop if provided, or check attributes for wire-model/wire:model, otherwise fall back to name
    $livewireModel = $wireModel ?? $attributes->get('wire-model') ?? $attributes->get('wire:model') ?? $name;
@endphp

<div class="space-y-1"
     x-data="markdownEditor(@js($markdown ?? ''), @js($editorId), @js($height), @js($livewireModel))"
     x-init="
        $nextTick(() => {
            initEditor();
        });
        $watch('preview', (value) => {
            if (value) {
                updatePreview();
            }
        });
        // Watch for Livewire property changes using $wire.$watch
        if (wireName && typeof $wire !== 'undefined') {
            $wire.$watch(wireName, (newValue) => {
                if (newValue !== markdown) {
                    setContent(newValue);
                }
            });
        }
        // Also listen for custom event to update content
        $el.addEventListener('update-editor-content', (e) => {
            if (e.detail && e.detail.content !== undefined) {
                setContent(e.detail.content);
            }
        });
     "
     :data-editor-id="editorId">

    @if($label)
        <label class="block text-sm tracking-tighter pb-1 font-medium text-gray-700 dark:text-gray-300">
            {{ $label }}
            @if($required)
                <span class="text-red-500">*</span>
            @endif
        </label>
    @endif

    @if($info)
        <p class="text-xs tracking-tight !-mt-0 pb-1 text-gray-500 dark:text-gray-400">{{ $info }}</p>
    @endif

    <div class="border border-gray-300 dark:border-gray-600 rounded-lg overflow-hidden">
        <!-- Toolbar -->
        <div class="bg-gray-50 dark:bg-gray-700 border-b border-gray-300 dark:border-gray-600 flex items-center">
            <div class="flex">
                <button
                    type="button"
                    x-on:click="preview = false"
                    x-bind:class="!preview ? 'bg-white dark:bg-gray-800 border-gray-300 dark:border-gray-600 border-b-0 text-gray-900 dark:text-white' : 'text-gray-600 dark:text-gray-300 hover:text-gray-900 dark:hover:text-white'"
                    class="px-4 py-2 text-sm font-medium border-r transition-colors duration-200">
                    Write
                </button>
                <button
                    type="button"
                    x-on:click="preview = true; updatePreview();"
                    x-bind:class="preview ? 'bg-white dark:bg-gray-800 border-gray-300 dark:border-gray-600 border-b-0 text-gray-900 dark:text-white' : 'text-gray-600 dark:text-gray-300 hover:text-gray-900 dark:hover:text-white'"
                    class="px-4 py-2 text-sm font-medium transition-colors duration-200">
                    Preview
                </button>
            </div>
            <div class="ml-auto px-4 py-2">
                <div class="flex items-center space-x-2 text-xs text-gray-500 dark:text-gray-400">
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                    </svg>
                    <span>Rich text editor</span>
                </div>
            </div>
        </div>

        <!-- Hidden input to ensure content is submitted -->
        <input type="hidden" name="{{ $name }}" :value="markdown" x-ref="hiddenInput">

        <!-- Editor Area -->
        <div x-show="!preview" class="bg-white dark:bg-gray-800" wire:ignore>
            <textarea
                :id="editorId"
                class="w-full border-0 focus:ring-0 dark:bg-gray-800 dark:text-white"
                style="min-height: {{ $height }}px; resize: vertical;"></textarea>
        </div>

        <!-- Preview Area -->
        <div
            x-show="preview"
            class="markdown-preview bg-white dark:bg-gray-800 p-4 overflow-auto prose prose-sm dark:prose-invert max-w-none"
            style="display: none; min-height: {{ $height }}px;"
            x-html="previewHtml">
        </div>
    </div>

    @error($name)
    <div class="text-xs font-medium text-red-600 dark:text-red-400 mt-1">{{ $message }}</div>
    @enderror
</div>

<style>
    .markdown-preview {
        word-wrap: break-word;
    }

    .markdown-preview p {
        margin-bottom: 1em;
    }

    .markdown-preview h1,
    .markdown-preview h2,
    .markdown-preview h3 {
        margin-top: 1em;
        margin-bottom: 0.5em;
        font-weight: bold;
    }

    .markdown-preview ul,
    .markdown-preview ol {
        margin-left: 1.5em;
        margin-bottom: 1em;
    }

    .markdown-preview strong {
        font-weight: bold;
    }

    .markdown-preview em {
        font-style: italic;
    }

    .markdown-preview img {
        max-width: 100%;
        height: auto;
    }
</style>
