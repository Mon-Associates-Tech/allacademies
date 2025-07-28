@props(['content' => '', 'class' => 'prose max-w-none'])

<div {{ $attributes->merge(['class' => $class]) }}
     x-data="{
        htmlContent: '',
        init() {
            this.renderContent();
        },
        renderContent() {
            this.htmlContent = marked.parse(@js($content));
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
                    trust: false
                });
            }
        }
     }"
     x-init="renderContent()">
    <div class="math-content" x-html="htmlContent"></div>
</div>
