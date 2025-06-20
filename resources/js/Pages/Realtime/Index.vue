<template>
    <AuthenticatedLayout>
        <div class="min-h-screen bg-gradient-to-br from-slate-50 to-blue-50">
            <!-- Header -->
            <div class="bg-white shadow-sm border-b border-gray-200 px-6 py-4">
                <div class="flex items-center justify-between">
                    <div>
                        <h1 class="text-2xl font-bold text-gray-900 flex items-center">
                            <div class="w-3 h-3 bg-green-500 rounded-full mr-3 animate-pulse"></div>
                            Real-Time Analytics
                        </h1>
                        <p class="text-gray-600 mt-1">Live website activity and visitor insights</p>
                    </div>
                    <div class="flex items-center space-x-4">
                        <!-- Add Website Button -->
                        <button @click="showAddWebsiteModal = true"
                            class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm font-medium flex items-center transition-colors">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                            </svg>
                            Add Website
                        </button>

                        <!-- Website Selector -->
                        <div class="flex items-center space-x-2">
                            <label class="text-sm font-medium text-gray-700">Website:</label>
                            <select v-model="selectedWebsite" @change="switchWebsite"
                                class="bg-white border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                <option value="all">All Websites</option>
                                <option v-for="site in websites" :key="site.id" :value="site.id">
                                    {{ site.name }}
                                </option>
                            </select>
                        </div>
                        <div class="flex items-center space-x-2 text-sm text-gray-600">
                            <div class="w-2 h-2 bg-green-500 rounded-full animate-pulse"></div>
                            <span>Last updated: {{ lastUpdated }}</span>
                        </div>
                        <button @click="toggleAutoRefresh"
                            :class="autoRefresh ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-600'"
                            class="px-4 py-2 rounded-lg text-sm font-medium transition-colors">
                            <svg class="w-4 h-4 mr-2 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                            </svg>
                            {{ autoRefresh ? 'Auto-refresh ON' : 'Auto-refresh OFF' }}
                        </button>
                    </div>
                </div>
            </div>

            <div class="p-6 space-y-6">
                <!-- Website Overview Cards -->
                <div v-if="selectedWebsite === 'all'" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
                    <div v-for="site in websites" :key="site.id"
                        class="bg-white rounded-xl p-6 shadow-sm border border-gray-200 hover:shadow-md transition-all cursor-pointer"
                        @click="selectedWebsite = site.id">
                        <div class="flex items-center justify-between mb-4">
                            <div class="flex items-center">
                                <div class="w-10 h-10 bg-gradient-to-br from-blue-500 to-purple-600 rounded-lg flex items-center justify-center text-white font-bold text-sm mr-3">
                                    {{ site.name.charAt(0).toUpperCase() }}
                                </div>
                                <div>
                                    <h3 class="font-semibold text-gray-900">{{ site.name }}</h3>
                                    <p class="text-xs text-gray-500">{{ site.domain }}</p>
                                </div>
                            </div>
                            <div class="flex items-center space-x-2">
                                <div :class="getStatusClass(site.status)"
                                    class="px-2 py-1 rounded-full text-xs font-medium">
                                    {{ site.status }}
                                </div>
                                <button @click.stop="showTrackingCode(site)"
                                    class="p-1 text-gray-400 hover:text-gray-600"
                                    title="View tracking code">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4" />
                                    </svg>
                                </button>
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div class="text-center">
                                <p class="text-2xl font-bold text-gray-900">{{ site.stats.usersOnline }}</p>
                                <p class="text-xs text-gray-500">Users Online</p>
                            </div>
                            <div class="text-center">
                                <p class="text-2xl font-bold text-gray-900">{{ site.stats.pageViews }}</p>
                                <p class="text-xs text-gray-500">Page Views</p>
                            </div>
                        </div>

                        <div class="mt-4 flex items-center justify-between text-sm">
                            <span class="text-gray-600">Today's traffic</span>
                            <div class="flex items-center">
                                <svg :class="site.stats.trend > 0 ? 'text-green-500' : 'text-red-500'"
                                    class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                    <path v-if="site.stats.trend > 0" fill-rule="evenodd" d="M3.293 9.707a1 1 0 010-1.414l6-6a1 1 0 011.414 0l6 6a1 1 0 01-1.414 1.414L11 5.414V17a1 1 0 11-2 0V5.414L5.707 9.707a1 1 0 01-1.414-1.414z" clip-rule="evenodd" />
                                    <path v-else fill-rule="evenodd" d="M16.707 10.293a1 1 0 010 1.414l-6 6a1 1 0 01-1.414 0l-6-6a1 1 0 111.414-1.414L9 14.586V3a1 1 0 012 0v11.586l4.293-4.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                                </svg>
                                <span :class="site.stats.trend > 0 ? 'text-green-600' : 'text-red-600'" class="font-medium">
                                    {{ Math.abs(site.stats.trend) }}%
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Single Website Detailed View -->
                <div v-if="selectedWebsite !== 'all'">
                    <!-- Current Website Info Bar -->
                    <div class="bg-white rounded-xl p-4 shadow-sm border border-gray-200 mb-6">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center">
                                <div class="w-12 h-12 bg-gradient-to-br from-blue-500 to-purple-600 rounded-lg flex items-center justify-center text-white font-bold mr-4">
                                    {{ currentWebsite?.name.charAt(0).toUpperCase() }}
                                </div>
                                <div>
                                    <h2 class="text-lg font-semibold text-gray-900">{{ currentWebsite?.name }}</h2>
                                    <p class="text-sm text-gray-500">{{ currentWebsite?.domain }}</p>
                                </div>
                            </div>
                            <div class="flex items-center space-x-4">
                                <div :class="getStatusClass(currentWebsite?.status)"
                                    class="px-3 py-1 rounded-full text-sm font-medium">
                                    {{ currentWebsite?.status }}
                                </div>
                                <button @click="showTrackingCode(currentWebsite)"
                                    class="text-sm text-blue-600 hover:text-blue-700 flex items-center">
                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4" />
                                    </svg>
                                    View Tracking Code
                                </button>
                                <button @click="selectedWebsite = 'all'"
                                    class="text-sm text-gray-500 hover:text-gray-700 flex items-center">
                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                                    </svg>
                                    Back to All Sites
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Real-time Stats Cards -->
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                        <div class="bg-white rounded-xl p-6 shadow-sm border border-gray-200 hover:shadow-md transition-shadow">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-sm font-medium text-gray-600">Users Online</p>
                                    <p class="text-3xl font-bold text-gray-900 mt-2">{{ currentSiteData.usersOnline || 0 }}</p>
                                </div>
                                <div class="p-3 bg-blue-100 rounded-xl">
                                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z" />
                                    </svg>
                                </div>
                            </div>
                        </div>

                        <div class="bg-white rounded-xl p-6 shadow-sm border border-gray-200 hover:shadow-md transition-shadow">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-sm font-medium text-gray-600">Active Pages</p>
                                    <p class="text-3xl font-bold text-gray-900 mt-2">{{ currentSiteData.activePages || 0 }}</p>
                                </div>
                                <div class="p-3 bg-purple-100 rounded-xl">
                                    <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                    </svg>
                                </div>
                            </div>
                        </div>

                        <div class="bg-white rounded-xl p-6 shadow-sm border border-gray-200 hover:shadow-md transition-shadow">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-sm font-medium text-gray-600">Avg Session Duration</p>
                                    <p class="text-3xl font-bold text-gray-900 mt-2">{{ currentSiteData.avgSessionDuration || '0:00' }}</p>
                                </div>
                                <div class="p-3 bg-green-100 rounded-xl">
                                    <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                </div>
                            </div>
                        </div>

                        <div class="bg-white rounded-xl p-6 shadow-sm border border-gray-200 hover:shadow-md transition-shadow">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-sm font-medium text-gray-600">Top Traffic Source</p>
                                    <p class="text-2xl font-bold text-gray-900 mt-2">{{ currentSiteData.topSource?.name || 'N/A' }}</p>
                                </div>
                                <div class="p-3 bg-orange-100 rounded-xl">
                                    <svg class="w-6 h-6 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
                                    </svg>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Combined Stats for All Websites -->
                <div v-if="selectedWebsite === 'all'" class="bg-white rounded-xl shadow-sm border border-gray-200">
                    <div class="p-6 border-b border-gray-200">
                        <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                            <svg class="w-5 h-5 mr-2 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                            </svg>
                            Combined Analytics Overview
                        </h3>
                    </div>
                    <div class="p-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                            <div class="text-center">
                                <p class="text-3xl font-bold text-gray-900">{{ totalStats.usersOnline }}</p>
                                <p class="text-sm text-gray-600 mt-1">Total Users Online</p>
                                <p class="text-xs text-gray-500 mt-1">Across all sites</p>
                            </div>
                            <div class="text-center">
                                <p class="text-3xl font-bold text-gray-900">{{ totalStats.pageViews }}</p>
                                <p class="text-sm text-gray-600 mt-1">Total Page Views</p>
                                <p class="text-xs text-gray-500 mt-1">Today</p>
                            </div>
                            <div class="text-center">
                                <p class="text-3xl font-bold text-gray-900">{{ totalStats.activeSites }}</p>
                                <p class="text-sm text-gray-600 mt-1">Active Websites</p>
                                <p class="text-xs text-gray-500 mt-1">Currently monitored</p>
                            </div>
                            <div class="text-center">
                                <p class="text-3xl font-bold text-gray-900">{{ totalStats.avgSessionDuration }}</p>
                                <p class="text-sm text-gray-600 mt-1">Avg Session Duration</p>
                                <p class="text-xs text-gray-500 mt-1">All sites combined</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Add Website Modal -->
            <div v-if="showAddWebsiteModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
                <div class="bg-white rounded-xl p-6 max-w-md w-full mx-4">
                    <div class="flex items-center justify-between mb-6">
                        <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                            <svg class="w-5 h-5 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                            </svg>
                            Add New Website
                        </h3>
                        <button @click="closeAddWebsiteModal" class="text-gray-400 hover:text-gray-600">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>

                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Website Name *</label>
                            <input type="text" v-model="newWebsite.name" placeholder="My Awesome Website"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Domain *</label>
                            <input type="text" v-model="newWebsite.domain" placeholder="example.com"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Description</label>
                            <textarea v-model="newWebsite.description" placeholder="Brief description of your website" rows="3"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"></textarea>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Category</label>
                            <select v-model="newWebsite.category"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                <option value="website">Website</option>
                                <option value="ecommerce">E-commerce</option>
                                <option value="blog">Blog</option>
                                <option value="portfolio">Portfolio</option>
                                <option value="saas">SaaS</option>
                                <option value="landing">Landing Page</option>
                            </select>
                        </div>
                    </div>

                    <div class="flex space-x-3 mt-6">
                        <button @click="closeAddWebsiteModal"
                            class="flex-1 px-4 py-2 text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-lg font-medium transition-colors">
                            Cancel
                        </button>
                        <button @click="addWebsite"
                            class="flex-1 px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-medium transition-colors">
                            Add Website
                        </button>
                    </div>
                </div>
            </div>

            <!-- Tracking Code Modal -->
            <div v-if="showTrackingModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
                <div class="bg-white rounded-xl p-6 max-w-4xl w-full mx-4 max-h-[90vh] overflow-y-auto">
                    <div class="flex items-center justify-between mb-6">
                        <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                            <svg class="w-5 h-5 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4" />
                            </svg>
                            Tracking Setup for {{ currentTrackingData.name }}
                        </h3>
                        <button @click="closeTrackingModal" class="text-gray-400 hover:text-gray-600">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>

                    <div class="space-y-6">
                        <!-- Tracking ID -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Tracking ID</label>
                            <div class="flex items-center space-x-2">
                                <code class="flex-1 px-3 py-2 bg-gray-100 rounded-lg text-sm font-mono">
                                    {{ currentTrackingData.trackingId }}
                                </code>
                                <button @click="copyToClipboard(currentTrackingData.trackingId, 'id')"
                                    class="px-3 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg text-sm font-medium flex items-center transition-colors">
                                    <svg v-if="copiedId" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                    </svg>
                                    <svg v-else class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z" />
                                    </svg>
                                </button>
                            </div>
                        </div>

                        <!-- Instructions -->
                        <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                            <h4 class="font-semibold text-blue-900 mb-2">Setup Instructions</h4>
                            <ol class="text-sm text-blue-800 space-y-2">
                                <li>1. Copy the tracking script below</li>
                                <li>2. Paste it in the &lt;head&gt; section of your website</li>
                                <li>3. The script will automatically start tracking page views and user interactions</li>
                                <li>4. Data will appear in your dashboard within 5-10 minutes</li>
                            </ol>
                        </div>

                        <!-- CDN Script -->
                        <div>
                            <div class="flex items-center justify-between mb-2">
                                <label class="block text-sm font-medium text-gray-700">Tracking Script (CDN)</label>
                                <button @click="copyToClipboard(currentTrackingData.script, 'script')"
                                    class="px-3 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg text-sm font-medium flex items-center transition-colors">
                                    <svg v-if="copiedScript" class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                    </svg>
                                    <svg v-else class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z" />
                                    </svg>
                                    {{ copiedScript ? 'Copied!' : 'Copy Script' }}
                                </button>
                            </div>
                            <div class="bg-gray-900 text-green-400 p-4 rounded-lg text-sm overflow-x-auto max-h-80">
                                <code>{{ `<script async defer src="https://cdn.your-analytics.com/track.js?id=${currentTrackingData.trackingId}"></script>` }}</code>
                            </div>
                        </div>

                        <!-- Features -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div class="bg-gray-50 rounded-lg p-4">
                                <h5 class="font-semibold text-gray-900 mb-2 flex items-center">
                                    <svg class="w-4 h-4 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                                    </svg>
                                    Automatic Tracking
                                </h5>
                                <ul class="text-sm text-gray-600 space-y-1">
                                    <li>• Page views and sessions</li>
                                    <li>• User interactions and clicks</li>
                                    <li>• Scroll depth and time on page</li>
                                    <li>• Referrer and traffic sources</li>
                                </ul>
                            </div>
                            <div class="bg-gray-50 rounded-lg p-4">
                                <h5 class="font-semibold text-gray-900 mb-2 flex items-center">
                                    <svg class="w-4 h-4 mr-2 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    Real-time Data
                                </h5>
                                <ul class="text-sm text-gray-600 space-y-1">
                                    <li>• Live visitor tracking</li>
                                    <li>• Geographic location data</li>
                                    <li>• Device and browser info</li>
                                    <li>• Current page activities</li>
                                </ul>
                            </div>
                        </div>

                        <!-- Verification -->
                        <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                            <h4 class="font-semibold text-yellow-900 mb-2">Verification</h4>
                            <p class="text-sm text-yellow-800">
                                After installing the script, visit your website and check back here in 5-10 minutes to see live data.
                                The website status will change from "pending" to "active" once we detect traffic.
                            </p>
                        </div>
                    </div>

                    <div class="flex space-x-3 mt-6">
                        <button @click="closeTrackingModal"
                            class="flex-1 px-4 py-2 text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-lg font-medium transition-colors">
                            Close
                        </button>
                        <button @click="copyAndVisitSite"
                            class="flex-1 px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-medium transition-colors flex items-center justify-center">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                            </svg>
                            Copy & Visit Site
                        </button>
                    </div>
                </div>
            </div>

            <!-- Traffic Spike Alert -->
            <div v-if="showTrafficAlert"
                class="fixed top-20 right-6 bg-orange-500 text-white px-6 py-4 rounded-lg shadow-xl z-50 animate-slide-in">
                <div class="flex items-center">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
                    </svg>
                    <div>
                        <div class="font-semibold">Traffic Spike Detected!</div>
                        <div class="text-sm opacity-90">{{ currentWebsite?.name || 'Multiple sites' }}: +45% increase in the last 10 minutes</div>
                    </div>
                    <button @click="showTrafficAlert = false" class="ml-4 text-white hover:text-gray-200">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted } from 'vue'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'

