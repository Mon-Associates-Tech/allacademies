<div class="min-h-screen bg-gradient-to-br from-blue-50 via-indigo-50 to-purple-50 dark:from-gray-900 dark:via-gray-800 dark:to-gray-900">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">

        <!-- Enhanced Header Section -->
        <div class="relative mb-8 overflow-hidden rounded-3xl">
            <div class="absolute inset-0 bg-gradient-to-r from-blue-600 via-indigo-600 to-purple-600 opacity-90"></div>
            <div class="absolute inset-0 bg-black opacity-20"></div>
            <div class="absolute inset-0" style="background-image: url('data:image/svg+xml;charset=utf-8,%3Csvg xmlns=\'http://www.w3.org/2000/svg\' width=\'80\' height=\'80\' viewBox=\'0 0 80 80\'%3E%3Cg fill=\'%23ffffff\' fill-opacity=\'0.1\'%3E%3Cpath d=\'M0 0h80v80H0V0zm20 20v40h40V20H20zm20 35a15 15 0 1 1 0-30 15 15 0 0 1 0 30z\'/%3E%3C/g%3E%3C/svg%3E')"></div>

            <div class="relative px-8 py-12">
                <div class="text-center">
                    <div class="inline-flex items-center justify-center w-16 h-16 bg-white/20 rounded-full backdrop-blur-sm mb-6">
                        <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>

                    <h1 class="text-4xl lg:text-5xl font-bold text-white mb-4">Help & Support</h1>
                    <p class="text-xl text-blue-100 mb-8 max-w-2xl mx-auto">
                        Get the help you need to succeed as an author on our platform
                    </p>

                    <!-- Search Bar -->
                    <div class="max-w-2xl mx-auto relative">
                        <div class="relative">
                            <input
                                wire:model.live.debounce.300ms="searchTerm"
                                type="text"
                                placeholder="Search for help articles, FAQs, or tutorials..."
                                class="w-full pl-12 pr-4 py-4 rounded-2xl border-0 text-gray-900 placeholder-gray-500 focus:ring-4 focus:ring-white/20 focus:outline-none text-lg backdrop-blur-sm bg-white/90"
                            >
                            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                <svg class="h-6 w-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                                </svg>
                            </div>
                            @if($isSearching)
                                <div class="absolute inset-y-0 right-0 pr-4 flex items-center">
                                    <svg class="animate-spin h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                    </svg>
                                </div>
                            @endif
                        </div>

                        <!-- Search Results -->
                        @if(!empty($searchResults) && count($searchResults) > 0)
                            <div class="absolute top-full left-0 right-0 mt-2 bg-white dark:bg-gray-800 rounded-xl shadow-2xl border border-gray-200 dark:border-gray-700 z-50">
                                <div class="p-4">
                                    <h3 class="text-sm font-medium text-gray-900 dark:text-white mb-3">Search Results</h3>
                                    <div class="space-y-2">
                                        @foreach($searchResults as $result)
                                            <div class="p-3 hover:bg-gray-50 dark:hover:bg-gray-700 rounded-lg cursor-pointer transition-colors">
                                                <div class="flex items-center">
                                                    <div class="flex-shrink-0 w-8 h-8 bg-indigo-100 dark:bg-indigo-900 rounded-full flex items-center justify-center">
                                                        @if($result['type'] === 'article')
                                                            <svg class="w-4 h-4 text-indigo-600 dark:text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                                            </svg>
                                                        @else
                                                            <svg class="w-4 h-4 text-indigo-600 dark:text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                                            </svg>
                                                        @endif
                                                    </div>
                                                    <div class="ml-3 flex-1">
                                                        <p class="text-sm font-medium text-gray-900 dark:text-white">{{ $result['title'] }}</p>
                                                        <p class="text-xs text-gray-500 dark:text-gray-400">{{ $result['excerpt'] }}</p>
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Support Stats -->
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mt-8">
                    <div class="bg-white/20 backdrop-blur-sm rounded-xl p-4 border border-white/30">
                        <div class="text-center">
                            <p class="text-2xl font-bold text-white">{{ $supportStats['total_articles'] }}</p>
                            <p class="text-blue-100 text-sm">Help Articles</p>
                        </div>
                    </div>
                    <div class="bg-white/20 backdrop-blur-sm rounded-xl p-4 border border-white/30">
                        <div class="text-center">
                            <p class="text-2xl font-bold text-white">{{ $supportStats['tutorials_available'] }}</p>
                            <p class="text-blue-100 text-sm">Video Tutorials</p>
                        </div>
                    </div>
                    <div class="bg-white/20 backdrop-blur-sm rounded-xl p-4 border border-white/30">
                        <div class="text-center">
                            <p class="text-2xl font-bold text-white">{{ $supportStats['avg_response_time'] }}</p>
                            <p class="text-blue-100 text-sm">Avg Response</p>
                        </div>
                    </div>
                    <div class="bg-white/20 backdrop-blur-sm rounded-xl p-4 border border-white/30">
                        <div class="text-center">
                            <p class="text-2xl font-bold text-white">{{ $supportStats['satisfaction_rate'] }}%</p>
                            <p class="text-blue-100 text-sm">Satisfaction</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Alert Messages -->
        @if (session()->has('message'))
            <div class="mb-6 bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-700 text-green-800 dark:text-green-200 px-4 py-3 rounded-xl shadow-sm">
                <div class="flex items-center">
                    <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                    </svg>
                    {{ session('message') }}
                </div>
            </div>
        @endif

        <!-- Quick Help Section -->
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl border border-gray-200 dark:border-gray-700 p-6 mb-8">
            <h2 class="text-xl font-bold text-gray-900 dark:text-white mb-6">Quick Help</h2>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                @foreach($quickHelp as $item)
                    <div class="bg-gradient-to-br from-indigo-50 to-purple-50 dark:from-indigo-900/20 dark:to-purple-900/20 rounded-xl p-6 border border-indigo-100 dark:border-indigo-800 hover:shadow-lg transition-all duration-300 cursor-pointer">
                        <div class="flex items-center mb-4">
                            <div class="p-3 bg-indigo-100 dark:bg-indigo-900/50 rounded-lg">
                                <svg class="w-6 h-6 text-indigo-600 dark:text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    @if($item['icon'] === 'book-open')
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                                    @elseif($item['icon'] === 'upload')
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                                    @elseif($item['icon'] === 'chart-bar')
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                                    @else
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5.121 17.804A13.937 13.937 0 0112 16c2.5 0 4.847.655 6.879 1.804M15 10a3 3 0 11-6 0 3 3 0 016 0zm6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    @endif
                                </svg>
                            </div>
                        </div>
                        <h3 class="font-semibold text-gray-900 dark:text-white mb-2">{{ $item['title'] }}</h3>
                        <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">{{ $item['description'] }}</p>
                        <button class="text-indigo-600 dark:text-indigo-400 hover:text-indigo-700 dark:hover:text-indigo-300 font-medium text-sm">
                            {{ $item['action'] }} →
                        </button>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Tab Navigation -->
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl border border-gray-200 dark:border-gray-700 mb-8 overflow-hidden">
            <div class="flex flex-wrap border-b border-gray-200 dark:border-gray-700">
                <button wire:click="setActiveTab('knowledge-base')"
                        class="flex-1 px-6 py-4 text-sm font-medium transition-all duration-200 {{ $activeTab === 'knowledge-base' ? 'bg-indigo-50 dark:bg-indigo-900/20 text-indigo-700 dark:text-indigo-300 border-b-2 border-indigo-500' : 'text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700' }}">
                    <div class="flex items-center justify-center">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                        </svg>
                        Knowledge Base
                    </div>
                </button>

                <button wire:click="setActiveTab('faq')"
                        class="flex-1 px-6 py-4 text-sm font-medium transition-all duration-200 {{ $activeTab === 'faq' ? 'bg-indigo-50 dark:bg-indigo-900/20 text-indigo-700 dark:text-indigo-300 border-b-2 border-indigo-500' : 'text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700' }}">
                    <div class="flex items-center justify-center">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        FAQ
                    </div>
                </button>

                <button wire:click="setActiveTab('tutorials')"
                        class="flex-1 px-6 py-4 text-sm font-medium transition-all duration-200 {{ $activeTab === 'tutorials' ? 'bg-indigo-50 dark:bg-indigo-900/20 text-indigo-700 dark:text-indigo-300 border-b-2 border-indigo-500' : 'text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700' }}">
                    <div class="flex items-center justify-center">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                        </svg>
                        Video Tutorials
                    </div>
                </button>

                <button wire:click="setActiveTab('contact')"
                        class="flex-1 px-6 py-4 text-sm font-medium transition-all duration-200 {{ $activeTab === 'contact' ? 'bg-indigo-50 dark:bg-indigo-900/20 text-indigo-700 dark:text-indigo-300 border-b-2 border-indigo-500' : 'text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700' }}">
                    <div class="flex items-center justify-center">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                        </svg>
                        Contact Support
                    </div>
                </button>
            </div>
        </div>

        <!-- Knowledge Base Tab -->
        @if($activeTab === 'knowledge-base')
            <div class="space-y-8">
                <!-- Categories Filter -->
                <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl border border-gray-200 dark:border-gray-700 p-6">
                    <div class="flex flex-wrap gap-3">
                        @foreach($categories as $value => $label)
                            <button wire:click="$set('selectedCategory', '{{ $value }}')"
                                    class="px-4 py-2 rounded-full text-sm font-medium transition-all duration-200 {{ $selectedCategory === $value ? 'bg-indigo-100 dark:bg-indigo-900/50 text-indigo-700 dark:text-indigo-300' : 'bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-400 hover:bg-gray-200 dark:hover:bg-gray-600' }}">
                                {{ $label }}
                            </button>
                        @endforeach
                    </div>
                </div>

                <!-- Knowledge Base Articles -->
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    @foreach($knowledgeBaseArticles as $article)
                        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl border border-gray-200 dark:border-gray-700 overflow-hidden hover:shadow-2xl transition-all duration-300">
                            <div class="p-6">
                                <div class="flex items-center justify-between mb-4">
                                    <span class="px-3 py-1 bg-indigo-100 dark:bg-indigo-900/50 text-indigo-700 dark:text-indigo-300 rounded-full text-sm font-medium">
                                        {{ $article['category'] }}
                                    </span>
                                    <div class="flex items-center text-sm text-gray-500 dark:text-gray-400">
                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                        </svg>
                                        {{ $article['reading_time'] }}
                                    </div>
                                </div>

                                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-3">{{ $article['title'] }}</h3>
                                <p class="text-gray-600 dark:text-gray-400 mb-4">{{ $article['excerpt'] }}</p>

                                <div class="flex flex-wrap gap-2 mb-4">
                                    @foreach($article['tags'] as $tag)
                                        <span class="px-2 py-1 bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-400 rounded text-xs">
                                            #{{ $tag }}
                                        </span>
                                    @endforeach
                                </div>

                                <div class="flex items-center justify-between pt-4 border-t border-gray-200 dark:border-gray-600">
                                    <div class="flex items-center space-x-4">
                                        <button wire:click="markArticleHelpful({{ $article['id'] }})"
                                                class="flex items-center space-x-2 text-gray-600 dark:text-gray-400 hover:text-green-500 transition-colors">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 10h4.764a2 2 0 011.789 2.894l-3.5 7A2 2 0 0115.263 21h-4.017c-.163 0-.326-.02-.485-.06L7 20m7-10V5a2 2 0 00-2-2h-.095c-.5 0-.905.405-.905.905 0 .714-.211 1.412-.608 2.006L7 11v9m7-10h-2M7 20H5a2 2 0 01-2-2v-6a2 2 0 012-2h2.5"/>
                                            </svg>
                                            <span>{{ $article['helpful_count'] }}</span>
                                        </button>

                                        <span class="text-sm text-gray-500 dark:text-gray-400">
                                            Updated {{ $article['updated_at']->diffForHumans() }}
                                        </span>
                                    </div>

                                    <button wire:click="selectArticle({{ $article['id'] }})"
                                            class="text-indigo-600 dark:text-indigo-400 hover:text-indigo-700 dark:hover:text-indigo-300 font-medium text-sm">
                                        Read Article →
                                    </button>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        <!-- FAQ Tab -->
        @if($activeTab === 'faq')
            <div class="space-y-6">
                @foreach($faqItems as $faq)
                    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl border border-gray-200 dark:border-gray-700 overflow-hidden">
                        <div class="p-6">
                            <div class="flex items-start justify-between mb-4">
                                <div class="flex-1">
                                    <div class="flex items-center mb-2">
                                        <span class="px-3 py-1 bg-purple-100 dark:bg-purple-900/50 text-purple-700 dark:text-purple-300 rounded-full text-sm font-medium mr-3">
                                            {{ $faq['category'] }}
                                        </span>
                                        <div class="flex items-center text-sm text-gray-500 dark:text-gray-400">
                                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 10h4.764a2 2 0 011.789 2.894l-3.5 7A2 2 0 0115.263 21h-4.017c-.163 0-.326-.02-.485-.06L7 20m7-10V5a2 2 0 00-2-2h-.095c-.5 0-.905.405-.905.905 0 .714-.211 1.412-.608 2.006L7 11v9m7-10h-2M7 20H5a2 2 0 01-2-2v-6a2 2 0 012-2h2.5"/>
                                            </svg>
                                            {{ $faq['helpful_count'] }} found this helpful
                                        </div>
                                    </div>
                                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-3">{{ $faq['question'] }}</h3>
                                    <p class="text-gray-600 dark:text-gray-400 leading-relaxed">{{ $faq['answer'] }}</p>
                                </div>

                                <div class="ml-4 flex-shrink-0">
                                    <div class="w-10 h-10 bg-gradient-to-br from-purple-500 to-pink-500 rounded-full flex items-center justify-center">
                                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                        </svg>
                                    </div>
                                </div>
                            </div>

                            <div class="pt-4 border-t border-gray-200 dark:border-gray-600">
                                <button wire:click="markFaqHelpful({{ $faq['id'] }})"
                                        class="inline-flex items-center space-x-2 text-gray-600 dark:text-gray-400 hover:text-green-500 transition-colors">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 10h4.764a2 2 0 011.789 2.894l-3.5 7A2 2 0 0115.263 21h-4.017c-.163 0-.326-.02-.485-.06L7 20m7-10V5a2 2 0 00-2-2h-.095c-.5 0-.905.405-.905.905 0 .714-.211 1.412-.608 2.006L7 11v9m7-10h-2M7 20H5a2 2 0 01-2-2v-6a2 2 0 012-2h2.5"/>
                                    </svg>
                                    <span>Was this helpful?</span>
                                </button>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif

        <!-- Tutorials Tab -->
        @if($activeTab === 'tutorials')
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                @foreach($tutorials as $tutorial)
                    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl border border-gray-200 dark:border-gray-700 overflow-hidden hover:shadow-2xl transition-all duration-300">
                        <div class="aspect-video bg-gradient-to-br from-indigo-400 to-purple-500 relative overflow-hidden">
                            <div class="absolute inset-0 bg-black/30 flex items-center justify-center">
                                <button wire:click="selectTutorial({{ $tutorial['id'] }})"
                                        class="w-16 h-16 bg-white/20 backdrop-blur-sm rounded-full flex items-center justify-center hover:bg-white/30 transition-all duration-300">
                                    <svg class="w-8 h-8 text-white ml-1" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M8 5v14l11-7z"/>
                                    </svg>
                                </button>
                            </div>

                            <div class="absolute top-4 left-4">
                                <span class="px-3 py-1 bg-black/50 text-white rounded-full text-sm">
                                    {{ $tutorial['duration'] }}
                                </span>
                            </div>

                            <div class="absolute top-4 right-4">
                                <span class="px-3 py-1 bg-black/50 text-white rounded-full text-sm">
                                    {{ $tutorial['difficulty'] }}
                                </span>
                            </div>
                        </div>

                        <div class="p-6">
                            <div class="flex items-center justify-between mb-4">
                                <span class="px-3 py-1 bg-green-100 dark:bg-green-900/50 text-green-700 dark:text-green-300 rounded-full text-sm font-medium">
                                    {{ $tutorial['category'] }}
                                </span>
                                <div class="flex items-center text-sm text-gray-500 dark:text-gray-400">
                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                    {{ $tutorial['steps'] }} steps
                                </div>
                            </div>

                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-3">{{ $tutorial['title'] }}</h3>
                            <p class="text-gray-600 dark:text-gray-400 mb-4">{{ $tutorial['description'] }}</p>

                            <button wire:click="selectTutorial({{ $tutorial['id'] }})"
                                    class="w-full bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-indigo-700 hover:to-purple-700 text-white py-3 rounded-lg font-medium transition-all duration-300 flex items-center justify-center">
                                <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M8 5v14l11-7z"/>
                                </svg>
                                Watch Tutorial
                            </button>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif

        <!-- Contact Support Tab -->
        @if($activeTab === 'contact')
            <div class="space-y-8">
                <!-- Contact Options -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl border border-gray-200 dark:border-gray-700 p-6 text-center">
                        <div class="w-16 h-16 bg-gradient-to-br from-blue-500 to-indigo-500 rounded-full flex items-center justify-center mx-auto mb-4">
                            <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                            </svg>
                        </div>
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">Email Support</h3>
                        <p class="text-gray-600 dark:text-gray-400 mb-4">Get help via email within 24 hours</p>
                        <button wire:click="openContactModal"
                                class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg font-medium transition-colors">
                            Send Email
                        </button>
                    </div>

                    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl border border-gray-200 dark:border-gray-700 p-6 text-center">
                        <div class="w-16 h-16 bg-gradient-to-br from-green-500 to-teal-500 rounded-full flex items-center justify-center mx-auto mb-4">
                            <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                            </svg>
                        </div>
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">Live Chat</h3>
                        <p class="text-gray-600 dark:text-gray-400 mb-4">Chat with our support team</p>
                        <button class="bg-green-600 hover:bg-green-700 text-white px-6 py-2 rounded-lg font-medium transition-colors">
                            Start Chat
                        </button>
                    </div>

                    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl border border-gray-200 dark:border-gray-700 p-6 text-center">
                        <div class="w-16 h-16 bg-gradient-to-br from-purple-500 to-pink-500 rounded-full flex items-center justify-center mx-auto mb-4">
                            <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/>
                            </svg>
                        </div>
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">Send Feedback</h3>
                        <p class="text-gray-600 dark:text-gray-400 mb-4">Help us improve our platform</p>
                        <button wire:click="openFeedbackModal"
                                class="bg-purple-600 hover:bg-purple-700 text-white px-6 py-2 rounded-lg font-medium transition-colors">
                            Give Feedback
                        </button>
                    </div>
                </div>

                <!-- System Status -->
                <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl border border-gray-200 dark:border-gray-700 p-6">
                    <div class="flex items-center justify-between mb-6">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">System Status</h3>
                        <div class="flex items-center">
                            <div class="w-3 h-3 bg-green-500 rounded-full mr-2"></div>
                            <span class="text-sm text-green-600 dark:text-green-400 font-medium">All Systems Operational</span>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                        @foreach($systemStatus['services'] as $service => $status)
                            <div class="flex items-center justify-between p-3 bg-gray-50 dark:bg-gray-700 rounded-lg">
                                <span class="text-sm font-medium text-gray-900 dark:text-white capitalize">{{ str_replace('_', ' ', $service) }}</span>
                                <div class="flex items-center">
                                    <div class="w-2 h-2 bg-green-500 rounded-full mr-2"></div>
                                    <span class="text-sm text-green-600 dark:text-green-400">Operational</span>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-4">
                        Last updated: {{ $systemStatus['last_updated']->format('M j, Y \a\t g:i A') }}
                    </p>
                </div>

                <!-- Recent Updates -->
                <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl border border-gray-200 dark:border-gray-700 p-6">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-6">Recent Updates</h3>

                    <div class="space-y-4">
                        @foreach($recentUpdates as $update)
                            <div class="flex items-start">
                                <div class="flex-shrink-0 w-8 h-8 rounded-full flex items-center justify-center
                                     {{ $update['type'] === 'feature' ? 'bg-blue-100 dark:bg-blue-900/50' :
                                        ($update['type'] === 'announcement' ? 'bg-green-100 dark:bg-green-900/50' : 'bg-purple-100 dark:bg-purple-900/50') }}">
                                    @if($update['type'] === 'feature')
                                        <svg class="w-4 h-4 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                                        </svg>
                                    @elseif($update['type'] === 'announcement')
                                        <svg class="w-4 h-4 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/>
                                        </svg>
                                    @else
                                        <svg class="w-4 h-4 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                        </svg>
                                    @endif
                                </div>
                                <div class="ml-4 flex-1">
                                    <h4 class="text-sm font-medium text-gray-900 dark:text-white">{{ $update['title'] }}</h4>
                                    <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">{{ $update['description'] }}</p>
                                    <p class="text-xs text-gray-500 dark:text-gray-500 mt-2">{{ $update['date']->diffForHumans() }}</p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        @endif
    </div>

    <!-- Contact Support Modal -->
    @if($showContactModal)
        <div class="fixed inset-0 z-50 overflow-y-auto">
            <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" wire:click="closeContactModal"></div>

                <div class="inline-block align-bottom bg-white dark:bg-gray-800 rounded-2xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-2xl sm:w-full">
                    <div class="bg-gradient-to-r from-blue-500 to-indigo-500 px-6 py-4">
                        <div class="flex items-center justify-between">
                            <h3 class="text-lg font-bold text-white">Contact Support</h3>
                            <button wire:click="closeContactModal" class="text-white hover:text-gray-200">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                </svg>
                            </button>
                        </div>
                    </div>

                    <form wire:submit.prevent="submitContact" class="p-6">
                        <div class="space-y-4">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Name</label>
                                    <input wire:model="contactName" type="text"
                                           class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white"
                                           placeholder="Your full name">
                                    @error('contactName') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Email</label>
                                    <input wire:model="contactEmail" type="email"
                                           class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white"
                                           placeholder="Your email address">
                                    @error('contactEmail') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                                </div>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Category</label>
                                    <select wire:model="contactCategory"
                                            class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white">
                                        <option value="general">General Question</option>
                                        <option value="technical">Technical Issue</option>
                                        <option value="billing">Billing Support</option>
                                        <option value="account">Account Problem</option>
                                        <option value="bug">Bug Report</option>
                                    </select>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Priority</label>
                                    <select wire:model="contactPriority"
                                            class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white">
                                        <option value="low">Low</option>
                                        <option value="medium">Medium</option>
                                        <option value="high">High</option>
                                        <option value="urgent">Urgent</option>
                                    </select>
                                </div>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Subject</label>
                                <input wire:model="contactSubject" type="text"
                                       class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white"
                                       placeholder="Brief description of your issue">
                                @error('contactSubject') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Message</label>
                                <textarea wire:model="contactMessage" rows="6"
                                          class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white"
                                          placeholder="Please describe your issue in detail..."></textarea>
                                @error('contactMessage') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                            </div>
                        </div>

                        <div class="mt-6 flex justify-end space-x-3">
                            <button type="button" wire:click="closeContactModal"
                                    class="px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700">
                                Cancel
                            </button>
                            <button type="submit"
                                    class="px-6 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-medium">
                                Send Message
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif

    <!-- Feedback Modal -->
    @if($showFeedbackModal)
        <div class="fixed inset-0 z-50 overflow-y-auto">
            <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" wire:click="closeFeedbackModal"></div>

                <div class="inline-block align-bottom bg-white dark:bg-gray-800 rounded-2xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-2xl sm:w-full">
                    <div class="bg-gradient-to-r from-purple-500 to-pink-500 px-6 py-4">
                        <div class="flex items-center justify-between">
                            <h3 class="text-lg font-bold text-white">Send Feedback</h3>
                            <button wire:click="closeFeedbackModal" class="text-white hover:text-gray-200">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                </svg>
                            </button>
                        </div>
                    </div>

                    <form wire:submit.prevent="submitFeedback" class="p-6">
                        <div class="space-y-4">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Feedback Type</label>
                                    <select wire:model="feedbackType"
                                            class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500 dark:bg-gray-700 dark:text-white">
                                        <option value="bug">Bug Report</option>
                                        <option value="feature">Feature Request</option>
                                        <option value="improvement">Improvement Suggestion</option>
                                        <option value="other">Other</option>
                                    </select>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Rating</label>
                                    <select wire:model="feedbackRating"
                                            class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500 dark:bg-gray-700 dark:text-white">
                                        <option value="5">⭐⭐⭐⭐⭐ Excellent</option>
                                        <option value="4">⭐⭐⭐⭐ Good</option>
                                        <option value="3">⭐⭐⭐ Average</option>
                                        <option value="2">⭐⭐ Poor</option>
                                        <option value="1">⭐ Very Poor</option>
                                    </select>
                                </div>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Title</label>
                                <input wire:model="feedbackTitle" type="text"
                                       class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500 dark:bg-gray-700 dark:text-white"
                                       placeholder="Brief title for your feedback">
                                @error('feedbackTitle') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Description</label>
                                <textarea wire:model="feedbackDescription" rows="6"
                                          class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500 dark:bg-gray-700 dark:text-white"
                                          placeholder="Please describe your feedback in detail..."></textarea>
                                @error('feedbackDescription') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                            </div>
                        </div>

                        <div class="mt-6 flex justify-end space-x-3">
                            <button type="button" wire:click="closeFeedbackModal"
                                    class="px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700">
                                Cancel
                            </button>
                            <button type="submit"
                                    class="px-6 py-2 bg-purple-600 hover:bg-purple-700 text-white rounded-lg font-medium">
                                Send Feedback
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif

    <!-- Tutorial Modal -->
    @if($showTutorialModal && $selectedTutorial)
        <div class="fixed inset-0 z-50 overflow-y-auto">
            <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" wire:click="closeTutorialModal"></div>

                <div class="inline-block align-bottom bg-white dark:bg-gray-800 rounded-2xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-4xl sm:w-full">
                    <div class="bg-gradient-to-r from-indigo-500 to-purple-500 px-6 py-4">
                        <div class="flex items-center justify-between">
                            <h3 class="text-lg font-bold text-white">{{ $selectedTutorial['title'] }}</h3>
                            <button wire:click="closeTutorialModal" class="text-white hover:text-gray-200">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                </svg>
                            </button>
                        </div>
                    </div>

                    <div class="p-6">
                        <div class="aspect-video bg-gray-200 dark:bg-gray-700 rounded-lg mb-4 flex items-center justify-center">
                            <div class="text-center">
                                <div class="w-16 h-16 bg-indigo-100 dark:bg-indigo-900 rounded-full flex items-center justify-center mx-auto mb-4">
                                    <svg class="w-8 h-8 text-indigo-600 dark:text-indigo-400" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M8 5v14l11-7z"/>
                                    </svg>
                                </div>
                                <p class="text-gray-600 dark:text-gray-400">Video tutorial will be loaded here</p>
                            </div>
                        </div>

                        <div class="flex items-center justify-between text-sm text-gray-600 dark:text-gray-400 mb-4">
                            <div class="flex items-center space-x-4">
                                <span>Duration: {{ $selectedTutorial['duration'] }}</span>
                                <span>Steps: {{ $selectedTutorial['steps'] }}</span>
                                <span>Level: {{ $selectedTutorial['difficulty'] }}</span>
                            </div>
                        </div>

                        <p class="text-gray-700 dark:text-gray-300">{{ $selectedTutorial['description'] }}</p>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
