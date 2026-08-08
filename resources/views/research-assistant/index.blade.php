<x-layouts.app>
    <section>
        <livewire:research-assistant />
        @push('scripts')
            <script>
                // Additional Alpine.js functionality for enhanced UX
                document.addEventListener('alpine:init', () => {
                    Alpine.data('chatEnhancements', () => ({
                        lastMessageCount: 0,

                        init() {
                            // Watch for new messages and auto-scroll
                            this.$watch('$wire.messages.length', (value) => {
                                if (value > this.lastMessageCount) {
                                    this.lastMessageCount = value;
                                    this.$nextTick(() => {
                                        const chatContainer = this.$refs.chatContainer;
                                        const isNearBottom = chatContainer.scrollHeight - chatContainer.scrollTop <= chatContainer.clientHeight + 100;
                                        if (isNearBottom) {
                                            chatContainer.scrollTop = chatContainer.scrollHeight;
                                        }
                                    });
                                }
                            });

                            // Initialize message count
                            this.lastMessageCount = this.$wire.messages.length;
                        },

                        formatTime(timestamp) {
                            return new Date(timestamp).toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'});
                        },

                        copyMessage(content) {
                            navigator.clipboard.writeText(content).then(() => {
                                // You could add a toast notification here
                                console.log('Message copied to clipboard');
                            });
                        }
                    }));
                });
            </script>
        @endpush

        @push('styles')
            <style>
                /* Custom scrollbar for chat container */
                .overflow-y-auto::-webkit-scrollbar {
                    width: 6px;
                }

                .overflow-y-auto::-webkit-scrollbar-track {
                    background: theme('colors.gray.100');
                }

                .dark .overflow-y-auto::-webkit-scrollbar-track {
                    background: theme('colors.gray.800');
                }

                .overflow-y-auto::-webkit-scrollbar-thumb {
                    background: theme('colors.gray.400');
                    border-radius: 3px;
                }

                .dark .overflow-y-auto::-webkit-scrollbar-thumb {
                    background: theme('colors.gray.600');
                }

                .overflow-y-auto::-webkit-scrollbar-thumb:hover {
                    background: theme('colors.gray.500');
                }

                .dark .overflow-y-auto::-webkit-scrollbar-thumb:hover {
                    background: theme('colors.gray.500');
                }

                /* Smooth transitions */
                * {
                    transition-property: background-color, border-color, color, fill, stroke;
                    transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1);
                    transition-duration: 200ms;
                }

                /* Message animations */
                @keyframes messageSlideIn {
                    from {
                        opacity: 0;
                        transform: translateY(10px);
                    }
                    to {
                        opacity: 1;
                        transform: translateY(0);
                    }
                }

                .message-enter {
                    animation: messageSlideIn 0.3s ease-out;
                }

                /* Loading dots animation */
                @keyframes bounce {
                    0%, 80%, 100% {
                        transform: scale(0);
                    }
                    40% {
                        transform: scale(1);
                    }
                }

                .animate-bounce {
                    animation: bounce 1.4s infinite ease-in-out both;
                }

                /* Focus styles */
                .focus-visible:focus {
                    outline: 2px solid theme('colors.blue.500');
                    outline-offset: 2px;
                }
            </style>
        @endpush

    </section>
</x-layouts.app>



