<template>
    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900 flex items-center">
                        <div class="p-3 bg-gradient-to-r from-purple-500 to-blue-500 rounded-xl mr-4">
                            <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                        </div>
                        Keyword Research & SERP Intelligence
                    </h1>
                    <p class="text-gray-600 text-lg mt-2">Discover high-value keywords and analyze competitor strategies</p>
                </div>
                <div class="flex items-center gap-3">
                    <button @click="showKeywordHistory" class="px-4 py-2 text-gray-600 hover:text-gray-900 transition-colors flex items-center">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        History
                    </button>
                    <button @click="exportKeywords" class="px-6 py-3 bg-gradient-to-r from-green-600 to-emerald-600 text-white rounded-xl hover:from-green-700 hover:to-emerald-700 transition-all shadow-lg hover:shadow-xl transform hover:-translate-y-1 flex items-center">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        Export CSV
                    </button>
                </div>
            </div>
        </template>

        <div class="space-y-8">
            <!-- Advanced Keyword Search Engine -->
            <div class="bg-gradient-to-br from-blue-50 via-purple-50 to-pink-50 rounded-2xl p-8 border-2 border-blue-200">
                <div class="max-w-4xl mx-auto">
                    <h2 class="text-2xl font-bold text-gray-900 mb-6 text-center">🎯 Discover Your Next Winning Keywords</h2>

                    <div class="relative mb-6">
                        <div class="absolute inset-y-0 left-0 pl-6 flex items-center pointer-events-none">
                            <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                        </div>
                        <input
                            v-model="searchKeyword"
                            @keyup.enter="searchKeywords"
                            @input="showSuggestions"
                            type="text"
                            placeholder="Enter your seed keyword (e.g., 'pork chop recipes', 'digital marketing')"
                            class="w-full pl-16 pr-6 py-6 text-lg border-2 border-gray-300 rounded-2xl focus:outline-none focus:ring-4 focus:ring-blue-500/20 focus:border-blue-500 bg-white shadow-lg placeholder-gray-400"
                        >

                        <!-- Keyword Suggestions Dropdown -->
                        <div v-if="showSuggestionsDropdown && keywordSuggestions.length"
                            class="absolute top-full left-0 right-0 mt-2 bg-white border-2 border-gray-200 rounded-xl shadow-xl z-10 max-h-60 overflow-y-auto">
                            <div v-for="suggestion in keywordSuggestions" :key="suggestion"
                                @click="selectSuggestion(suggestion)"
                                class="px-6 py-3 hover:bg-blue-50 cursor-pointer border-b border-gray-100 last:border-b-0 flex items-center">
                                <svg class="w-4 h-4 text-gray-400 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                </svg>
                                {{ suggestion }}
                            </div>
                        </div>
                    </div>

                    <div class="flex flex-wrap items-center justify-center gap-4 mb-6">
                        <div class="flex items-center">
                            <label class="text-sm font-medium text-gray-700 mr-3">Location:</label>
                            <select v-model="selectedLocation" class="px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                                <option value="US">🇺🇸 United States</option>
                                <option value="UK">🇬🇧 United Kingdom</option>
                                <option value="CA">🇨🇦 Canada</option>
                                <option value="AU">🇦🇺 Australia</option>
                                <option value="DE">🇩🇪 Germany</option>
                                <option value="FR">🇫🇷 France</option>
                            </select>
                        </div>
                        <div class="flex items-center">
                            <label class="text-sm font-medium text-gray-700 mr-3">Language:</label>
                            <select v-model="selectedLanguage" class="px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                                <option value="en">English</option>
                                <option value="es">Spanish</option>
                                <option value="fr">French</option>
                                <option value="de">German</option>
                                <option value="it">Italian</option>
                            </select>
                        </div>
                    </div>

                    <div class="text-center">
                        <button @click="searchKeywords"
                            :disabled="!searchKeyword.trim() || isSearching"
                            class="px-12 py-4 bg-gradient-to-r from-blue-600 to-purple-600 text-white text-lg font-bold rounded-2xl hover:from-blue-700 hover:to-purple-700 transition-all duration-200 shadow-lg hover:shadow-xl transform hover:-translate-y-1 disabled:opacity-50 disabled:cursor-not-allowed disabled:transform-none flex items-center mx-auto">
                            <div v-if="isSearching" class="animate-spin w-5 h-5 border-2 border-white border-t-transparent rounded-full mr-3"></div>
                            <svg v-else class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                            </svg>
                            {{ isSearching ? 'Analyzing Keywords...' : 'Analyze Keywords' }}
                        </button>
                    </div>

                    <!-- Quick Action Buttons -->
                    <div class="flex flex-wrap justify-center gap-3 mt-6">
                        <button v-for="quickKeyword in quickKeywords" :key="quickKeyword"
                            @click="quickSearch(quickKeyword)"
                            class="px-4 py-2 bg-white/70 backdrop-blur-sm border border-gray-300 rounded-full text-sm font-medium text-gray-700 hover:bg-white hover:border-blue-300 transition-all">
                            {{ quickKeyword }}
                        </button>
                    </div>
                </div>
            </div>

            <!-- Keyword Overview Section -->
            <div v-if="currentKeywordData" class="bg-white rounded-2xl border-2 border-gray-200 overflow-hidden shadow-sm">
                <div class="bg-gradient-to-r from-gray-50 to-blue-50 px-8 py-6 border-b border-gray-200">
                    <div class="flex items-center justify-between">
                        <div>
                            <h3 class="text-2xl font-bold text-gray-900 flex items-center">
                                <span class="text-3xl mr-3">🔍</span>
                                Keyword Analysis: "{{ currentKeywordData.keyword }}"
                            </h3>
                            <p class="text-gray-600 mt-2">Comprehensive insights and competitive intelligence</p>
                        </div>
                        <div class="flex items-center gap-3">
                            <button @click="addToTracking" class="px-4 py-2 bg-blue-100 text-blue-700 rounded-lg hover:bg-blue-200 transition-colors flex items-center">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                                </svg>
                                Track Keyword
                            </button>
                            <button @click="generateContentBrief" class="px-4 py-2 bg-purple-100 text-purple-700 rounded-lg hover:bg-purple-200 transition-colors flex items-center">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z" />
                                </svg>
                                AI Content Brief
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Key Metrics Cards -->
                <div class="p-8">
                    <div class="grid grid-cols-2 md:grid-cols-5 gap-6 mb-8">
                        <div class="bg-gradient-to-br from-blue-50 to-blue-100 rounded-2xl p-6 border-2 border-blue-200">
                            <div class="flex items-center justify-between mb-3">
                                <div class="p-3 bg-blue-500 rounded-xl">
                                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                    </svg>
                                </div>
                                <span class="text-xs font-bold bg-blue-200 text-blue-800 px-2 py-1 rounded-full">Monthly</span>
                            </div>
                            <div class="text-3xl font-black text-blue-700 mb-1">{{ currentKeywordData.searchVolume.toLocaleString() }}</div>
                            <div class="text-sm font-semibold text-blue-600">Search Volume</div>
                        </div>

                        <div class="bg-gradient-to-br from-green-50 to-emerald-100 rounded-2xl p-6 border-2 border-green-200">
                            <div class="flex items-center justify-between mb-3">
                                <div class="p-3 bg-green-500 rounded-xl">
                                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1" />
                                    </svg>
                                </div>
                                <span class="text-xs font-bold bg-green-200 text-green-800 px-2 py-1 rounded-full">USD</span>
                            </div>
                            <div class="text-3xl font-black text-green-700 mb-1">${{ currentKeywordData.cpc }}</div>
                            <div class="text-sm font-semibold text-green-600">Cost Per Click</div>
                        </div>

                        <div class="bg-gradient-to-br from-red-50 to-rose-100 rounded-2xl p-6 border-2 border-red-200">
                            <div class="flex items-center justify-between mb-3">
                                <div class="p-3 bg-red-500 rounded-xl">
                                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                                    </svg>
                                </div>
                                <span class="text-xs font-bold px-2 py-1 rounded-full"
                                    :class="getDifficultyBadgeClass(currentKeywordData.difficulty)">
                                    {{ getDifficultyText(currentKeywordData.difficulty) }}
                                </span>
                            </div>
                            <div class="text-3xl font-black text-red-700 mb-1">{{ currentKeywordData.difficulty }}/100</div>
                            <div class="text-sm font-semibold text-red-600">SEO Difficulty</div>
                        </div>

                        <div class="bg-gradient-to-br from-purple-50 to-purple-100 rounded-2xl p-6 border-2 border-purple-200">
                            <div class="flex items-center justify-between mb-3">
                                <div class="p-3 bg-purple-500 rounded-xl">
                                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z" />
                                    </svg>
                                </div>
                                <span class="text-xs font-bold px-2 py-1 rounded-full"
                                    :class="getIntentBadgeClass(currentKeywordData.intent)">
                                    {{ currentKeywordData.intent }}
                                </span>
                            </div>
                            <div class="text-2xl font-black text-purple-700 mb-1">{{ currentKeywordData.intent }}</div>
                            <div class="text-sm font-semibold text-purple-600">Search Intent</div>
                        </div>

                        <div class="bg-gradient-to-br from-orange-50 to-orange-100 rounded-2xl p-6 border-2 border-orange-200">
                            <div class="flex items-center justify-between mb-3">
                                <div class="p-3 bg-orange-500 rounded-xl">
                                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
                                    </svg>
                                </div>
                                <span class="text-xs font-bold bg-orange-200 text-orange-800 px-2 py-1 rounded-full">
                                    {{ currentKeywordData.trend > 0 ? '↗️' : currentKeywordData.trend < 0 ? '↘️' : '➡️' }}
                                </span>
                            </div>
                            <div class="text-3xl font-black text-orange-700 mb-1">{{ currentKeywordData.trend > 0 ? '+' : '' }}{{ currentKeywordData.trend }}%</div>
                            <div class="text-sm font-semibold text-orange-600">12M Trend</div>
                        </div>
                    </div>

                    <!-- Traffic Potential -->
                    <div class="bg-gradient-to-r from-emerald-50 to-green-50 rounded-xl p-6 border border-emerald-200 mb-8">
                        <h4 class="text-lg font-bold text-gray-900 mb-4 flex items-center">
                            <svg class="w-5 h-5 mr-2 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
                            </svg>
                            Traffic Potential Analysis
                        </h4>
                        <div class="grid grid-cols-3 gap-6">
                            <div class="text-center">
                                <div class="text-2xl font-bold text-emerald-600">{{ Math.round(currentKeywordData.searchVolume * 0.32) }}</div>
                                <div class="text-sm text-gray-600">Position #1 Traffic</div>
                                <div class="text-xs text-emerald-600">~32% CTR</div>
                            </div>
                            <div class="text-center">
                                <div class="text-2xl font-bold text-blue-600">{{ Math.round(currentKeywordData.searchVolume * 0.24) }}</div>
                                <div class="text-sm text-gray-600">Position #2 Traffic</div>
                                <div class="text-xs text-blue-600">~24% CTR</div>
                            </div>
                            <div class="text-center">
                                <div class="text-2xl font-bold text-orange-600">{{ Math.round(currentKeywordData.searchVolume * 0.18) }}</div>
                                <div class="text-sm text-gray-600">Position #3 Traffic</div>
                                <div class="text-xs text-orange-600">~18% CTR</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- SERP Overview -->
            <div v-if="currentKeywordData" class="bg-white rounded-2xl border-2 border-gray-200 overflow-hidden shadow-sm">
                <div class="bg-gradient-to-r from-gray-50 to-purple-50 px-8 py-6 border-b border-gray-200">
                    <h3 class="text-2xl font-bold text-gray-900 flex items-center">
                        <svg class="w-6 h-6 mr-3 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z" />
                        </svg>
                        SERP Analysis - Top 10 Results
                    </h3>
                    <p class="text-gray-600 mt-2">Competitive landscape and ranking opportunities</p>
                </div>

                <div class="p-8">
                    <div class="space-y-4">
                        <div v-for="(result, index) in serpResults" :key="index"
                            class="border-2 border-gray-100 rounded-xl p-6 hover:border-blue-200 transition-colors">
                            <div class="flex items-start justify-between">
                                <div class="flex items-start flex-1">
                                    <div class="w-8 h-8 rounded-full flex items-center justify-center text-sm font-bold mr-4"
                                        :class="index < 3 ? 'bg-yellow-100 text-yellow-800' : 'bg-gray-100 text-gray-600'">
                                        {{ index + 1 }}
                                    </div>
                                    <div class="flex-1">
                                        <h4 class="text-lg font-bold text-blue-600 hover:text-blue-800 cursor-pointer mb-2">
                                            {{ result.title }}
                                        </h4>
                                        <p class="text-sm text-gray-600 mb-3 break-all">{{ result.url }}</p>
                                        <p class="text-gray-700 mb-4 leading-relaxed">{{ result.description }}</p>

                                        <div class="flex flex-wrap gap-2 mb-4">
                                            <span v-if="result.hasSchema" class="px-3 py-1 bg-green-100 text-green-800 text-xs font-medium rounded-full flex items-center">
                                                ✅ Schema
                                            </span>
                                            <span v-if="result.hasImages" class="px-3 py-1 bg-blue-100 text-blue-800 text-xs font-medium rounded-full flex items-center">
                                                📷 Images
                                            </span>
                                            <span v-if="result.hasVideo" class="px-3 py-1 bg-purple-100 text-purple-800 text-xs font-medium rounded-full flex items-center">
                                                🎥 Video
                                            </span>
                                            <span v-if="result.hasFAQ" class="px-3 py-1 bg-orange-100 text-orange-800 text-xs font-medium rounded-full flex items-center">
                                                ❓ FAQ
                                            </span>
                                        </div>

                                        <div class="grid grid-cols-4 gap-4 text-sm">
                                            <div>
                                                <span class="text-gray-500">Domain Authority:</span>
                                                <span class="font-bold ml-1" :class="getDaColor(result.domainAuthority)">{{ result.domainAuthority }}</span>
                                            </div>
                                            <div>
                                                <span class="text-gray-500">Word Count:</span>
                                                <span class="font-bold ml-1 text-gray-900">{{ result.wordCount.toLocaleString() }}</span>
                                            </div>
                                            <div>
                                                <span class="text-gray-500">Backlinks:</span>
                                                <span class="font-bold ml-1 text-gray-900">{{ result.backlinks.toLocaleString() }}</span>
                                            </div>
                                            <div>
                                                <span class="text-gray-500">Est. Traffic:</span>
                                                <span class="font-bold ml-1 text-green-600">{{ result.estimatedTraffic.toLocaleString() }}</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="ml-6">
                                    <button class="px-4 py-2 bg-blue-100 text-blue-700 rounded-lg hover:bg-blue-200 transition-colors text-sm font-medium">
                                        Analyze
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Related Keywords -->
            <div v-if="relatedKeywords.length" class="bg-white rounded-2xl border-2 border-gray-200 overflow-hidden shadow-sm">
                <div class="bg-gradient-to-r from-gray-50 to-green-50 px-8 py-6 border-b border-gray-200">
                    <h3 class="text-2xl font-bold text-gray-900 flex items-center">
                        <svg class="w-6 h-6 mr-3 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1" />
                        </svg>
                        Related Keywords & Opportunities
                    </h3>
                    <p class="text-gray-600 mt-2">Expand your keyword strategy with these related terms</p>
                </div>

                <div class="p-8">
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        <div v-for="keyword in relatedKeywords" :key="keyword.keyword"
                            class="border border-gray-200 rounded-xl p-6 hover:border-blue-300 hover:shadow-md transition-all cursor-pointer"
                            @click="analyzeRelatedKeyword(keyword.keyword)">
                                                            <div class="flex items-center justify-between mb-3">
                                <h4 class="font-bold text-gray-900 text-lg">{{ keyword.keyword }}</h4>
                                <div class="flex items-center gap-2">
                                    <span class="text-xs font-bold px-2 py-1 rounded-full"
                                        :class="getDifficultyBadgeClass(keyword.difficulty)">
                                        {{ getDifficultyText(keyword.difficulty) }}
                                    </span>
                                    <button @click.stop="addToTracking(keyword)" class="p-1 text-gray-400 hover:text-blue-600">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                                        </svg>
                                    </button>
                                </div>
                            </div>

                            <div class="grid grid-cols-3 gap-4 text-sm mb-4">
                                <div class="text-center">
                                    <div class="font-bold text-blue-600">{{ keyword.volume.toLocaleString() }}</div>
                                    <div class="text-xs text-gray-500">Volume</div>
                                </div>
                                <div class="text-center">
                                    <div class="font-bold text-green-600">${{ keyword.cpc }}</div>
                                    <div class="text-xs text-gray-500">CPC</div>
                                </div>
                                <div class="text-center">
                                    <div class="font-bold text-purple-600">{{ keyword.difficulty }}/100</div>
                                    <div class="text-xs text-gray-500">Difficulty</div>
                                </div>
                            </div>

                            <div class="flex items-center justify-between">
                                <span class="text-xs px-2 py-1 rounded-full"
                                    :class="getIntentBadgeClass(keyword.intent)">
                                    {{ keyword.intent }}
                                </span>
                                <div class="text-xs text-gray-500">
                                    {{ keyword.trend > 0 ? '↗️' : keyword.trend < 0 ? '↘️' : '➡️' }} {{ keyword.trend }}%
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Content Gap Analysis -->
            <div v-if="currentKeywordData" class="bg-gradient-to-br from-yellow-50 to-orange-50 rounded-2xl border-2 border-yellow-200 p-8">
                <h3 class="text-2xl font-bold text-gray-900 mb-6 flex items-center">
                    <span class="text-3xl mr-3">💡</span>
                    Content Gap & Opportunity Analysis
                </h3>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    <div>
                        <h4 class="text-lg font-bold text-gray-800 mb-4">🎯 Opportunities Found</h4>
                        <div class="space-y-3">
                            <div class="p-4 bg-white rounded-xl border border-green-200">
                                <div class="flex items-start">
                                    <div class="p-2 bg-green-100 rounded-lg mr-3">
                                        <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                    </div>
                                    <div>
                                        <h5 class="font-bold text-gray-900 mb-1">Low Competition Window</h5>
                                        <p class="text-sm text-gray-600 mb-2">60% of top 10 results have weak domain authority (&lt;50)</p>
                                        <span class="text-xs bg-green-100 text-green-800 px-2 py-1 rounded-full font-medium">High Opportunity</span>
                                    </div>
                                </div>
                            </div>

                            <div class="p-4 bg-white rounded-xl border border-blue-200">
                                <div class="flex items-start">
                                    <div class="p-2 bg-blue-100 rounded-lg mr-3">
                                        <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                        </svg>
                                    </div>
                                    <div>
                                        <h5 class="font-bold text-gray-900 mb-1">Schema Gap</h5>
                                        <p class="text-sm text-gray-600 mb-2">Only 4/10 competitors use proper structured data</p>
                                        <span class="text-xs bg-blue-100 text-blue-800 px-2 py-1 rounded-full font-medium">Technical Edge</span>
                                    </div>
                                </div>
                            </div>

                            <div class="p-4 bg-white rounded-xl border border-purple-200">
                                <div class="flex items-start">
                                    <div class="p-2 bg-purple-100 rounded-lg mr-3">
                                        <svg class="w-4 h-4 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                                        </svg>
                                    </div>
                                    <div>
                                        <h5 class="font-bold text-gray-900 mb-1">Content Depth Gap</h5>
                                        <p class="text-sm text-gray-600 mb-2">Average content length is 1,200 words - opportunity for comprehensive guides</p>
                                        <span class="text-xs bg-purple-100 text-purple-800 px-2 py-1 rounded-full font-medium">Content Strategy</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div>
                        <h4 class="text-lg font-bold text-gray-800 mb-4">⚠️ Challenges to Consider</h4>
                        <div class="space-y-3">
                            <div class="p-4 bg-white rounded-xl border border-red-200">
                                <div class="flex items-start">
                                    <div class="p-2 bg-red-100 rounded-lg mr-3">
                                        <svg class="w-4 h-4 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.081 16.5c-.77.833.192 2.5 1.732 2.5z" />
                                        </svg>
                                    </div>
                                    <div>
                                        <h5 class="font-bold text-gray-900 mb-1">High Authority Competitors</h5>
                                        <p class="text-sm text-gray-600 mb-2">Top 3 positions dominated by DA 80+ domains</p>
                                        <span class="text-xs bg-red-100 text-red-800 px-2 py-1 rounded-full font-medium">High Challenge</span>
                                    </div>
                                </div>
                            </div>

                            <div class="p-4 bg-white rounded-xl border border-yellow-200">
                                <div class="flex items-start">
                                    <div class="p-2 bg-yellow-100 rounded-lg mr-3">
                                        <svg class="w-4 h-4 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                    </div>
                                    <div>
                                        <h5 class="font-bold text-gray-900 mb-1">Content Saturation</h5>
                                        <p class="text-sm text-gray-600 mb-2">Many similar topics already covered - need unique angle</p>
                                        <span class="text-xs bg-yellow-100 text-yellow-800 px-2 py-1 rounded-full font-medium">Medium Challenge</span>
                                    </div>
                                </div>
                            </div>

                            <div class="p-4 bg-white rounded-xl border border-orange-200">
                                <div class="flex items-start">
                                    <div class="p-2 bg-orange-100 rounded-lg mr-3">
                                        <svg class="w-4 h-4 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1" />
                                        </svg>
                                    </div>
                                    <div>
                                        <h5 class="font-bold text-gray-900 mb-1">Paid Competition</h5>
                                        <p class="text-sm text-gray-600 mb-2">High CPC indicates strong commercial intent and ad competition</p>
                                        <span class="text-xs bg-orange-100 text-orange-800 px-2 py-1 rounded-full font-medium">Commercial Intent</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Action Recommendations -->
                <div class="mt-8 p-6 bg-white rounded-xl border-2 border-blue-200">
                    <h4 class="text-lg font-bold text-gray-900 mb-4 flex items-center">
                        <svg class="w-5 h-5 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z" />
                        </svg>
                        Recommended Action Plan
                    </h4>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div class="p-4 bg-blue-50 rounded-lg">
                            <h5 class="font-bold text-blue-900 mb-2">1. Content Strategy</h5>
                            <p class="text-sm text-blue-700">Create 2,000+ word comprehensive guide with unique insights and better user experience</p>
                        </div>
                        <div class="p-4 bg-green-50 rounded-lg">
                            <h5 class="font-bold text-green-900 mb-2">2. Technical SEO</h5>
                            <p class="text-sm text-green-700">Implement proper schema markup and optimize for featured snippets to gain edge</p>
                        </div>
                        <div class="p-4 bg-purple-50 rounded-lg">
                            <h5 class="font-bold text-purple-900 mb-2">3. Link Building</h5>
                            <p class="text-sm text-purple-700">Target 15-20 high-quality backlinks from relevant food/recipe websites</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Trending Keywords Section -->
            <div class="bg-white rounded-2xl border-2 border-gray-200 overflow-hidden shadow-sm">
                <div class="bg-gradient-to-r from-gray-50 to-pink-50 px-8 py-6 border-b border-gray-200">
                    <h3 class="text-2xl font-bold text-gray-900 flex items-center">
                        <svg class="w-6 h-6 mr-3 text-pink-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
                        </svg>
                        🔥 Trending Keywords in Your Niche
                    </h3>
                    <p class="text-gray-600 mt-2">Discover emerging opportunities before your competitors</p>
                </div>

                <div class="p-8">
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                        <div v-for="trending in trendingKeywords" :key="trending.keyword"
                            class="bg-gradient-to-br from-pink-50 to-rose-50 border-2 border-pink-200 rounded-xl p-6 hover:border-pink-300 hover:shadow-lg transition-all cursor-pointer"
                            @click="analyzeRelatedKeyword(trending.keyword)">
                            <div class="flex items-center justify-between mb-3">
                                <span class="text-2xl">🚀</span>
                                <span class="text-xs font-bold bg-pink-200 text-pink-800 px-2 py-1 rounded-full">
                                    +{{ trending.growth }}%
                                </span>
                            </div>
                            <h4 class="font-bold text-gray-900 mb-2">{{ trending.keyword }}</h4>
                            <div class="text-sm space-y-1">
                                <div class="flex justify-between">
                                    <span class="text-gray-500">Volume:</span>
                                    <span class="font-medium">{{ trending.volume.toLocaleString() }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-500">Difficulty:</span>
                                    <span class="font-medium">{{ trending.difficulty }}/100</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-500">Growth:</span>
                                    <span class="font-medium text-green-600">+{{ trending.growth }}%</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';

// Reactive data
const searchKeyword = ref('');
const selectedLocation = ref('US');
const selectedLanguage = ref('en');
const isSearching = ref(false);
const showSuggestionsDropdown = ref(false);
const currentKeywordData = ref(null);

// Sample data
const quickKeywords = ref([
    'pork chop recipes', 'digital marketing', 'weight loss tips', 'home decor ideas', 'cooking techniques'
]);

const keywordSuggestions = ref([
    'pork chop recipes easy', 'pork chop recipes grilled', 'pork chop recipes baked', 'pork chop recipes air fryer'
]);

const serpResults = ref([]);
const relatedKeywords = ref([]);
const trendingKeywords = ref([
    { keyword: 'air fryer pork chops', volume: 8100, difficulty: 42, growth: 125 },
    { keyword: 'keto pork recipes', volume: 5400, difficulty: 38, growth: 89 },
    { keyword: 'instant pot pork chops', volume: 12000, difficulty: 45, growth: 67 },
    { keyword: 'healthy pork meals', volume: 3200, difficulty: 35, growth: 156 }
]);

// Methods
const searchKeywords = async () => {
    if (!searchKeyword.value.trim()) return;

    isSearching.value = true;
    showSuggestionsDropdown.value = false;

    try {
        // Simulate API call
        await new Promise(resolve => setTimeout(resolve, 2000));

        // Generate sample data
        currentKeywordData.value = {
            keyword: searchKeyword.value,
            searchVolume: Math.floor(Math.random() * 50000) + 1000,
            cpc: (Math.random() * 5 + 0.5).toFixed(2),
            difficulty: Math.floor(Math.random() * 60) + 20,
            intent: ['Informational', 'Commercial', 'Transactional', 'Navigational'][Math.floor(Math.random() * 4)],
            trend: Math.floor(Math.random() * 40) - 20
        };

        // Generate SERP results
        generateSerpResults();
        generateRelatedKeywords();

    } catch (error) {
        console.error('Error searching keywords:', error);
    } finally {
        isSearching.value = false;
    }
};

const generateSerpResults = () => {
    const domains = ['foodnetwork.com', 'allrecipes.com', 'delish.com', 'tasteofhome.com', 'epicurious.com', 'bonappetit.com', 'foodandwine.com', 'seriouseats.com', 'kitchn.com', 'recipetineats.com'];

    serpResults.value = Array.from({ length: 10 }, (_, index) => ({
        title: `${searchKeyword.value.split(' ').map(w => w.charAt(0).toUpperCase() + w.slice(1)).join(' ')} Recipe ${index + 1}`,
        url: `https://${domains[index]}/${searchKeyword.value.replace(/ /g, '-')}-recipe-${index + 1}`,
        description: `Learn how to make the perfect ${searchKeyword.value}. This easy recipe includes step-by-step instructions and helpful tips for the best results.`,
        domainAuthority: Math.floor(Math.random() * 40) + 50,
        wordCount: Math.floor(Math.random() * 2000) + 800,
        backlinks: Math.floor(Math.random() * 5000) + 100,
        estimatedTraffic: Math.floor(Math.random() * 10000) + 500,
        hasSchema: Math.random() > 0.4,
        hasImages: Math.random() > 0.2,
        hasVideo: Math.random() > 0.7,
        hasFAQ: Math.random() > 0.6
    }));
};

const generateRelatedKeywords = () => {
    const variations = [
        'easy', 'best', 'quick', 'healthy', 'homemade', 'simple', 'delicious', 'perfect', 'crispy', 'tender'
    ];

    relatedKeywords.value = variations.map(variation => ({
        keyword: `${variation} ${searchKeyword.value}`,
        volume: Math.floor(Math.random() * 20000) + 500,
        cpc: (Math.random() * 3 + 0.3).toFixed(2),
        difficulty: Math.floor(Math.random() * 50) + 15,
        intent: ['Informational', 'Commercial', 'Transactional'][Math.floor(Math.random() * 3)],
        trend: Math.floor(Math.random() * 30) - 15
    }));
};

const showSuggestions = () => {
    if (searchKeyword.value.length > 2) {
        showSuggestionsDropdown.value = true;
    } else {
        showSuggestionsDropdown.value = false;
    }
};

const selectSuggestion = (suggestion) => {
    searchKeyword.value = suggestion;
    showSuggestionsDropdown.value = false;
    searchKeywords();
};

const quickSearch = (keyword) => {
    searchKeyword.value = keyword;
    searchKeywords();
};

const analyzeRelatedKeyword = (keyword) => {
    searchKeyword.value = keyword;
    searchKeywords();
};

const addToTracking = (keyword) => {
    alert(`"${keyword.keyword || keyword}" added to tracking list!`);
};

const generateContentBrief = () => {
    if (!currentKeywordData.value) return;
    alert(`AI Content Brief for "${currentKeywordData.value.keyword}" will be generated. This feature includes optimized H1/H2 structure, meta tags, and content outline.`);
};

const exportKeywords = () => {
    alert('Keywords exported to CSV successfully!');
};

const showKeywordHistory = () => {
    alert('Keyword search history will be displayed here.');
};

// Helper functions
const getDifficultyBadgeClass = (difficulty) => {
    if (difficulty < 30) return 'bg-green-100 text-green-800';
    if (difficulty < 60) return 'bg-yellow-100 text-yellow-800';
    return 'bg-red-100 text-red-800';
};

const getDifficultyText = (difficulty) => {
    if (difficulty < 30) return 'Easy';
    if (difficulty < 60) return 'Medium';
    return 'Hard';
};

const getIntentBadgeClass = (intent) => {
    const classes = {
        'Informational': 'bg-blue-100 text-blue-800',
        'Commercial': 'bg-green-100 text-green-800',
        'Transactional': 'bg-purple-100 text-purple-800',
        'Navigational': 'bg-orange-100 text-orange-800'
    };
    return classes[intent] || 'bg-gray-100 text-gray-800';
};

const getDaColor = (da) => {
    if (da >= 70) return 'text-green-600';
    if (da >= 50) return 'text-yellow-600';
    return 'text-red-600';
};

// Close suggestions when clicking outside
onMounted(() => {
    document.addEventListener('click', (e) => {
        if (!e.target.closest('.relative')) {
            showSuggestionsDropdown.value = false;
        }
    });
});
</script>

<style scoped>
/* Custom animations */
@keyframes fade-in {
    from { opacity: 0; transform: translateY(10px); }
    to { opacity: 1; transform: translateY(0); }
}

.animate-fade-in {
    animation: fade-in 0.3s ease-out;
}

/* Enhanced hover effects */
.hover\:-translate-y-1:hover {
    transform: translateY(-0.25rem);
}

/* Custom scrollbar */
.overflow-y-auto::-webkit-scrollbar {
    width: 6px;
}

.overflow-y-auto::-webkit-scrollbar-track {
    background: #f1f5f9;
}

.overflow-y-auto::-webkit-scrollbar-thumb {
    background: #cbd5e1;
    border-radius: 3px;
}

/* Loading spinner */
@keyframes spin {
    from { transform: rotate(0deg); }
    to { transform: rotate(360deg); }
}

.animate-spin {
    animation: spin 1s linear infinite;
}

/* Button hover effects */
button:hover {
    transform: translateY(-1px);
}

button:active {
    transform: translateY(0);
}

/* Card hover effects */
.hover\:shadow-lg:hover {
    box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
}

/* Input focus effects */
input:focus {
    box-shadow: 0 0 0 4px rgba(59, 130, 246, 0.1);
}

/* Gradient text effects */
.bg-gradient-to-r {
    background-image: linear-gradient(to right, var(--tw-gradient-stops));
}

/* Mobile responsiveness */
@media (max-width: 768px) {
    .grid-cols-5 {
        grid-template-columns: repeat(2, minmax(0, 1fr));
    }

    .grid-cols-4 {
        grid-template-columns: repeat(2, minmax(0, 1fr));
    }

    .grid-cols-3 {
        grid-template-columns: repeat(1, minmax(0, 1fr));
    }

    .text-3xl {
        font-size: 1.875rem;
    }

    .p-8 {
        padding: 1.5rem;
    }
}

/* High contrast mode */
@media (prefers-contrast: high) {
    .border-gray-200 {
        border-color: rgb(0 0 0);
        border-width: 2px;
    }
}

/* Reduced motion */
@media (prefers-reduced-motion: reduce) {
    .animate-fade-in,
    .animate-spin,
    .transition-all,
    button:hover {
        animation: none;
        transform: none;
        transition: none;
    }
}
</style>