// Reactive data
const autoRefresh = ref(true)
const lastUpdated = ref(new Date().toLocaleTimeString())
const showTrafficAlert = ref(false)
const selectedWebsite = ref('all')
const showAddWebsiteModal = ref(false)
const showTrackingModal = ref(false)
const copiedScript = ref(false)
const copiedId = ref(false)

// New website form data
const newWebsite = ref({
    name: '',
    domain: '',
    description: '',
    category: 'website'
})

// Current tracking data for modal
const currentTrackingData = ref({
    trackingId: '',
    domain: '',
    name: '',
    script: ''
})

// Websites data with tracking IDs
const websites = ref([
    {
        id: 'site1',
        name: 'E-commerce Store',
        domain: 'mystore.com',
        status: 'active',
        trackingId: 'AT_e4f8a2b1c9d3e7f6',
        createdAt: '2024-01-15',
        stats: {
            usersOnline: 247,
            pageViews: 8450,
            trend: 12
        }
    },
    {
        id: 'site2',
        name: 'Blog Platform',
        domain: 'myblog.com',
        status: 'active',
        trackingId: 'AT_b7c2d5e8f1a4g9h3',
        createdAt: '2024-02-08',
        stats: {
            usersOnline: 156,
            pageViews: 3290,
            trend: 8
        }
    },
    {
        id: 'site3',
        name: 'Portfolio Site',
        domain: 'portfolio.dev',
        status: 'active',
        trackingId: 'AT_k5n8m2p9q7r3s6t1',
        createdAt: '2024-01-28',
        stats: {
            usersOnline: 89,
            pageViews: 1850,
            trend: -3
        }
    }
])

