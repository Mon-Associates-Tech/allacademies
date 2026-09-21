<x-layouts.app>
    <div class="max-w-6xl mx-auto p-6">
        <div class="mb-6">
            <h1 class="text-2xl font-bold text-gray-900 dark:text-gray-100">LaTeX Math Playground</h1>
            <p class="text-gray-600 dark:text-gray-400 mt-1">Type a LaTeX expression. Backticks and $ delimiters are automatically handled.</p>
        </div>

        <div 
            x-data="mathPlayground()"
            class="grid grid-cols-1 lg:grid-cols-2 gap-6 bg-white dark:bg-gray-900 rounded-xl shadow-lg border border-gray-200 dark:border-gray-800 overflow-hidden"
        >
            <!-- Left Side: Input -->
            <div class="flex flex-col p-6 border-b lg:border-b-0 lg:border-r border-gray-200 dark:border-gray-800">
                <div class="flex items-center justify-between mb-3">
                    <label class="text-sm font-semibold text-gray-700 dark:text-gray-300">LaTeX Input</label>
                    <label class="inline-flex items-center cursor-pointer">
                        <input type="checkbox" x-model="displayMode" class="sr-only peer">
                        <div class="relative w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-2 peer-focus:ring-blue-300 dark:peer-focus:ring-blue-800 rounded-full peer dark:bg-gray-700 peer-checked:after:translate-x-full peer-checked:after:border-white after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-gray-600 peer-checked:bg-blue-600"></div>
                        <span class="ms-2 text-xs font-medium text-gray-700 dark:text-gray-300">Display Mode</span>
                    </label>
                </div>

                <textarea 
                    x-ref="input"
                    x-model="expression" 
                    rows="10"
                    class="w-full flex-1 p-4 font-mono text-sm bg-gray-50 dark:bg-gray-800 border border-gray-300 dark:border-gray-700 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none resize-none transition text-gray-800 dark:text-gray-200"
                    placeholder="Try: `$\frac{1}{2} + \frac{1}{3}$`"
                    spellcheck="false"
                ></textarea>

                <div class="flex flex-wrap gap-2 mt-4">
                    <button @click="insert('\\frac{}{}')" class="px-3 py-1.5 text-xs font-medium bg-gray-100 dark:bg-gray-800 text-gray-700 dark:text-gray-300 border border-gray-300 dark:border-gray-700 rounded-md hover:bg-gray-200 dark:hover:bg-gray-700 transition">Fraction</button>
                    <button @click="insert('\\sum_{i=1}^{n}')" class="px-3 py-1.5 text-xs font-medium bg-gray-100 dark:bg-gray-800 text-gray-700 dark:text-gray-300 border border-gray-300 dark:border-gray-700 rounded-md hover:bg-gray-200 dark:hover:bg-gray-700 transition">Sum</button>
                    <button @click="insert('\\int_{0}^{\\infty}')" class="px-3 py-1.5 text-xs font-medium bg-gray-100 dark:bg-gray-800 text-gray-700 dark:text-gray-300 border border-gray-300 dark:border-gray-700 rounded-md hover:bg-gray-200 dark:hover:bg-gray-700 transition">Integral</button>
                    <button @click="insert('\\sqrt{}')" class="px-3 py-1.5 text-xs font-medium bg-gray-100 dark:bg-gray-800 text-gray-700 dark:text-gray-300 border border-gray-300 dark:border-gray-700 rounded-md hover:bg-gray-200 dark:hover:bg-gray-700 transition">Square Root</button>
                </div>
            </div>

            <!-- Right Side: Preview -->
            <div class="flex flex-col p-6 bg-gray-50/50 dark:bg-gray-950/50">
                <label class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-3">Live Preview</label>
                <div 
                    x-ref="preview" 
                    class="flex-1 min-h-[250px] p-6 bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded-lg overflow-auto"
                ></div>
            </div>
        </div>
    </div>

    @once
    <script>
    function mathPlayground() {
        return {
            expression: 'Evaluate: `$\\frac{1}{2} + \\frac{1}{3}$` and `$2 \\times 3$`',
            displayMode: false,
            
            init() {
                this.render();
                this.$watch('expression', () => this.render());
                this.$watch('displayMode', () => this.render());
            },
            
            render() {
                this.$nextTick(() => {
                    if (!this.$refs.preview) return;
                    
                    try {
                        let cleanExpr = this.expression.trim();
                        
                        // STRIP BACKTICKS: Convert `` `$math$` `` to `$math$`
                        cleanExpr = cleanExpr.replace(/`(\$\$.*?\$\$)`/gs, '$1');
                        cleanExpr = cleanExpr.replace(/`(\$.*?\$)`/gs, '$1');
                        
                        // Check if we have markdown (text + math) or just pure math
                        const hasText = /[a-zA-Z]/.test(cleanExpr);
                        
                        if (hasText && typeof window.renderMarkdownWithMath === 'function') {
                            // Use your existing markdown-it-texmath pipeline
                            this.$refs.preview.innerHTML = window.renderMarkdownWithMath(cleanExpr);
                        } else if (typeof window.katex !== 'undefined') {
                            // Pure math mode - strip $ delimiters for direct KaTeX render
                            let pureMath = cleanExpr.trim();
                            if (pureMath.startsWith('$$') && pureMath.endsWith('$$')) {
                                pureMath = pureMath.slice(2, -2).trim();
                                this.displayMode = true;
                            } else if (pureMath.startsWith('$') && pureMath.endsWith('$')) {
                                pureMath = pureMath.slice(1, -1).trim();
                            }
                            
                            window.katex.render(pureMath, this.$refs.preview, {
                                displayMode: this.displayMode,
                                throwOnError: false,
                                strict: false,
                                trust: true,
                            });
                        } else {
                            this.$refs.preview.innerHTML = '<span class="text-red-500">KaTeX not loaded</span>';
                        }
                    } catch (e) {
                        this.$refs.preview.innerHTML = '<span class="text-red-500 text-sm font-mono">Error: ' + e.message + '</span>';
                    }
                });
            },
            
            insert(symbol) {
                this.expression += ' ' + symbol + ' ';
                this.$nextTick(() => this.$refs.input.focus());
            }
        }
    }
    </script>
    @endonce
</x-layouts.app>