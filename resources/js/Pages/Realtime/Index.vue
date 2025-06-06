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
                <!-- Website Overview Cards (when "All Websites" is selected) -->
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
                            <div :class="site.status === 'active' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-600'"
                                class="px-2 py-1 rounded-full text-xs font-medium">
                                {{ site.status }}
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
                                <div :class="currentWebsite?.status === 'active' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-600'"
                                    class="px-3 py-1 rounded-full text-sm font-medium">
                                    {{ currentWebsite?.status }}
                                </div>
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
                                    <p class="text-3xl font-bold text-gray-900 mt-2">{{ currentSiteData.usersOnline }}</p>
                                    <div class="flex items-center mt-2">
                                        <svg class="w-4 h-4 text-green-500 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M3.293 9.707a1 1 0 010-1.414l6-6a1 1 0 011.414 0l6 6a1 1 0 01-1.414 1.414L11 5.414V17a1 1 0 11-2 0V5.414L5.707 9.707a1 1 0 01-1.414-1.414z" clip-rule="evenodd" />
                                        </svg>
                                        <span class="text-sm text-green-600 font-medium">+12%</span>
                                        <span class="text-sm text-gray-500 ml-1">vs last hour</span>
                                    </div>
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
                                    <p class="text-3xl font-bold text-gray-900 mt-2">{{ currentSiteData.activePages }}</p>
                                    <div class="flex items-center mt-2">
                                        <div class="w-4 h-4 bg-purple-500 rounded mr-1"></div>
                                        <span class="text-sm text-gray-500">{{ currentSiteData.totalPages }} total pages</span>
                                    </div>
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
                                    <p class="text-3xl font-bold text-gray-900 mt-2">{{ currentSiteData.avgSessionDuration }}</p>
                                    <div class="flex items-center mt-2">
                                        <svg class="w-4 h-4 text-green-500 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M3.293 9.707a1 1 0 010-1.414l6-6a1 1 0 011.414 0l6 6a1 1 0 01-1.414 1.414L11 5.414V17a1 1 0 11-2 0V5.414L5.707 9.707a1 1 0 01-1.414-1.414z" clip-rule="evenodd" />
                                        </svg>
                                        <span class="text-sm text-green-600 font-medium">+8%</span>
                                        <span class="text-sm text-gray-500 ml-1">improvement</span>
                                    </div>
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
                                    <p class="text-2xl font-bold text-gray-900 mt-2">{{ currentSiteData.topSource.name }}</p>
                                    <div class="flex items-center mt-2">
                                        <div class="w-4 h-4 bg-orange-500 rounded mr-1"></div>
                                        <span class="text-sm text-gray-500">{{ currentSiteData.topSource.percentage }}% of traffic</span>
                                    </div>
                                </div>
                                <div class="p-3 bg-orange-100 rounded-xl">
                                    <svg class="w-6 h-6 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
                                    </svg>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                        <!-- Live Visitor Map -->
                        <div class="lg:col-span-2 bg-white rounded-xl shadow-sm border border-gray-200">
                            <div class="p-6 border-b border-gray-200">
                                <div class="flex items-center justify-between">
                                    <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                                        <svg class="w-5 h-5 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                        Live Visitor Map
                                    </h3>
                                    <div class="flex items-center space-x-2 text-sm text-gray-600">
                                        <div class="w-2 h-2 bg-green-500 rounded-full animate-pulse"></div>
                                        <span>{{ currentSiteData.visitorLocations.length }} active visitors</span>
                                    </div>
                                </div>
                            </div>
                            <div class="p-6">
                                <!-- World Map Container -->
                                <div class="relative h-80 bg-gradient-to-br from-blue-50 to-indigo-100 rounded-lg overflow-hidden">
                                    <!-- Simplified World Map Background -->
                                    <div class="absolute inset-0 opacity-20">
                                        <svg viewBox="0 0 800 400" class="w-full h-full">
                                            <!-- Simplified continents -->
                                            <path d="M100,100 Q200,80 300,120 Q400,100 500,110 Q600,90 700,100 L700,200 Q600,220 500,200 Q400,210 300,190 Q200,200 100,180 Z" fill="#1e40af" opacity="0.3"/>
                                            <path d="M150,250 Q250,230 350,260 Q450,240 550,250 L550,320 Q450,340 350,320 Q250,330 150,310 Z" fill="#1e40af" opacity="0.3"/>
                                            <path d="M450,80 Q550,60 650,80 Q700,70 750,75 L750,160 Q700,180 650,160 Q550,170 450,150 Z" fill="#1e40af" opacity="0.3"/>
                                        </svg>
                                    </div>

                                    <!-- Live Visitor Dots -->
                                    <div v-for="visitor in currentSiteData.visitorLocations" :key="visitor.id"
                                        class="absolute w-3 h-3 bg-red-500 rounded-full animate-ping cursor-pointer"
                                        :style="{ left: visitor.x + '%', top: visitor.y + '%' }"
                                        @mouseenter="showVisitorTooltip($event, visitor)"
                                        @mouseleave="hideTooltip">
                                        <div class="w-3 h-3 bg-red-400 rounded-full animate-pulse"></div>
                                    </div>

                                    <!-- Activity Bubbles -->
                                    <div v-for="activity in currentSiteData.recentActivities.slice(0, 5)" :key="activity.id"
                                        class="absolute activity-bubble"
                                        :style="{ left: activity.x + '%', top: activity.y + '%' }">
                                        <div class="w-6 h-6 bg-yellow-400 rounded-full opacity-70 animate-bounce flex items-center justify-center">
                                            <div class="w-2 h-2 bg-white rounded-full"></div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Map Legend -->
                                <div class="flex items-center justify-between mt-4 text-sm text-gray-600">
                                    <div class="flex items-center space-x-4">
                                        <div class="flex items-center">
                                            <div class="w-3 h-3 bg-red-500 rounded-full mr-2 animate-pulse"></div>
                                            <span>Active Visitors</span>
                                        </div>
                                        <div class="flex items-center">
                                            <div class="w-3 h-3 bg-yellow-400 rounded-full mr-2"></div>
                                            <span>Recent Activity</span>
                                        </div>
                                    </div>
                                    <span>Real-time updates</span>
                                </div>
                            </div>
                        </div>

                        <!-- Current Pages Panel -->
                        <div class="bg-white rounded-xl shadow-sm border border-gray-200">
                            <div class="p-6 border-b border-gray-200">
                                <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                                    <svg class="w-5 h-5 mr-2 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                                    </svg>
                                    Top Active Pages
                                </h3>
                            </div>
                            <div class="p-6">
                                <div class="space-y-4">
                                    <div v-for="page in currentSiteData.topPages" :key="page.id"
                                        class="flex items-center justify-between p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors cursor-pointer">
                                        <div class="flex-1">
                                            <p class="font-medium text-gray-900 text-sm">{{ page.title }}</p>
                                            <p class="text-xs text-gray-500 mt-1">{{ page.url }}</p>
                                        </div>
                                        <div class="flex items-center space-x-2">
                                            <div class="flex items-center">
                                                <div class="w-2 h-2 bg-green-500 rounded-full mr-1 animate-pulse"></div>
                                                <span class="text-sm font-medium text-gray-900">{{ page.activeUsers }}</span>
                                            </div>
                                            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                            </svg>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Activity Feed and Analytics -->
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                        <!-- Live Activity Feed -->
                        <div class="bg-white rounded-xl shadow-sm border border-gray-200">
                            <div class="p-6 border-b border-gray-200">
                                <div class="flex items-center justify-between">
                                    <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                                        <svg class="w-5 h-5 mr-2 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                                        </svg>
                                        Live Activity Feed
                                    </h3>
                                    <button @click="clearFeed" class="text-sm text-gray-500 hover:text-gray-700">
                                        Clear
                                    </button>
                                </div>
                            </div>
                            <div class="max-h-96 overflow-y-auto">
                                <div class="p-6 space-y-3">
                                    <div v-for="activity in currentSiteData.recentActivities" :key="activity.id"
                                        class="flex items-center space-x-3 p-3 bg-gray-50 rounded-lg animate-fade-in">
                                        <div class="flex-shrink-0">
                                            <img :src="activity.countryFlag" :alt="activity.country" class="w-6 h-6 rounded">
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <div class="flex items-center space-x-2">
                                                <span class="text-sm font-medium text-gray-900">{{ activity.page }}</span>
                                                <span class="text-xs text-gray-500">{{ activity.timestamp }}</span>
                                            </div>
                                            <div class="flex items-center space-x-2 mt-1">
                                                <span class="text-xs text-gray-500">{{ activity.referrer }}</span>
                                                <span class="text-xs text-gray-400">•</span>
                                                <span class="text-xs text-gray-500">{{ activity.device }}</span>
                                                <span class="text-xs text-gray-400">•</span>
                                                <span class="text-xs text-gray-500">{{ activity.country }}</span>
                                            </div>
                                        </div>
                                        <div class="flex-shrink-0">
                                            <div :class="getDeviceIconClass(activity.device)"
                                                class="w-4 h-4">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Traffic Sources & Device Breakdown -->
                        <div class="space-y-6">
                            <!-- Traffic Sources -->
                            <div class="bg-white rounded-xl shadow-sm border border-gray-200">
                                <div class="p-6 border-b border-gray-200">
                                    <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                                        <svg class="w-5 h-5 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                                        </svg>
                                        Traffic Sources
                                    </h3>
                                </div>
                                <div class="p-6">
                                    <div class="space-y-3">
                                        <div v-for="source in currentSiteData.trafficSources" :key="source.name"
                                            class="flex items-center justify-between">
                                            <div class="flex items-center">
                                                <div :class="source.color" class="w-3 h-3 rounded-full mr-3"></div>
                                                <span class="text-sm font-medium text-gray-900">{{ source.name }}</span>
                                            </div>
                                            <div class="flex items-center space-x-2">
                                                <span class="text-sm text-gray-600">{{ source.visitors }}</span>
                                                <span class="text-xs text-gray-500">({{ source.percentage }}%)</span>
                                            </div>
                                        </div>
                                    </div>
                                    <!-- Progress bars -->
                                    <div class="mt-4 space-y-2">
                                        <div v-for="source in currentSiteData.trafficSources" :key="source.name" class="flex items-center">
                                            <div class="flex-1 bg-gray-200 rounded-full h-2 mr-3">
                                                <div :class="source.color"
                                                    class="h-2 rounded-full transition-all duration-500 ease-out"
                                                    :style="{ width: source.percentage + '%' }"></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Device & OS Breakdown -->
                            <div class="bg-white rounded-xl shadow-sm border border-gray-200">
                                <div class="p-6 border-b border-gray-200">
                                    <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                                        <svg class="w-5 h-5 mr-2 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z" />
                                        </svg>
                                        Device & OS
                                    </h3>
                                </div>
                                <div class="p-6">
                                    <div class="grid grid-cols-2 gap-4">
                                        <div v-for="device in currentSiteData.deviceBreakdown" :key="device.name"
                                            class="text-center p-3 bg-gray-50 rounded-lg">
                                            <div class="text-2xl mb-2">{{ device.icon }}</div>
                                            <div class="text-sm font-medium text-gray-900">{{ device.name }}</div>
                                            <div class="text-xs text-gray-500 mt-1">{{ device.percentage }}%</div>
                                            <div class="text-xs font-medium text-gray-900">{{ device.users }} users</div>
                                        </div>
                                    </div>
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

            <!-- Tooltip -->
            <div v-if="tooltip.visible"
                class="fixed bg-gray-900 text-white px-3 py-2 rounded-lg text-sm shadow-xl z-50 pointer-events-none"
                :style="{ left: tooltip.x + 'px', top: tooltip.y + 'px' }">
                <div class="font-semibold">{{ tooltip.title }}</div>
                <div class="text-gray-300 text-xs mt-1">{{ tooltip.content }}</div>
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
import { ref, computed, onMounted, onUnmounted } from 'vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';