// Website-specific data
const websiteData = ref({
    site1: {
        usersOnline: 247,
        activePages: 15,
        totalPages: 84,
        avgSessionDuration: '4:32',
        topSource: { name: 'Google', percentage: 48 }
    },
    site2: {
        usersOnline: 156,
        activePages: 12,
        totalPages: 245,
        avgSessionDuration: '6:18',
        topSource: { name: 'Social Media', percentage: 52 }
    },
    site3: {
        usersOnline: 89,
        activePages: 8,
        totalPages: 15,
        avgSessionDuration: '3:45',
        topSource: { name: 'Direct', percentage: 65 }
    }
})

// Computed properties
const currentWebsite = computed(() => {
    return websites.value.find(site => site.id === selectedWebsite.value)
})

const currentSiteData = computed(() => {
    return websiteData.value[selectedWebsite.value] || {}
})

const totalStats = computed(() => {
    const active = websites.value.filter(site => site.status === 'active')
    return {
        usersOnline: active.reduce((sum, site) => sum + site.stats.usersOnline, 0),
        pageViews: active.reduce((sum, site) => sum + site.stats.pageViews, 0),
        activeSites: active.length,
        avgSessionDuration: '5:47'
    }
})

// Intervals
let refreshInterval = null

// Methods
const generateTrackingId = () => {
    const chars = 'abcdefghijklmnopqrstuvwxyz0123456789'
    let result = 'AT_'
    for (let i = 0; i < 16; i++) {
        result += chars.charAt(Math.floor(Math.random() * chars.length))
    }
    return result
}

