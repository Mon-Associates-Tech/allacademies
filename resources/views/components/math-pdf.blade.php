@props([
    'content' => '',
    'class' => '',
])

<div 
    x-data="mathDisplayClean(@js($content))"
    class="math-display-container {{ $class }}"
>
    <div x-ref="output" class="prose dark:prose-invert max-w-none"></div>
</div>

@once


<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('mathDisplayClean', (initialContent) => ({
        content: String(initialContent || ''),
        
        init() {
            // Wait for KaTeX JS to load before rendering
            if (typeof window.katex === 'undefined') {
                const interval = setInterval(() => {
                    if (typeof window.katex !== 'undefined') {
                        clearInterval(interval);
                        this.render();
                    }
                }, 50);
            } else {
                this.render();
            }
        },

        render() {
            this.$nextTick(() => {
                if (!this.$refs.output) return;
                
                try {
                    let cleanContent = this.content.trim();
                    
                    // Strip backticks around math if present
                    cleanContent = cleanContent.replace(/`(\$\$[\s\S]*?\$\$)`/g, '$1');
                    cleanContent = cleanContent.replace(/`(\$[\s\S]*?\$)`/g, '$1');

                    this.$refs.output.innerHTML = this.parseAndRender(cleanContent);
                } catch (e) {
                    this.$refs.output.innerHTML = `<span class="text-red-500 text-sm font-mono">Error: ${e.message}</span>`;
                }
            });
        },

        parseAndRender(text) {
            // Match $$...$$, $...$, OR <eq>...</eq> tags
            const regex = /(\$\$[\s\S]*?\$\$|\$[\s\S]*?\$|<eq>[\s\S]*?<\/eq>)/g;
            const parts = text.split(regex);
            
            return parts.map(part => {
                if (!part) return '';

                // 1. Display Math ($$...$$)
                if (part.startsWith('$$') && part.endsWith('$$')) {
                    const math = part.slice(2, -2).trim();
                    return window.katex.renderToString(math, { displayMode: true, throwOnError: false });
                } 
                
                // 2. Custom <eq>...</eq> tags (Treat as inline math)
                if (part.startsWith('<eq>') && part.endsWith('</eq>')) {
                    const math = part.slice(4, -5).trim();
                    return window.katex.renderToString(math, { displayMode: false, throwOnError: false });
                }

                // 3. Inline Math ($...$)
                if (part.startsWith('$') && part.endsWith('$') && part.length > 2) {
                    const inner = part.slice(1, -1).trim();
                    // Prevent rendering currency like "$50" as math
                    if (inner.includes('\\') || inner.includes(' ') || /[a-zA-Z]/.test(inner)) {
                        return window.katex.renderToString(inner, { displayMode: false, throwOnError: false });
                    }
                }
                
                // 4. Plain Text (Escape HTML to prevent XSS)
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