// Reactive data
const autoRefresh = ref(true);
const lastUpdated = ref(new Date().toLocaleTimeString());
const showTrafficAlert = ref(false);
const selectedWebsite = ref('all');

// Websites data
const websites = ref([
    {
        id: 'site1',
        name: 'E-commerce Store',
        domain: 'mystore.com',
        status: 'active',
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
        stats: {
            usersOnline: 89,
            pageViews: 1850,
            trend: -3
        }
    },
    {
        id: 'site4',
        name: 'SaaS Dashboard',
        domain: 'saas-app.io',
        status: 'active',
        stats: {
            usersOnline: 312,
            pageViews: 12750,
            trend: 18
        }
    },
    {
        id: 'site5',
        name: 'Landing Page',
        domain: 'landing.co',
        status: 'inactive',
        stats: {
            usersOnline: 23,
            pageViews: 450,
            trend: -12
        }
    }
]);

// Website-specific data
const websiteData = ref({
    site1: {
        usersOnline: 247,
        activePages: 15,
        totalPages: 84,
        avgSessionDuration: '4:32',
        topSource: { name: 'Google', percentage: 48 },
        visitorLocations: [
            { id: 1, x: 25, y: 35, country: 'United States', device: 'Desktop', page: '/products/shoes' },
            { id: 2, x: 45, y: 25, country: 'United Kingdom', device: 'Mobile', page: '/checkout' },
            { id: 3, x: 55, y: 40, country: 'Germany', device: 'Tablet', page: '/about' },
            { id: 4, x: 15, y: 45, country: 'Canada', device: 'Desktop', page: '/blog' },
            { id: 5, x: 75, y: 50, country: 'Japan', device: 'Mobile', page: '/products/bags' }
        ],
        topPages: [
            { id: 1, title: 'Product Gallery', url: '/products/callie-sling-crossbody', activeUsers: 45 },
            { id: 2, title: 'Checkout', url: '/checkout', activeUsers: 32 },
            { id: 3, title: 'Homepage', url: '/', activeUsers: 28 },
            { id: 4, title: 'Collection Page', url: '/collections/crossbody', activeUsers: 19 },
            { id: 5, title: 'Contact Us', url: '/contact', activeUsers: 12 }
        ],
        recentActivities: [
            {
                id: 1, timestamp: '2 sec ago', page: '/products/callie-sling-crossbody',
                referrer: 'Google', device: 'Desktop', country: 'United States',
                countryFlag: 'https://flagcdn.com/16x12/us.png', x: 25, y: 35
            },
            {
                id: 2, timestamp: '8 sec ago', page: '/checkout',
                referrer: 'Direct', device: 'Mobile', country: 'United Kingdom',
                countryFlag: 'https://flagcdn.com/16x12/gb.png', x: 45, y: 25
            }
        ],
        trafficSources: [
            { name: 'Google', visitors: 118, percentage: 48, color: 'bg-blue-500' },
            { name: 'Direct', visitors: 73, percentage: 30, color: 'bg-green-500' },
            { name: 'Social Media', visitors: 37, percentage: 15, color: 'bg-purple-500' },
            { name: 'Referrals', visitors: 19, percentage: 7, color: 'bg-orange-500' }
        ],
        deviceBreakdown: [
            { name: 'Desktop', icon: '💻', percentage: 45, users: 111 },
            { name: 'Mobile', icon: '📱', percentage: 42, users: 104 },
            { name: 'Tablet', icon: '📱', percentage: 13, users: 32 }
        ]
    },
    site2: {
        usersOnline: 156,
        activePages: 12,
        totalPages: 245,
        avgSessionDuration: '6:18',
        topSource: { name: 'Social Media', percentage: 52 },
        visitorLocations: [
            { id: 1, x: 35, y: 30, country: 'France', device: 'Desktop', page: '/blog/tech-trends' },
            { id: 2, x: 55, y: 35, country: 'Germany', device: 'Mobile', page: '/categories/lifestyle' },
            { id: 3, x: 25, y: 40, country: 'United States', device: 'Tablet', page: '/about' }
        ],
        topPages: [
            { id: 1, title: 'Tech Trends 2025', url: '/blog/tech-trends-2025', activeUsers: 42 },
            { id: 2, title: 'Lifestyle Tips', url: '/categories/lifestyle', activeUsers: 28 },
            { id: 3, title: 'About Us', url: '/about', activeUsers: 24 },
            { id: 4, title: 'Contact', url: '/contact', activeUsers: 18 }
        ],
        recentActivities: [
            {
                id: 1, timestamp: '1 sec ago', page: '/blog/tech-trends-2025',
                referrer: 'Twitter', device: 'Desktop', country: 'France',
                countryFlag: 'https://flagcdn.com/16x12/fr.png', x: 35, y: 30
            }
        ],
        trafficSources: [
            { name: 'Social Media', visitors: 81, percentage: 52, color: 'bg-purple-500' },
            { name: 'Google', visitors: 47, percentage: 30, color: 'bg-blue-500' },
            { name: 'Direct', visitors: 28, percentage: 18, color: 'bg-green-500' }
        ],
        deviceBreakdown: [
            { name: 'Desktop', icon: '💻', percentage: 38, users: 59 },
            { name: 'Mobile', icon: '📱', percentage: 52, users: 81 },
            { name: 'Tablet', icon: '📱', percentage: 10, users: 16 }
        ]
    },
    site3: {
        usersOnline: 89,
        activePages: 8,
        totalPages: 15,
        avgSessionDuration: '3:45',
        topSource: { name: 'Direct', percentage: 65 },
        visitorLocations: [
            { id: 1, x: 25, y: 35, country: 'United States', device: 'Desktop', page: '/portfolio' },
            { id: 2, x: 65, y: 30, country: 'France', device: 'Mobile', page: '/contact' }
        ],
        topPages: [
            { id: 1, title: 'Portfolio', url: '/portfolio', activeUsers: 35 },
            { id: 2, title: 'About', url: '/about', activeUsers: 22 },
            { id: 3, title: 'Contact', url: '/contact', activeUsers: 18 },
            { id: 4, title: 'Resume', url: '/resume', activeUsers: 14 }
        ],
        recentActivities: [
            {
                id: 1, timestamp: '5 sec ago', page: '/portfolio',
                referrer: 'Direct', device: 'Desktop', country: 'United States',
                countryFlag: 'https://flagcdn.com/16x12/us.png', x: 25, y: 35
            }
        ],
        trafficSources: [
            { name: 'Direct', visitors: 58, percentage: 65, color: 'bg-green-500' },
            { name: 'Google', visitors: 22, percentage: 25, color: 'bg-blue-500' },
            { name: 'LinkedIn', visitors: 9, percentage: 10, color: 'bg-indigo-500' }
        ],
        deviceBreakdown: [
            { name: 'Desktop', icon: '💻', percentage: 68, users: 61 },
            { name: 'Mobile', icon: '📱', percentage: 28, users: 25 },
            { name: 'Tablet', icon: '📱', percentage: 4, users: 3 }
        ]
    },
    site4: {
        usersOnline: 312,
        activePages: 22,
        totalPages: 156,
        avgSessionDuration: '8:42',
        topSource: { name: 'Direct', percentage: 58 },
        visitorLocations: [
            { id: 1, x: 25, y: 35, country: 'United States', device: 'Desktop', page: '/dashboard' },
            { id: 2, x: 45, y: 25, country: 'United Kingdom', device: 'Desktop', page: '/analytics' },
            { id: 3, x: 55, y: 40, country: 'Germany', device: 'Desktop', page: '/settings' },
            { id: 4, x: 75, y: 50, country: 'Japan', device: 'Mobile', page: '/reports' }
        ],
        topPages: [
            { id: 1, title: 'Dashboard', url: '/dashboard', activeUsers: 89 },
            { id: 2, title: 'Analytics', url: '/analytics', activeUsers: 67 },
            { id: 3, title: 'Reports', url: '/reports', activeUsers: 45 },
            { id: 4, title: 'Settings', url: '/settings', activeUsers: 32 }
        ],
        recentActivities: [
            {
                id: 1, timestamp: 'Just now', page: '/dashboard',
                referrer: 'Direct', device: 'Desktop', country: 'United States',
                countryFlag: 'https://flagcdn.com/16x12/us.png', x: 25, y: 35
            }
        ],
        trafficSources: [
            { name: 'Direct', visitors: 181, percentage: 58, color: 'bg-green-500' },
            { name: 'Google', visitors: 94, percentage: 30, color: 'bg-blue-500' },
            { name: 'Referrals', visitors: 37, percentage: 12, color: 'bg-orange-500' }
        ],
        deviceBreakdown: [
            { name: 'Desktop', icon: '💻', percentage: 78, users: 243 },
            { name: 'Mobile', icon: '📱', percentage: 18, users: 56 },
            { name: 'Tablet', icon: '📱', percentage: 4, users: 13 }
        ]
    },
    site5: {
        usersOnline: 23,
        activePages: 3,
        totalPages: 8,
        avgSessionDuration: '2:15',
        topSource: { name: 'Google Ads', percentage: 72 },
        visitorLocations: [
            { id: 1, x: 25, y: 35, country: 'United States', device: 'Mobile', page: '/landing' }
        ],
        topPages: [
            { id: 1, title: 'Landing Page', url: '/landing', activeUsers: 15 },
            { id: 2, title: 'Pricing', url: '/pricing', activeUsers: 5 },
            { id: 3, title: 'Contact', url: '/contact', activeUsers: 3 }
        ],
        recentActivities: [
            {
                id: 1, timestamp: '12 sec ago', page: '/landing',
                referrer: 'Google Ads', device: 'Mobile', country: 'United States',
                countryFlag: 'https://flagcdn.com/16x12/us.png', x: 25, y: 35
            }
        ],
        trafficSources: [
            { name: 'Google Ads', visitors: 17, percentage: 72, color: 'bg-yellow-500' },
            { name: 'Direct', visitors: 4, percentage: 18, color: 'bg-green-500' },
            { name: 'Social Media', visitors: 2, percentage: 10, color: 'bg-purple-500' }
        ],
        deviceBreakdown: [
            { name: 'Mobile', icon: '📱', percentage: 65, users: 15 },
            { name: 'Desktop', icon: '💻', percentage: 30, users: 7 },
            { name: 'Tablet', icon: '📱', percentage: 5, users: 1 }
        ]
    }
});