const generateCDNScript = (trackingId, domain) => {
    return ``
}

const addWebsite = () => {
    if (!newWebsite.value.name || !newWebsite.value.domain) {
        alert('Please fill in website name and domain')
        return
    }

    const trackingId = generateTrackingId()
    const newSiteId = `site${Date.now()}`

    const websiteToAdd = {
        id: newSiteId,
        name: newWebsite.value.name,
        domain: newWebsite.value.domain,
        description: newWebsite.value.description,
        category: newWebsite.value.category,
        status: 'pending',
        trackingId: trackingId,
        createdAt: new Date().toISOString().split('T')[0],
        stats: {
            usersOnline: 0,
            pageViews: 0,
            trend: 0
        }
    }

    websites.value.push(websiteToAdd)

    // Initialize empty analytics data
    websiteData.value[newSiteId] = {
        usersOnline: 0,
        activePages: 0,
        totalPages: 0,
        avgSessionDuration: '0:00',
        topSource: { name: 'Direct', percentage: 100 }
    }

    // Show tracking setup modal
    currentTrackingData.value = {
        trackingId,
        domain: newWebsite.value.domain,
        name: newWebsite.value.name,
        script: generateCDNScript(trackingId, newWebsite.value.domain)
    }

    closeAddWebsiteModal()
    showTrackingModal.value = true
}

