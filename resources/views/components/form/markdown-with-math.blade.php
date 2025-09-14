@props(['content' => '', 'class' => 'prose max-w-none'])

<div {{ $attributes->merge(['class' => $class]) }}
     x-data="{
        htmlContent: '',
        init() {
            this.renderContent();
        },
        renderContent() {
            // Process escaped square brackets first
            let processedContent = @js($content);

             // Remove all backticks (both single and triple)
          processedContent = processedContent.replace(/\\\[/g, '\\\\\\[');
processedContent = processedContent.replace(/\\\]/g, '\\\\\\]');
processedContent = processedContent.replace(/`/g, ''); // Remove all backticks

this.htmlContent = window.renderMarkdownWithMath(processedContent);
this.$nextTick(() => {
    this.renderMath();
});
        },
        renderMath() {
            if (typeof window.renderMathInElement !== 'undefined') {
                window.renderMathInElement(this.$el.querySelector('.math-content'), {
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
            }
        }
     }"
     x-init="renderContent()">
    <div class="math-content" x-html="htmlContent"></div>
</div>
