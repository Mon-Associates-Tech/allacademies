<div class="min-h-screen bg-gradient-to-br from-indigo-50 via-white to-purple-50 dark:from-gray-900 dark:via-gray-800 dark:to-gray-900">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Enhanced Header Section -->
        <div class="relative mb-8 overflow-hidden rounded-3xl">
            <div class="absolute inset-0 bg-gradient-to-r from-indigo-600 via-purple-600 to-pink-600 opacity-90"></div>
            <div class="absolute inset-0 bg-black opacity-20"></div>
            <div class="absolute inset-0" style="background-image: url('data:image/svg+xml;charset=utf-8,%3Csvg xmlns=\'http://www.w3.org/2000/svg\' width=\'60\' height=\'60\' viewBox=\'0 0 60 60\'%3E%3Cg fill=\'%23ffffff\' fill-opacity=\'0.1\'%3E%3Ccircle cx=\'30\' cy=\'30\' r=\'20\'/%3E%3Ccircle cx=\'30\' cy=\'30\' r=\'10\'/%3E%3C/g%3E%3C/svg%3E')"></div>

            <div class="relative px-8 py-12">
                <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between">
                    <div class="mb-6 lg:mb-0">
                        <div class="flex items-center mb-4">
                            <div class="p-3 bg-white/20 rounded-full backdrop-blur-sm mr-4">
                                <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                                </svg>
                            </div>
                            <div>
                                <h1 class="text-3xl lg:text-4xl font-bold text-white mb-2">Authors Community</h1>
                                <p class="text-purple-100 text-lg">Connect, collaborate, and grow together</p>
                            </div>
                        </div>
                    </div>

                    <!-- Community Stats -->
                    <div class="grid grid-cols-2 gap-4">
                        <div class="bg-white/20 backdrop-blur-sm rounded-xl p-4 border border-white/30">
                            <div class="text-center">
                                <p class="text-2xl font-bold text-white">{{ $communityStats['total_authors'] }}</p>
                                <p class="text-purple-100 text-sm">Active Authors</p>
                            </div>
                        </div>

                        <div class="bg-white/20 backdrop-blur-sm rounded-xl p-4 border border-white/30">
                            <div class="text-center">
                                <p class="text-2xl font-bold text-white">{{ $communityStats['active_discussions'] }}</p>
                                <p class="text-purple-100 text-sm">Discussions</p>
                            </div>
                        </div>

                        <div class="bg-white/20 backdrop-blur-sm rounded-xl p-4 border border-white/30">
                            <div class="text-center">
                                <p class="text-2xl font-bold text-white">{{ $communityStats['upcoming_events'] }}</p>
                                <p class="text-purple-100 text-sm">Events</p>
                            </div>
                        </div>

                        <div class="bg-white/20 backdrop-blur-sm rounded-xl p-4 border border-white/30">
                            <div class="text-center">
                                <p class="text-2xl font-bold text-white">{{ $communityStats['collaboration_projects'] }}</p>
                                <p class="text-purple-100 text-sm">Projects</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Enhanced Tab Navigation -->
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl border border-gray-200 dark:border-gray-700 mb-8 overflow-hidden">
            <div class="flex flex-wrap border-b border-gray-200 dark:border-gray-700">
                <button wire:click="setActiveTab('network')"
                        class="flex-1 px-6 py-4 text-sm font-medium transition-all duration-200 {{ $activeTab === 'network' ? 'bg-indigo-50 dark:bg-indigo-900/20 text-indigo-700 dark:text-indigo-300 border-b-2 border-indigo-500' : 'text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700' }}">
                    <div class="flex items-center justify-center">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                        </svg>
                        Network
                    </div>
                </button>

                <button wire:click="setActiveTab('discussions')"
                        class="flex-1 px-6 py-4 text-sm font-medium transition-all duration-200 {{ $activeTab === 'discussions' ? 'bg-indigo-50 dark:bg-indigo-900/20 text-indigo-700 dark:text-indigo-300 border-b-2 border-indigo-500' : 'text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700' }}">
                    <div class="flex items-center justify-center">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                        </svg>
                        Discussions
                    </div>
                </button>

                <button wire:click="setActiveTab('events')"
                        class="flex-1 px-6 py-4 text-sm font-medium transition-all duration-200 {{ $activeTab === 'events' ? 'bg-indigo-50 dark:bg-indigo-900/20 text-indigo-700 dark:text-indigo-300 border-b-2 border-indigo-500' : 'text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700' }}">
                    <div class="flex items-center justify-center">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                        Events
                    </div>
                </button>

                <button wire:click="setActiveTab('collaborations')"
                        class="flex-1 px-6 py-4 text-sm font-medium transition-all duration-200 {{ $activeTab === 'collaborations' ? 'bg-indigo-50 dark:bg-indigo-900/20 text-indigo-700 dark:text-indigo-300 border-b-2 border-indigo-500' : 'text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700' }}">
                    <div class="flex items-center justify-center">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"/>
                        </svg>
                        Collaborations
                    </div>
                </button>
            </div>
        </div>

        <!-- Network Tab Content -->
        @if($activeTab === 'network')
            <div class="space-y-8">
                <!-- Featured Authors -->
                <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl border border-gray-200 dark:border-gray-700 p-6">
                    <div class="flex items-center justify-between mb-6">
                        <h2 class="text-xl font-bold text-gray-900 dark:text-white">Featured Authors</h2>
                        <button class="text-indigo-600 hover:text-indigo-700 text-sm font-medium">View All</button>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        @foreach($featuredAuthors as $author)
                            <div class="bg-gradient-to-br from-indigo-50 to-purple-50 dark:from-indigo-900/20 dark:to-purple-900/20 rounded-xl p-6 border border-indigo-100 dark:border-indigo-800 hover:shadow-lg transition-all duration-300">
                                <div class="flex items-center mb-4">
                                    <div class="h-12 w-12 rounded-full bg-gradient-to-br from-indigo-500 to-purple-500 flex items-center justify-center text-white font-bold text-lg">
                                        {{ strtoupper(substr($author->name, 0, 1)) }}
                                    </div>
                                    <div class="ml-4">
                                        <h3 class="font-semibold text-gray-900 dark:text-white">{{ $author->name }}</h3>
                                        <p class="text-sm text-gray-600 dark:text-gray-400">{{ $author->books_count ?? 0 }} books</p>
                                    </div>
                                </div>

                                <p class="text-sm text-gray-600 dark:text-gray-400 mb-4 line-clamp-2">
                                    {{ $author->biography ? Str::limit($author->biography, 100) : 'No biography available.' }}
                                </p>

                                <div class="flex space-x-2">
                                    <button wire:click="connectWithAuthor({{ $author->id }})"
                                            class="flex-1 bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors">
                                        Connect
                                    </button>
                                    <button class="px-4 py-2 border border-indigo-200 dark:border-indigo-700 text-indigo-600 dark:text-indigo-400 hover:bg-indigo-50 dark:hover:bg-indigo-900/20 rounded-lg text-sm font-medium transition-colors">
                                        View Profile
                                    </button>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <!-- Network Suggestions -->
                <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl border border-gray-200 dark:border-gray-700 p-6">
                    <h2 class="text-xl font-bold text-gray-900 dark:text-white mb-6">People You May Know</h2>

                    <div class="space-y-4">
                        @foreach($networkSuggestions as $suggestion)
                            <div class="flex items-center justify-between p-4 bg-gray-50 dark:bg-gray-700 rounded-xl">
                                <div class="flex items-center">
                                    <div class="h-10 w-10 rounded-full bg-gradient-to-br from-purple-500 to-pink-500 flex items-center justify-center text-white font-bold">
                                        {{ strtoupper(substr($suggestion->name, 0, 1)) }}
                                    </div>
                                    <div class="ml-4">
                                        <p class="font-medium text-gray-900 dark:text-white">{{ $suggestion->name }}</p>
                                        <p class="text-sm text-gray-600 dark:text-gray-400">{{ $suggestion->books_count ?? 0 }} books published</p>
                                    </div>
                                </div>

                                <button wire:click="connectWithAuthor({{ $suggestion->id }})"
                                        class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors">
                                    Connect
                                </button>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        @endif

        <!-- Discussions Tab Content -->
        @if($activeTab === 'discussions')
            <div class="space-y-8">
                <!-- Create Post Button -->
                <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl border border-gray-200 dark:border-gray-700 p-6">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center">
                            <div class="h-10 w-10 rounded-full bg-gradient-to-br from-indigo-500 to-purple-500 flex items-center justify-center text-white font-bold">
                                {{ strtoupper(substr($author->name, 0, 1)) }}
                            </div>
                            <div class="ml-4">
                                <p class="font-medium text-gray-900 dark:text-white">Share your thoughts with the community</p>
                                <p class="text-sm text-gray-600 dark:text-gray-400">Start a discussion or ask a question</p>
                            </div>
                        </div>

                        <button wire:click="openCreatePostModal"
                                class="bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-3 rounded-xl font-medium transition-colors flex items-center">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                            </svg>
                            Create Post
                        </button>
                    </div>
                </div>

                <!-- Recent Posts -->
                <div class="space-y-6">
                    @foreach($recentPosts as $post)
                        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl border border-gray-200 dark:border-gray-700 overflow-hidden">
                            <div class="p-6">
                                <div class="flex items-start justify-between mb-4">
                                    <div class="flex items-center">
                                        <div class="h-10 w-10 rounded-full bg-gradient-to-br from-purple-500 to-pink-500 flex items-center justify-center text-white font-bold">
                                            {{ strtoupper(substr($post['author']['name'], 0, 1)) }}
                                        </div>
                                        <div class="ml-4">
                                            <p class="font-medium text-gray-900 dark:text-white">{{ $post['author']['name'] }}</p>
                                            <p class="text-sm text-gray-600 dark:text-gray-400">{{ $post['created_at']->diffForHumans() }}</p>
                                        </div>
                                    </div>

                                    <div class="flex space-x-2">
                                        @foreach($post['tags'] as $tag)
                                            <span class="px-2 py-1 bg-indigo-100 dark:bg-indigo-900/30 text-indigo-700 dark:text-indigo-300 text-xs rounded-full">
                                                #{{ $tag }}
                                            </span>
                                        @endforeach
                                    </div>
                                </div>

                                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">{{ $post['title'] }}</h3>
                                <p class="text-gray-600 dark:text-gray-400 mb-4">{{ $post['content'] }}</p>

                                <div class="flex items-center justify-between pt-4 border-t border-gray-200 dark:border-gray-600">
                                    <div class="flex items-center space-x-6">
                                        <button wire:click="likePost({{ $post['id'] }})"
                                                class="flex items-center space-x-2 text-gray-600 dark:text-gray-400 hover:text-red-500 transition-colors">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
                                            </svg>
                                            <span>{{ $post['likes'] }}</span>
                                        </button>

                                        <button class="flex items-center space-x-2 text-gray-600 dark:text-gray-400 hover:text-blue-500 transition-colors">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                                            </svg>
                                            <span>{{ $post['comments'] }}</span>
                                        </button>
                                    </div>

                                    <button class="text-indigo-600 hover:text-indigo-700 font-medium text-sm">
                                        Read More
                                    </button>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        <!-- Events Tab Content -->
        @if($activeTab === 'events')
            <div class="space-y-8">
                <!-- Create Event Button -->
                <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl border border-gray-200 dark:border-gray-700 p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <h2 class="text-xl font-bold text-gray-900 dark:text-white">Community Events</h2>
                            <p class="text-gray-600 dark:text-gray-400">Workshops, meetups, and networking events</p>
                        </div>

                        <button wire:click="openCreateEventModal"
                                class="bg-purple-600 hover:bg-purple-700 text-white px-6 py-3 rounded-xl font-medium transition-colors flex items-center">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                            </svg>
                            Create Event
                        </button>
                    </div>
                </div>

                <!-- Upcoming Events -->
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    @foreach($upcomingEvents as $event)
                        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl border border-gray-200 dark:border-gray-700 overflow-hidden">
                            <div class="bg-gradient-to-r from-purple-500 to-pink-500 p-6 text-white">
                                <div class="flex items-center justify-between mb-4">
                                    <div class="flex items-center">
                                        <div class="p-2 bg-white/20 rounded-lg">
                                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                            </svg>
                                        </div>
                                        <div class="ml-4">
                                            <p class="font-semibold">{{ $event['date']->format('M j, Y') }}</p>
                                            <p class="text-purple-100">{{ $event['time'] }}</p>
                                        </div>
                                    </div>

                                    <span class="px-3 py-1 bg-white/20 rounded-full text-sm">
                                        {{ ucfirst($event['type']) }}
                                    </span>
                                </div>

                                <h3 class="text-xl font-bold mb-2">{{ $event['title'] }}</h3>
                                <p class="text-purple-100">{{ $event['description'] }}</p>
                            </div>

                            <div class="p-6">
                                <div class="flex items-center justify-between mb-4">
                                    <div class="flex items-center">
                                        <div class="h-8 w-8 rounded-full bg-gradient-to-br from-purple-500 to-pink-500 flex items-center justify-center text-white font-bold text-sm">
                                            {{ strtoupper(substr($event['host']['name'], 0, 1)) }}
                                        </div>
                                        <div class="ml-3">
                                            <p class="text-sm font-medium text-gray-900 dark:text-white">{{ $event['host']['name'] }}</p>
                                            <p class="text-xs text-gray-600 dark:text-gray-400">Host</p>
                                        </div>
                                    </div>

                                    <div class="text-right">
                                        <p class="text-sm font-medium text-gray-900 dark:text-white">{{ $event['attendees'] }}/{{ $event['max_attendees'] }}</p>
                                        <p class="text-xs text-gray-600 dark:text-gray-400">Attendees</p>
                                    </div>
                                </div>

                                @if(isset($event['location']))
                                    <div class="flex items-center mb-4 text-gray-600 dark:text-gray-400">
                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                                        </svg>
                                        <span class="text-sm">{{ $event['location'] }}</span>
                                    </div>
                                @endif

                                <button wire:click="joinEvent({{ $event['id'] }})"
                                        class="w-full bg-purple-600 hover:bg-purple-700 text-white py-3 rounded-lg font-medium transition-colors {{ $event['attendees'] >= $event['max_attendees'] ? 'opacity-50 cursor-not-allowed' : '' }}"
                                    {{ $event['attendees'] >= $event['max_attendees'] ? 'disabled' : '' }}>
                                    {{ $event['attendees'] >= $event['max_attendees'] ? 'Event Full' : 'Join Event' }}
                                </button>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        <!-- Collaborations Tab Content -->
        @if($activeTab === 'collaborations')
            <div class="space-y-8">
                <!-- Collaboration Header -->
                <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl border border-gray-200 dark:border-gray-700 p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <h2 class="text-xl font-bold text-gray-900 dark:text-white">Collaboration Projects</h2>
                            <p class="text-gray-600 dark:text-gray-400">Find writing partners and join group projects</p>
                        </div>

                        <button class="bg-green-600 hover:bg-green-700 text-white px-6 py-3 rounded-xl font-medium transition-colors flex items-center">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                            </svg>
                            Start Project
                        </button>
                    </div>
                </div>

                <!-- Active Collaborations -->
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    @foreach($collaborations as $collaboration)
                        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl border border-gray-200 dark:border-gray-700 overflow-hidden">
                            <div class="bg-gradient-to-r from-green-500 to-teal-500 p-6 text-white">
                                <div class="flex items-center justify-between mb-4">
                                    <div class="flex flex-wrap gap-2">
                                        @foreach($collaboration['genres'] as $genre)
                                            <span class="px-2 py-1 bg-white/20 rounded-full text-xs">{{ $genre }}</span>
                                        @endforeach
                                    </div>

                                    <div class="text-right">
                                        <p class="text-sm font-medium">{{ $collaboration['spots_available'] }} spots left</p>
                                        <p class="text-xs text-green-100">of {{ $collaboration['total_spots'] }} total</p>
                                    </div>
                                </div>

                                <h3 class="text-xl font-bold mb-2">{{ $collaboration['title'] }}</h3>
                                <p class="text-green-100">{{ $collaboration['description'] }}</p>
                            </div>

                            <div class="p-6">
                                <div class="flex items-center justify-between mb-4">
                                    <div class="flex items-center">
                                        <div class="h-8 w-8 rounded-full bg-gradient-to-br from-green-500 to-teal-500 flex items-center justify-center text-white font-bold text-sm">
                                            {{ strtoupper(substr($collaboration['coordinator']['name'], 0, 1)) }}
                                        </div>
                                        <div class="ml-3">
                                            <p class="text-sm font-medium text-gray-900 dark:text-white">{{ $collaboration['coordinator']['name'] }}</p>
                                            <p class="text-xs text-gray-600 dark:text-gray-400">Project Coordinator</p>
                                        </div>
                                    </div>

                                    <div class="text-right">
                                        <p class="text-sm font-medium text-gray-900 dark:text-white">{{ $collaboration['deadline']->format('M j, Y') }}</p>
                                        <p class="text-xs text-gray-600 dark:text-gray-400">Deadline</p>
                                    </div>
                                </div>

                                <div class="mb-4">
                                    <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2">
                                        <div class="bg-gradient-to-r from-green-500 to-teal-500 h-2 rounded-full transition-all duration-300"
                                             style="width: {{ (($collaboration['total_spots'] - $collaboration['spots_available']) / $collaboration['total_spots']) * 100 }}%">
                                        </div>
                                    </div>
                                    <p class="text-xs text-gray-600 dark:text-gray-400 mt-1">
                                        {{ $collaboration['total_spots'] - $collaboration['spots_available'] }} of {{ $collaboration['total_spots'] }} positions filled
                                    </p>
                                </div>

                                <button wire:click="joinCollaboration({{ $collaboration['id'] }})"
                                        class="w-full bg-green-600 hover:bg-green-700 text-white py-3 rounded-lg font-medium transition-colors {{ $collaboration['spots_available'] == 0 ? 'opacity-50 cursor-not-allowed' : '' }}"
                                    {{ $collaboration['spots_available'] == 0 ? 'disabled' : '' }}>
                                    {{ $collaboration['spots_available'] == 0 ? 'Project Full' : 'Join Project' }}
                                </button>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif
    </div>

    <!-- Create Post Modal -->
    @if($showCreatePostModal)
        <div class="fixed inset-0 z-50 overflow-y-auto">
            <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" wire:click="closeCreatePostModal"></div>

                <div class="inline-block align-bottom bg-white dark:bg-gray-800 rounded-2xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-2xl sm:w-full">
                    <div class="bg-gradient-to-r from-indigo-500 to-purple-500 px-6 py-4">
                        <div class="flex items-center justify-between">
                            <h3 class="text-lg font-bold text-white">Create New Post</h3>
                            <button wire:click="closeCreatePostModal" class="text-white hover:text-gray-200">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                </svg>
                            </button>
                        </div>
                    </div>

                    <form wire:submit.prevent="createPost" class="p-6">
                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Post Title</label>
                                <input wire:model="postTitle" type="text"
                                       class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-700 dark:text-white"
                                       placeholder="Enter a compelling title...">
                                @error('postTitle') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Content</label>
                                <textarea wire:model="postContent" rows="6"
                                          class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-700 dark:text-white"
                                          placeholder="Share your thoughts, ask questions, or start a discussion..."></textarea>
                                @error('postContent') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                            </div>
                        </div>

                        <div class="mt-6 flex justify-end space-x-3">
                            <button type="button" wire:click="closeCreatePostModal"
                                    class="px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700">
                                Cancel
                            </button>
                            <button type="submit"
                                    class="px-6 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg font-medium">
                                Create Post
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif

    <!-- Create Event Modal -->
    @if($showCreateEventModal)
        <div class="fixed inset-0 z-50 overflow-y-auto">
            <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" wire:click="closeCreateEventModal"></div>

                <div class="inline-block align-bottom bg-white dark:bg-gray-800 rounded-2xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-2xl sm:w-full">
                    <div class="bg-gradient-to-r from-purple-500 to-pink-500 px-6 py-4">
                        <div class="flex items-center justify-between">
                            <h3 class="text-lg font-bold text-white">Create New Event</h3>
                            <button wire:click="closeCreateEventModal" class="text-white hover:text-gray-200">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                </svg>
                            </button>
                        </div>
                    </div>

                    <form wire:submit.prevent="createEvent" class="p-6">
                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Event Title</label>
                                <input wire:model="eventTitle" type="text"
                                       class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500 dark:bg-gray-700 dark:text-white"
                                       placeholder="Enter event title...">
                                @error('eventTitle') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Description</label>
                                <textarea wire:model="eventDescription" rows="4"
                                          class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500 dark:bg-gray-700 dark:text-white"
                                          placeholder="Describe your event..."></textarea>
                                @error('eventDescription') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Date</label>
                                    <input wire:model="eventDate" type="date"
                                           class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500 dark:bg-gray-700 dark:text-white">
                                    @error('eventDate') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Time</label>
                                    <input wire:model="eventTime" type="time"
                                           class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500 dark:bg-gray-700 dark:text-white">
                                    @error('eventTime') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                                </div>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Event Type</label>
                                <select wire:model="eventType"
                                        class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500 dark:bg-gray-700 dark:text-white">
                                    <option value="virtual">Virtual</option>
                                    <option value="in-person">In-Person</option>
                                </select>
                            </div>

                            @if($eventType === 'in-person')
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Location</label>
                                    <input wire:model="eventLocation" type="text"
                                           class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500 dark:bg-gray-700 dark:text-white"
                                           placeholder="Enter event location...">
                                    @error('eventLocation') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                                </div>
                            @endif
                        </div>

                        <div class="mt-6 flex justify-end space-x-3">
                            <button type="button" wire:click="closeCreateEventModal"
                                    class="px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700">
                                Cancel
                            </button>
                            <button type="submit"
                                    class="px-6 py-2 bg-purple-600 hover:bg-purple-700 text-white rounded-lg font-medium">
                                Create Event
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif
</div>