// Computed properties
const currentWebsite = computed(() => {
    return websites.value.find(site => site.id === selectedWebsite.value);
});

const currentSiteData = computed(() => {
    return websiteData.value[selectedWebsite.value] || {};
});

const totalStats = computed(() => {
    const active = websites.value.filter(site => site.status === 'active');
    return {
        usersOnline: active.reduce((sum, site) => sum + site.stats.usersOnline, 0),
        pageViews: active.reduce((sum, site) => sum + site.stats.pageViews, 0),
        activeSites: active.length,
        avgSessionDuration: '5:47' // Average across all sites
    };
});

// Tooltip
const tooltip = ref({
    visible: false,
    x: 0,
    y: 0,
    title: '',
    content: ''
});

// Intervals
let refreshInterval = null;
let activityInterval = null;

// Methods
const switchWebsite = () => {
    // Update data when switching websites
    if (selectedWebsite.value !== 'all') {
        updateStats();
    }
};

const toggleAutoRefresh = () => {
    autoRefresh.value = !autoRefresh.value;
    if (autoRefresh.value) {
        startAutoRefresh();
    } else {
        stopAutoRefresh();
    }
};

const startAutoRefresh = () => {
    refreshInterval = setInterval(() => {
        updateStats();
        lastUpdated.value = new Date().toLocaleTimeString();
    }, 5000);

    activityInterval = setInterval(() => {
        addNewActivity();
    }, 3000);
};

