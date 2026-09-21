@props([
    'content' => '',
    'class' => '',
])

<div 
    x-data="mathDisplay(@js($content))"
    class="math-display-container {{ $class }}"
>
    <div x-ref="output" class="prose dark:prose-invert max-w-none">
        <!-- Rendered math content will appear here -->
    </div>
</div>

@once
<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('mathDisplay', (initialContent) => ({
        content: String(initialContent || ''),
        
        init() {
            this.render();
        },

        render() {
            this.$nextTick(() => {
                if (!this.$refs.output) return;
                
                try {
                    let cleanContent = this.content.trim();
                    
                    // Strip backticks around math if present (e.g., `` `$x$` `` -> `$x$`)
                    cleanContent = cleanContent.replace(/`(\$\$[\s\S]*?\$\$)`/g, '$1');
                    cleanContent = cleanContent.replace(/`(\$[\s\S]*?\$)`/g, '$1');

                    // Primary: Use existing markdown-it-texmath pipeline if available
                    if (typeof window.renderMarkdownWithMath === 'function') {
                        this.$refs.output.innerHTML = window.renderMarkdownWithMath(cleanContent);
                    } 
                    // Fallback: Pure KaTeX parser for mixed text and math
                    else if (typeof window.katex !== 'undefined') {
                        this.$refs.output.innerHTML = this.parseMixedContent(cleanContent);
                    } 
                    // Error state
                    else {
                        this.$refs.output.innerHTML = '<span class="text-red-500 text-sm">KaTeX not loaded</span>';
                    }
                } catch (e) {
                    this.$refs.output.innerHTML = `<span class="text-red-500 text-sm font-mono">Error: ${e.message}</span>`;
                }
            });
        },

        parseMixedContent(text) {
            // Split by display math ($$...$$) or inline math ($...$)
            const parts = text.split(/(\$\$[\s\S]*?\$\$|\$[\s\S]*?\$)/g);
            
            return parts.map(part => {
                // Display Math
                if (part.startsWith('$$') && part.endsWith('$$')) {
                    const math = part.slice(2, -2).trim();
                    return window.katex.renderToString(math, { 
                        displayMode: true, 
                        throwOnError: false,
                        strict: false 
                    });
                } 
                // Inline Math
                else if (part.startsWith('$') && part.endsWith('$') && part.length > 2) {
                    const inner = part.slice(1, -1).trim();
                    // Heuristic: Ensure it's actually math and not a currency symbol (e.g., "$5")
                    if (inner.includes('\\') || inner.includes(' ') || /[a-zA-Z]/.test(inner)) {
                        return window.katex.renderToString(inner, { 
                            displayMode: false, 
                            throwOnError: false,
                            strict: false 
                        });
                    }
                }
                
                // Plain Text: Escape HTML to prevent XSS, preserve newlines
                return part
                    .replace(/&/g, "&amp;")
                    .replace(/</g, "&lt;")
                    .replace(/>/g, "&gt;")
                    .replace(/\n/g, "<br>");
            }).join('');
        }
    }));
});
</script>
@endonce