const showTrackingCode = (site) => {
    currentTrackingData.value = {
        trackingId: site.trackingId,
        domain: site.domain,
        name: site.name,
        script: generateCDNScript(site.trackingId, site.domain)
    }
    showTrackingModal.value = true
}

const closeAddWebsiteModal = () => {
    showAddWebsiteModal.value = false
    newWebsite.value = {
        name: '',
        domain: '',
        description: '',
        category: 'website'
    }
}

const closeTrackingModal = () => {
    showTrackingModal.value = false
    copiedScript.value = false
    copiedId.value = false
}

const copyToClipboard = async (text, type) => {
    try {
        await navigator.clipboard.writeText(text)
        if (type === 'script') {
            copiedScript.value = true
            setTimeout(() => copiedScript.value = false, 2000)
        } else if (type === 'id') {
            copiedId.value = true
            setTimeout(() => copiedId.value = false, 2000)
        }
    } catch (err) {
        console.error('Failed to copy: ', err)
    }
}

const copyAndVisitSite = () => {
    copyToClipboard(currentTrackingData.value.script, 'script')
    window.open(`https://${currentTrackingData.value.domain}`, '_blank')
}

const getStatusClass = (status) => {
    switch (status) {
        case 'active':
            return 'bg-green-100 text-green-800'
        case 'pending':
            return 'bg-yellow-100 text-yellow-800'
        case 'inactive':
            return 'bg-gray-100 text-gray-600'
        default:
            return 'bg-gray-100 text-gray-600'
    }
}