const stopAutoRefresh = () => {
    if (refreshInterval) {
        clearInterval(refreshInterval);
        refreshInterval = null;
    }
    if (activityInterval) {
        clearInterval(activityInterval);
        activityInterval = null;
    }
};

const updateStats = () => {
    const variation = () => Math.floor(Math.random() * 21) - 10;

    if (selectedWebsite.value === 'all') {
        // Update all websites
        websites.value.forEach(site => {
            if (site.status === 'active') {
                site.stats.usersOnline = Math.max(20, site.stats.usersOnline + variation());
                site.stats.pageViews += Math.max(0, Math.floor(Math.random() * 50));
            }
        });
    } else {
        // Update specific website
        const site = websites.value.find(s => s.id === selectedWebsite.value);
        if (site && site.status === 'active') {
            site.stats.usersOnline = Math.max(20, site.stats.usersOnline + variation());
            websiteData.value[selectedWebsite.value].usersOnline = site.stats.usersOnline;
        }
    }

    // Randomly trigger traffic alert
    if (Math.random() < 0.1) {
        showTrafficAlert.value = true;
        setTimeout(() => {
            showTrafficAlert.value = false;
        }, 8000);
    }
};

const addNewActivity = () => {
    if (selectedWebsite.value === 'all') return;

    const countries = [
        { name: 'United States', flag: 'https://flagcdn.com/16x12/us.png', x: 25, y: 35 },
        { name: 'United Kingdom', flag: 'https://flagcdn.com/16x12/gb.png', x: 45, y: 25 },
        { name: 'Germany', flag: 'https://flagcdn.com/16x12/de.png', x: 55, y: 40 },
        { name: 'Canada', flag: 'https://flagcdn.com/16x12/ca.png', x: 15, y: 45 },
        { name: 'Japan', flag: 'https://flagcdn.com/16x12/jp.png', x: 75, y: 50 },
        { name: 'France', flag: 'https://flagcdn.com/16x12/fr.png', x: 65, y: 30 }
    ];

    const siteData = websiteData.value[selectedWebsite.value];
    if (!siteData) return;

    const pages = siteData.topPages.map(p => p.url);
    const referrers = siteData.trafficSources.map(s => s.name);
    const devices = ['Desktop', 'Mobile', 'Tablet'];

    const randomCountry = countries[Math.floor(Math.random() * countries.length)];
    const newActivity = {
        id: Date.now(),
        timestamp: 'Just now',
        page: pages[Math.floor(Math.random() * pages.length)],
        referrer: referrers[Math.floor(Math.random() * referrers.length)],
        device: devices[Math.floor(Math.random() * devices.length)],
        country: randomCountry.name,
        countryFlag: randomCountry.flag,
        x: randomCountry.x + (Math.random() * 10 - 5),
        y: randomCountry.y + (Math.random() * 10 - 5)
    };

    siteData.recentActivities.unshift(newActivity);
    if (siteData.recentActivities.length > 15) {
        siteData.recentActivities.pop();
    }

    // Update timestamps
    siteData.recentActivities.forEach((activity, index) => {
        if (index === 0) return;
        const seconds = index * 7 + Math.floor(Math.random() * 10);
        if (seconds < 60) {
            activity.timestamp = `${seconds} sec ago`;
        } else {
            activity.timestamp = `${Math.floor(seconds / 60)} min ago`;
        }
    });
};