const switchWebsite = () => {
    if (selectedWebsite.value !== 'all') {
        updateStats()
    }
}

const toggleAutoRefresh = () => {
    autoRefresh.value = !autoRefresh.value
    if (autoRefresh.value) {
        startAutoRefresh()
    } else {
        stopAutoRefresh()
    }
}

const startAutoRefresh = () => {
    refreshInterval = setInterval(() => {
        updateStats()
        lastUpdated.value = new Date().toLocaleTimeString()
    }, 5000)
}

const stopAutoRefresh = () => {
    if (refreshInterval) {
        clearInterval(refreshInterval)
        refreshInterval = null
    }
}

const updateStats = () => {
    const variation = () => Math.floor(Math.random() * 21) - 10

    if (selectedWebsite.value === 'all') {
        websites.value.forEach(site => {
            if (site.status === 'active') {
                site.stats.usersOnline = Math.max(20, site.stats.usersOnline + variation())
                site.stats.pageViews += Math.max(0, Math.floor(Math.random() * 50))
            }
        })
    } else {
        const site = websites.value.find(s => s.id === selectedWebsite.value)
        if (site && site.status === 'active') {
            site.stats.usersOnline = Math.max(20, site.stats.usersOnline + variation())
            if (websiteData.value[selectedWebsite.value]) {
                websiteData.value[selectedWebsite.value].usersOnline = site.stats.usersOnline
            }
        }
    }

    // Randomly trigger traffic alert
    if (Math.random() < 0.1) {
        showTrafficAlert.value = true
        setTimeout(() => {
            showTrafficAlert.value = false
        }, 8000)
    }
}

// Lifecycle
onMounted(() => {
    if (autoRefresh.value) {
        startAutoRefresh()
    }

    setTimeout(() => {
        showTrafficAlert.value = true
        setTimeout(() => {
            showTrafficAlert.value = false
        }, 6000)
    }, 3000)
})

onUnmounted(() => {
    stopAutoRefresh()
})
</script>

<style scoped>
@keyframes fade-in {
    from {
        opacity: 0;
        transform: translateY(-10px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

@keyframes slide-in {
    from {
        opacity: 0;
        transform: translateX(100%);
    }
    to {
        opacity: 1;
        transform: translateX(0);
    }
}

.animate-fade-in {
    animation: fade-in 0.5s ease-out;
}

.animate-slide-in {
    animation: slide-in 0.3s ease-out;
}
</style>