const clearFeed = () => {
    if (selectedWebsite.value !== 'all') {
        websiteData.value[selectedWebsite.value].recentActivities = [];
    }
};

const showVisitorTooltip = (event, visitor) => {
    tooltip.value = {
        visible: true,
        x: event.pageX + 10,
        y: event.pageY - 10,
        title: `${visitor.country} - ${visitor.device}`,
        content: `Viewing: ${visitor.page}`
    };
};

const hideTooltip = () => {
    tooltip.value.visible = false;
};

const getDeviceIconClass = (device) => {
    const icons = {
        'Desktop': 'text-blue-600',
        'Mobile': 'text-green-600',
        'Tablet': 'text-purple-600'
    };
    return icons[device] || 'text-gray-600';
};

// Lifecycle
onMounted(() => {
    if (autoRefresh.value) {
        startAutoRefresh();
    }

    setTimeout(() => {
        showTrafficAlert.value = true;
        setTimeout(() => {
            showTrafficAlert.value = false;
        }, 6000);
    }, 3000);
});

onUnmounted(() => {
    stopAutoRefresh();
});
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

.activity-bubble {
    animation: bounce 2s infinite;
}

@keyframes bounce {
    0%, 20%, 53%, 80%, 100% {
        transform: translate3d(0, 0, 0);
    }
    40%, 43% {
        transform: translate3d(0, -8px, 0);
    }
    70% {
        transform: translate3d(0, -4px, 0);
    }
    90% {
        transform: translate3d(0, -2px, 0);
    }
}
</style>
