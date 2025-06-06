<template>
    <AuthenticatedLayout>
        <div class="min-h-screen bg-gray-50">
            <!-- Header -->
            <div class="bg-white shadow-sm border-b border-gray-200 px-6 py-4">
                <div class="flex items-center justify-between">
                    <div>
                        <h1 class="text-2xl font-bold text-gray-900">Dashboard</h1>
                        <p class="text-gray-600 mt-1">Get insights into your website performance</p>
                    </div>
                    <div class="flex items-center space-x-4">
                        <!-- Site Switcher -->
                        <div class="relative">
                            <select v-model="selectedWebsite" @change="handleWebsiteChange"
                                class="appearance-none bg-white border border-gray-300 rounded-lg px-4 py-2 pr-8 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                                <option v-for="site in websites" :key="site" :value="site">{{ site }}</option>
                            </select>
                            <svg class="absolute right-2 top-1/2 transform -translate-y-1/2 w-4 h-4 text-gray-400"
                                fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M19 9l-7 7-7-7" />
                            </svg>
                        </div>

                        <!-- Date Filter -->
                        <div class="relative">
                            <select v-model="selectedPeriod" @change="handlePeriodChange"
                                class="appearance-none bg-white border border-gray-300 rounded-lg px-4 py-2 pr-8 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                                <option value="Today">Today</option>
                                <option value="7 days">7 days</option>
                                <option value="30 days">30 days</option>
                                <option value="90 days">90 days</option>
                            </select>
                            <svg class="absolute right-2 top-1/2 transform -translate-y-1/2 w-4 h-4 text-gray-400"
                                fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M19 9l-7 7-7-7" />
                            </svg>
                        </div>
                    </div>
                </div>
            </div>

            <div class=" space-y-6">
                <!-- KPI Summary Cards -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-6">
                    <KPICard title="Total Visits" :value="kpiData.totalVisits.toLocaleString()" :change="12.5"
                        icon="eye" color="bg-blue-500" />
                    <KPICard title="Active Users" :value="kpiData.activeUsers" :change="8.3" icon="users"
                        color="bg-green-500" />
                    <KPICard title="Bounce Rate" :value="kpiData.bounceRate" :change="-2.1" icon="trending-up"
                        color="bg-orange-500" suffix="%" />
                    <KPICard title="Avg. Session Duration" :value="kpiData.avgSessionDuration" :change="15.7"
                        icon="clock" color="bg-purple-500" />
                    <KPICard title="SEO Health Score" :value="kpiData.seoHealthScore" :change="5.2" icon="search"
                        color="bg-indigo-500" suffix="%" />
                </div>

                <!-- Charts Section -->
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    <!-- Traffic Over Time -->
                    <div class="bg-white rounded-xl p-6 shadow-sm border border-gray-100">
                        <div class="flex items-center justify-between mb-6">
                            <h3 class="text-lg font-semibold text-gray-900">Traffic Over Time</h3>
                            <div class="flex items-center space-x-2">
                                <button @click="chartMetric = 'users'"
                                    :class="chartMetric === 'users' ? 'bg-blue-100 text-blue-700' : 'text-gray-600 hover:text-gray-900'"
                                    class="px-3 py-1 rounded-lg text-sm font-medium transition-colors">
                                    Users
                                </button>
                                <button @click="chartMetric = 'pageViews'"
                                    :class="chartMetric === 'pageViews' ? 'bg-blue-100 text-blue-700' : 'text-gray-600 hover:text-gray-900'"
                                    class="px-3 py-1 rounded-lg text-sm font-medium transition-colors">
                                    Page Views
                                </button>
                                <button @click="chartMetric = 'sessions'"
                                    :class="chartMetric === 'sessions' ? 'bg-blue-100 text-blue-700' : 'text-gray-600 hover:text-gray-900'"
                                    class="px-3 py-1 rounded-lg text-sm font-medium transition-colors">
                                    Sessions
                                </button>
                            </div>
                        </div>

                        <!-- Dynamic Chart Container -->
                        <div
                            class="h-80 bg-gradient-to-b from-blue-50 to-white rounded-lg p-4 relative overflow-hidden">
                            <svg class="w-full h-full" viewBox="0 0 700 280" preserveAspectRatio="xMidYMid meet">
                                <!-- Grid Lines -->
                                <defs>
                                    <pattern id="grid" width="100" height="40" patternUnits="userSpaceOnUse">
                                        <path d="M 100 0 L 0 0 0 40" fill="none" stroke="#e5e7eb" stroke-width="1"
                                            opacity="0.5" />
                                    </pattern>
                                    <linearGradient id="areaGradient" x1="0%" y1="0%" x2="0%" y2="100%">
                                        <stop offset="0%" style="stop-color:#3b82f6;stop-opacity:0.3" />
                                        <stop offset="100%" style="stop-color:#3b82f6;stop-opacity:0.05" />
                                    </linearGradient>
                                </defs>

                                <rect width="100%" height="100%" fill="url(#grid)" />

                                <!-- Y-Axis Labels -->
                                <g class="y-axis-labels">
                                    <text v-for="(tick, index) in yAxisTicks" :key="index" :x="30"
                                        :y="250 - (tick.value / maxChartValue * 200)" class="text-xs fill-gray-500"
                                        text-anchor="end" dominant-baseline="middle">
                                        {{ formatNumber(tick.value) }}
                                    </text>
                                </g>

                                <!-- X-Axis Labels -->
                                <g class="x-axis-labels">
                                    <text v-for="(point, index) in chartData" :key="index" :x="70 + (index * 90)"
                                        y="270" class="text-xs fill-gray-500" text-anchor="middle">
                                        {{ point.date }}
                                    </text>
                                </g>

                                <!-- Area Fill -->
                                <path :d="generateAreaPath" fill="url(#areaGradient)"
                                    class="transition-all duration-500 ease-in-out" />

                                <!-- Line Path -->
                                <path :d="generateLinePath" fill="none" stroke="#3b82f6" stroke-width="3"
                                    stroke-linecap="round" stroke-linejoin="round"
                                    class="transition-all duration-500 ease-in-out line-animate" />

                                <!-- Data Points -->
                                <g class="data-points">
                                    <circle v-for="(point, index) in chartData" :key="index" :cx="70 + (index * 90)"
                                        :cy="50 + (200 - (point[chartMetric] / maxChartValue * 200))" r="4"
                                        fill="#3b82f6" stroke="white" stroke-width="2"
                                        class="cursor-pointer hover:r-6 transition-all duration-200 point-animate"
                                        style="animation-delay: ${index * 100}ms"
                                        @mouseenter="showTooltip($event, point, index)"
                                        @mouseleave="hideChartTooltip" />
                                </g>

                                <!-- Hover Effects -->
                                <g v-if="hoveredPoint" class="hover-effects">
                                    <!-- Vertical Line -->
                                    <line :x1="70 + (hoveredIndex * 90)" :x2="70 + (hoveredIndex * 90)" y1="50" y2="250"
                                        stroke="#3b82f6" stroke-width="1" stroke-dasharray="5,5" opacity="0.7" />

                                    <!-- Highlight Point -->
                                    <circle :cx="70 + (hoveredIndex * 90)"
                                        :cy="50 + (200 - (hoveredPoint[chartMetric] / maxChartValue * 200))" r="6"
                                        fill="#3b82f6" stroke="white" stroke-width="3" class="animate-pulse" />
                                </g>
                            </svg>

                            <!-- Chart Tooltip -->
                            <div v-if="chartTooltip.visible"
                                class="absolute bg-gray-900 text-white px-3 py-2 rounded-lg text-sm shadow-xl z-10 pointer-events-none transition-all duration-200"
                                :style="{ left: chartTooltip.x + 'px', top: chartTooltip.y + 'px' }">
                                <div class="font-semibold">{{ chartTooltip.date }}</div>
                                <div class="text-blue-300">
                                    {{ chartMetric.charAt(0).toUpperCase() + chartMetric.slice(1) }}:
                                    <span class="font-bold">{{ formatNumber(chartTooltip.value) }}</span>
                                </div>
                                <div class="text-xs text-gray-300 mt-1">
                                    <span v-if="chartTooltip.change > 0" class="text-green-400">↗ +{{
                                        chartTooltip.change }}%</span>
                                    <span v-else-if="chartTooltip.change < 0" class="text-red-400">↘ {{
                                        chartTooltip.change }}%</span>
                                    <span v-else class="text-gray-400">→ No change</span>
                                    vs previous day
                                </div>
                            </div>

                            <!-- Chart Legend -->
                            <div class="absolute top-4 right-4 bg-white/80 backdrop-blur-sm rounded-lg p-3 shadow-sm">
                                <div class="flex items-center space-x-2">
                                    <div class="w-3 h-3 bg-blue-500 rounded-full"></div>
                                    <span class="text-sm font-medium text-gray-700">
                                        {{ chartMetric.charAt(0).toUpperCase() + chartMetric.slice(1) }}
                                    </span>
                                </div>
                                <div class="text-xs text-gray-500 mt-1">
                                    Avg: {{ formatNumber(averageValue) }}
                                </div>
                            </div>

                            <!-- Trend Indicator -->
                            <div class="absolute bottom-4 right-4 flex items-center space-x-2">
                                <div :class="trendDirection > 0 ? 'text-green-600' : trendDirection < 0 ? 'text-red-600' : 'text-gray-600'"
                                    class="flex items-center text-sm font-medium">
                                    <svg v-if="trendDirection > 0" class="w-4 h-4 mr-1" fill="currentColor"
                                        viewBox="0 0 20 20">
                                        <path fill-rule="evenodd"
                                            d="M3.293 9.707a1 1 0 010-1.414l6-6a1 1 0 011.414 0l6 6a1 1 0 01-1.414 1.414L11 5.414V17a1 1 0 11-2 0V5.414L5.707 9.707a1 1 0 01-1.414-1.414z"
                                            clip-rule="evenodd" />
                                    </svg>
                                    <svg v-else-if="trendDirection < 0" class="w-4 h-4 mr-1" fill="currentColor"
                                        viewBox="0 0 20 20">
                                        <path fill-rule="evenodd"
                                            d="M16.707 10.293a1 1 0 010 1.414l-6 6a1 1 0 01-1.414 0l-6-6a1 1 0 111.414-1.414L9 14.586V3a1 1 0 012 0v11.586l4.293-4.293a1 1 0 011.414 0z"
                                            clip-rule="evenodd" />
                                    </svg>
                                    <svg v-else class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M3 10a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1z"
                                            clip-rule="evenodd" />
                                    </svg>
                                    {{ Math.abs(trendDirection) }}%
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Top Pages Performance -->
                    <div class="bg-white rounded-xl p-6 shadow-sm border border-gray-100">
                        <div class="flex items-center justify-between mb-6">
                            <h3 class="text-lg font-semibold text-gray-900">Top Pages Performance</h3>
                            <button class="text-sm text-blue-600 hover:text-blue-700 font-medium">
                                View All
                            </button>
                        </div>
                        <div class="space-y-4">
                            <div v-for="(page, index) in topPagesData" :key="index"
                                class="flex items-center justify-between">
                                <div class="flex-1">
                                    <div class="flex items-center">
                                        <div class="w-8 h-4 bg-purple-500 rounded mr-3"
                                            :style="{ width: Math.max(20, (page.visits / 3000 * 100)) + 'px' }"></div>
                                        <span class="text-sm font-medium text-gray-900">{{ page.page }}</span>
                                    </div>
                                    <div class="text-xs text-gray-500 mt-1 ml-11">{{ page.visits }} visits • {{
                                        page.timeOnPage }} avg time</div>
                                </div>
                                <div class="text-sm font-semibold text-gray-700">{{ page.visits }}</div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Real-Time Feed & SEO Snapshot -->
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    <!-- Real-Time Feed -->
                    <div class="bg-white rounded-xl p-6 shadow-sm border border-gray-100">
                        <div class="flex items-center justify-between mb-6">
                            <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                                <svg class="w-5 h-5 mr-2 text-green-500" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M13 10V3L4 14h7v7l9-11h-7z" />
                                </svg>
                                Real-Time Activity
                            </h3>
                            <div class="flex items-center">
                                <div class="w-2 h-2 bg-green-500 rounded-full animate-pulse mr-2"></div>
                                <span class="text-sm text-gray-600">{{ kpiData.activeUsers }} users online</span>
                            </div>
                        </div>
                        <div class="space-y-4">
                            <div v-for="user in realtimeUsers" :key="user.id"
                                class="flex items-center space-x-3 p-3 bg-gray-50 rounded-lg animate-fade-in">
                                <div class="w-8 h-8 bg-green-100 rounded-full flex items-center justify-center">
                                    <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                    </svg>
                                </div>
                                <div class="flex-1">
                                    <p class="text-sm font-medium text-gray-900">User from {{ user.location }}</p>
                                    <p class="text-xs text-gray-500">Viewing {{ user.page }} • {{ user.timeOnline }}s
                                        online</p>
                                </div>
                                <div class="w-2 h-2 bg-green-500 rounded-full animate-pulse"></div>
                            </div>
                            <div v-if="realtimeUsers.length === 0" class="text-center py-8 text-gray-500">
                                <svg class="w-8 h-8 mx-auto mb-2 opacity-50" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M13 10V3L4 14h7v7l9-11h-7z" />
                                </svg>
                                <p class="text-sm">Waiting for real-time activity...</p>
                            </div>
                        </div>
                    </div>

                    <!-- SEO Snapshot -->
                    <div class="bg-white rounded-xl p-6 shadow-sm border border-gray-100">
                        <div class="flex items-center justify-between mb-6">
                            <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                                <svg class="w-5 h-5 mr-2 text-blue-500" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                </svg>
                                SEO Snapshot
                            </h3>
                            <button class="text-sm text-blue-600 hover:text-blue-700 font-medium">
                                Full Report
                            </button>
                        </div>
                        <div class="grid grid-cols-2 gap-4">
                            <div class="text-center p-4 bg-blue-50 rounded-lg">
                                <svg class="w-8 h-8 text-blue-600 mx-auto mb-2" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9v-9m0-9v9" />
                                </svg>
                                <p class="text-2xl font-bold text-gray-900">{{ seoData.indexedPages }}</p>
                                <p class="text-sm text-gray-600">Indexed Pages</p>
                            </div>
                            <div class="text-center p-4 bg-red-50 rounded-lg">
                                <svg class="w-8 h-8 text-red-600 mx-auto mb-2" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.732-.833-2.5 0L4.268 16.5c-.77.833.192 2.5 1.732 2.5z" />
                                </svg>
                                <p class="text-2xl font-bold text-gray-900">{{ seoData.crawlingIssues }}</p>
                                <p class="text-sm text-gray-600">Crawling Issues</p>
                            </div>
                            <div class="text-center p-4 bg-green-50 rounded-lg">
                                <svg class="w-8 h-8 text-green-600 mx-auto mb-2" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <p class="text-2xl font-bold text-gray-900">{{ seoData.jsonLdPresence }}%</p>
                                <p class="text-sm text-gray-600">JSON-LD Coverage</p>
                            </div>
                            <div class="text-center p-4 bg-orange-50 rounded-lg">
                                <svg class="w-8 h-8 text-orange-600 mx-auto mb-2" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M13 10V3L4 14h7v7l9-11h-7z" />
                                </svg>
                                <p class="text-2xl font-bold text-gray-900">{{ seoData.keywordAlerts }}</p>
                                <p class="text-sm text-gray-600">Keyword Alerts</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Recent Events & Trending Keywords -->
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    <!-- Recent Events -->
                    <div class="bg-white rounded-xl p-6 shadow-sm border border-gray-100">
                        <div class="flex items-center justify-between mb-6">
                            <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                                <svg class="w-5 h-5 mr-2 text-gray-600" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                                Recent Events
                            </h3>
                            <button class="text-sm text-blue-600 hover:text-blue-700 font-medium flex items-center">
                                View Full Report
                                <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                                </svg>
                            </button>
                        </div>
                        <div class="space-y-4">
                            <div v-for="(event, index) in recentEvents" :key="index"
                                class="flex items-start space-x-3 p-3 hover:bg-gray-50 rounded-lg transition-colors">
                                <div :class="getEventColorClass(event.type)" class="p-2 rounded-lg">
                                    <component :is="getEventIcon(event.type)" class="w-4 h-4" />
                                </div>
                                <div class="flex-1">
                                    <p class="text-sm font-medium text-gray-900">{{ event.message }}</p>
                                    <p class="text-xs text-gray-500">{{ event.time }}</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Trending Keywords -->
                    <div class="bg-white rounded-xl p-6 shadow-sm border border-gray-100">
                        <div class="flex items-center justify-between mb-6">
                            <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                                <svg class="w-5 h-5 mr-2 text-indigo-500" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z" />
                                </svg>
                                Trending Keywords
                            </h3>
                            <button class="text-sm text-blue-600 hover:text-blue-700 font-medium">
                                View All
                            </button>
                        </div>
                        <div class="space-y-3">
                            <div v-for="(keyword, index) in trendingKeywords" :key="index"
                                class="flex items-center justify-between p-3 hover:bg-gray-50 rounded-lg transition-colors">
                                <div class="flex-1">
                                    <div class="flex items-center space-x-2">
                                        <span class="text-sm font-medium text-gray-900">{{ keyword.keyword }}</span>
                                        <svg v-if="keyword.trend === 'up'" class="w-4 h-4 text-green-500"
                                            fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd"
                                                d="M3.293 9.707a1 1 0 010-1.414l6-6a1 1 0 011.414 0l6 6a1 1 0 01-1.414 1.414L11 5.414V17a1 1 0 11-2 0V5.414L5.707 9.707a1 1 0 01-1.414-1.414z"
                                                clip-rule="evenodd" />
                                        </svg>
                                        <svg v-else class="w-4 h-4 text-red-500" fill="currentColor"
                                            viewBox="0 0 20 20">
                                            <path fill-rule="evenodd"
                                                d="M16.707 10.293a1 1 0 010 1.414l-6 6a1 1 0 01-1.414 0l-6-6a1 1 0 111.414-1.414L9 14.586V3a1 1 0 012 0v11.586l4.293-4.293a1 1 0 011.414 0z"
                                                clip-rule="evenodd" />
                                        </svg>
                                    </div>
                                    <div class="flex items-center space-x-4 mt-1">
                                        <span class="text-xs text-gray-500">Pos: {{ keyword.position }}</span>
                                        <span class="text-xs text-gray-500">Vol: {{ keyword.volume.toLocaleString()
                                            }}</span>
                                        <span class="text-xs text-gray-500">CPC: ${{ keyword.cpc }}</span>
                                    </div>
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
import { ref, computed, onMounted, onUnmounted } from 'vue'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';

// Define KPI Card Component
const KPICard = {
    props: {
        title: String,
        value: [String, Number],
        change: Number,
        icon: String,
        color: String,
        suffix: {
            type: String,
            default: ''
        }
    },
    template: `
    <div class="bg-white rounded-xl p-6 shadow-sm border border-gray-100 hover:shadow-md transition-all duration-200">
      <div class="flex items-center justify-between">
        <div>
          <p class="text-sm font-medium text-gray-600 mb-2">{{ title }}</p>
          <p class="text-3xl font-bold text-gray-900">{{ value }}{{ suffix }}</p>
          <div v-if="change" class="flex items-center mt-2">
            <svg v-if="change > 0" class="w-4 h-4 text-green-500 mr-1" fill="currentColor" viewBox="0 0 20 20">
              <path fill-rule="evenodd" d="M3.293 9.707a1 1 0 010-1.414l6-6a1 1 0 011.414 0l6 6a1 1 0 01-1.414 1.414L11 5.414V17a1 1 0 11-2 0V5.414L5.707 9.707a1 1 0 01-1.414-1.414z" clip-rule="evenodd" />
            </svg>
            <svg v-else class="w-4 h-4 text-red-500 mr-1" fill="currentColor" viewBox="0 0 20 20">
              <path fill-rule="evenodd" d="M16.707 10.293a1 1 0 010 1.414l-6 6a1 1 0 01-1.414 0l-6-6a1 1 0 111.414-1.414L9 14.586V3a1 1 0 012 0v11.586l4.293-4.293a1 1 0 011.414 0z" clip-rule="evenodd" />
            </svg>
            <span :class="change > 0 ? 'text-green-600' : 'text-red-600'" class="text-sm font-medium">
              {{ Math.abs(change) }}%
            </span>
            <span class="text-sm text-gray-500 ml-1">vs last period</span>
          </div>
        </div>
        <div :class="color" class="p-3 rounded-xl">
          <component :is="getIconComponent(icon)" class="w-6 h-6 text-white" />
        </div>
      </div>
    </div>
  `,
    methods: {
        getIconComponent(iconName) {
            const iconMap = {
                eye: 'EyeIcon',
                users: 'UsersIcon',
                'trending-up': 'TrendingUpIcon',
                clock: 'ClockIcon',
                search: 'SearchIcon'
            }
            return iconMap[iconName] || 'div'
        }
    }
}

// Icon Components
const EyeIcon = {
    template: `
    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
    </svg>
  `
}

const UsersIcon = {
    template: `
    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z" />
    </svg>
  `
}

const TrendingUpIcon = {
    template: `
    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
    </svg>
  `
}

const ClockIcon = {
    template: `
    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
    </svg>
  `
}

const SearchIcon = {
    template: `
    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
    </svg>
  `
}

const UserIcon = {
    template: `
    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
    </svg>
  `
}

const ActivityIcon = {
    template: `
    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
    </svg>
  `
}

// Reactive data
const selectedPeriod = ref('7 days')
const selectedWebsite = ref('E-commerce Store')
const chartMetric = ref('users')
const realtimeUsers = ref([])
const hoveredPoint = ref(null)
const hoveredIndex = ref(-1)
const chartTooltip = ref({
    visible: false,
    x: 0,
    y: 0,
    date: '',
    value: 0,
    change: 0
})

// Sample data
const websites = [
    'E-commerce Store',
    'Blog Platform',
    'Portfolio Site',
    'SaaS Dashboard'
]

const kpiData = ref({
    totalVisits: 45238,
    activeUsers: 247,
    bounceRate: 32.5,
    avgSessionDuration: '4:32',
    seoHealthScore: 87
})

const trafficData = ref([
    { date: 'Dec 1', users: 1240, pageViews: 3420, sessions: 1180 },
    { date: 'Dec 2', users: 1180, pageViews: 3200, sessions: 1120 },
    { date: 'Dec 3', users: 1350, pageViews: 3780, sessions: 1290 },
    { date: 'Dec 4', users: 1420, pageViews: 4100, sessions: 1380 },
    { date: 'Dec 5', users: 1680, pageViews: 4850, sessions: 1620 },
    { date: 'Dec 6', users: 1520, pageViews: 4320, sessions: 1470 },
    { date: 'Dec 7', users: 1750, pageViews: 5200, sessions: 1690 }
])

const topPagesData = ref([
    { page: '/products/shoes', visits: 2840, timeOnPage: '3:24' },
    { page: '/checkout', visits: 1920, timeOnPage: '2:15' },
    { page: '/', visits: 1650, timeOnPage: '1:45' },
    { page: '/blog/summer-trends', visits: 1380, timeOnPage: '4:32' },
    { page: '/about', visits: 980, timeOnPage: '2:08' }
])

const seoData = ref({
    indexedPages: 1247,
    crawlingIssues: 3,
    jsonLdPresence: 89,
    keywordAlerts: 2
})

const recentEvents = ref([
    { type: 'user', message: 'New user from California visited /products/shoes', time: '2 min ago' },
    { type: 'seo', message: 'Page /blog/new-arrivals indexed successfully', time: '15 min ago' },
    { type: 'traffic', message: 'Traffic spike detected (+45% in last hour)', time: '23 min ago' },
    { type: 'system', message: 'Analytics tracking updated', time: '1 hour ago' },
    { type: 'user', message: 'User completed purchase worth $156.99', time: '1 hour ago' }
])

const trendingKeywords = ref([
    { keyword: 'summer shoes', position: 3, volume: 12400, cpc: 1.25, trend: 'up' },
    { keyword: 'running sneakers', position: 7, volume: 8900, cpc: 2.10, trend: 'up' },
    { keyword: 'casual footwear', position: 12, volume: 5600, cpc: 1.80, trend: 'down' },
    { keyword: 'women boots', position: 5, volume: 7300, cpc: 2.45, trend: 'up' },
    { keyword: 'sport shoes sale', position: 15, volume: 4200, cpc: 1.95, trend: 'down' }
])

const deviceBreakdown = ref([
    { name: 'Desktop', percentage: 45, users: 111, color: 'bg-blue-500' },
    { name: 'Mobile', percentage: 42, users: 104, color: 'bg-green-500' },
    { name: 'Tablet', percentage: 13, users: 32, color: 'bg-purple-500' }
])

const trafficSources = ref([
    { name: 'Google', visitors: 118, percentage: 48, color: 'bg-blue-500' },
    { name: 'Direct', visitors: 73, percentage: 30, color: 'bg-green-500' },
    { name: 'Social Media', visitors: 37, percentage: 15, color: 'bg-purple-500' },
    { name: 'Referrals', visitors: 19, percentage: 7, color: 'bg-orange-500' }
])

const performanceMetrics = ref({
    pageLoadTime: '2.3s',
    bounceRateChange: -2.1,
    conversionRate: 3.2,
    averageOrderValue: 127.50,
    customerLifetimeValue: 1250
})

const alerts = ref([
    { type: 'warning', message: 'High bounce rate detected on /checkout page', priority: 'high', time: '10 min ago' },
    { type: 'success', message: 'Page speed improved by 15% this week', priority: 'medium', time: '2 hours ago' },
    { type: 'info', message: 'New users from mobile increased by 23%', priority: 'low', time: '1 day ago' }
])

const goals = ref([
    { name: 'Monthly Revenue Target', current: 28500, target: 35000, percentage: 81.4, color: 'bg-green-500' },
    { name: 'New User Acquisition', current: 1247, target: 1500, percentage: 83.1, color: 'bg-blue-500' },
    { name: 'Conversion Rate Goal', current: 3.2, target: 4.0, percentage: 80.0, color: 'bg-purple-500' },
    { name: 'Page Load Speed', current: 2.3, target: 2.0, percentage: 87.0, color: 'bg-orange-500' }
])

const geographicData = ref([
    { country: 'United States', visitors: 12450, percentage: 45.2, flag: '🇺🇸', growth: 12.5 },
    { country: 'United Kingdom', visitors: 5680, percentage: 20.6, flag: '🇬🇧', growth: 8.3 },
    { country: 'Germany', visitors: 3420, percentage: 12.4, flag: '🇩🇪', growth: -2.1 },
    { country: 'France', visitors: 2890, percentage: 10.5, flag: '🇫🇷', growth: 15.7 },
    { country: 'Canada', visitors: 1980, percentage: 7.2, flag: '🇨🇦', growth: 5.2 },
    { country: 'Australia', visitors: 1120, percentage: 4.1, flag: '🇦🇺', growth: 22.8 }
])

const revenueData = ref([
    { period: 'Jan', revenue: 45200, orders: 342, avgOrder: 132.16 },
    { period: 'Feb', revenue: 52800, orders: 398, avgOrder: 132.66 },
    { period: 'Mar', revenue: 48600, orders: 365, avgOrder: 133.15 },
    { period: 'Apr', revenue: 61200, orders: 459, avgOrder: 133.33 },
    { period: 'May', revenue: 58900, orders: 441, avgOrder: 133.56 },
    { period: 'Jun', revenue: 67500, orders: 504, avgOrder: 133.93 },
    { period: 'Jul', revenue: 72100, orders: 537, avgOrder: 134.26 }
])

const conversionFunnel = ref([
    { stage: 'Visitors', count: 45238, percentage: 100, color: 'bg-blue-500' },
    { stage: 'Page Views', count: 38450, percentage: 85.0, color: 'bg-green-500' },
    { stage: 'Add to Cart', count: 12680, percentage: 28.0, color: 'bg-yellow-500' },
    { stage: 'Checkout Started', count: 5890, percentage: 13.0, color: 'bg-orange-500' },
    { stage: 'Purchase Completed', count: 1447, percentage: 3.2, color: 'bg-purple-500' }
])

const contentPerformance = ref([
    { title: 'Summer Collection Launch', type: 'Blog Post', views: 15420, shares: 342, engagement: 78.5 },
    { title: 'Product Demo Video', type: 'Video', views: 12680, shares: 567, engagement: 85.2 },
    { title: 'Customer Success Stories', type: 'Case Study', views: 8950, shares: 189, engagement: 72.1 },
    { title: 'How-to Guide', type: 'Tutorial', views: 7230, shares: 234, engagement: 68.9 },
    { title: 'Industry Report 2024', type: 'Whitepaper', views: 5680, shares: 456, engagement: 91.3 }
])

const userBehavior = ref({
    sessionDuration: {
        average: '4:32',
        mobile: '3:45',
        desktop: '5:18',
        tablet: '4:07'
    },
    pagesPerSession: {
        average: 3.4,
        mobile: 2.8,
        desktop: 4.1,
        tablet: 3.2
    },
    returnVisitors: 42.8,
    newVisitors: 57.2,
    dailyActiveUsers: 2847,
    weeklyActiveUsers: 15623,
    monthlyActiveUsers: 45238
})

const competitorAnalysis = ref([
    { competitor: 'Competitor A', marketShare: 28.5, traffic: 145000, keywords: 2340 },
    { competitor: 'Competitor B', marketShare: 22.1, traffic: 112000, keywords: 1890 },
    { competitor: 'Our Site', marketShare: 18.7, traffic: 95000, keywords: 1567 },
    { competitor: 'Competitor C', marketShare: 15.3, traffic: 78000, keywords: 1245 },
    { competitor: 'Competitor D', marketShare: 15.4, traffic: 72000, keywords: 987 }
])

const socialMediaMetrics = ref([
    { platform: 'Facebook', followers: 12450, engagement: 4.2, clicks: 890, conversions: 23 },
    { platform: 'Instagram', followers: 8970, engagement: 6.8, clicks: 1240, conversions: 45 },
    { platform: 'Twitter', followers: 5680, engagement: 3.1, clicks: 567, conversions: 12 },
    { platform: 'LinkedIn', followers: 3420, engagement: 5.9, clicks: 456, conversions: 34 },
    { platform: 'TikTok', followers: 15670, engagement: 12.4, clicks: 2340, conversions: 67 }
])

// Intervals
let realtimeInterval = null
let advancedInterval = null

// Computed properties for dynamic calculations
const totalRevenue = computed(() => {
    return revenueData.value.reduce((sum, month) => sum + month.revenue, 0)
})

const averageConversionRate = computed(() => {
    const funnel = conversionFunnel.value
    const visitors = funnel[0].count
    const purchases = funnel[funnel.length - 1].count
    return ((purchases / visitors) * 100).toFixed(2)
})

const topPerformingCountry = computed(() => {
    return geographicData.value.reduce((prev, current) =>
        (prev.visitors > current.visitors) ? prev : current
    )
})

const socialMediaTotal = computed(() => {
    return socialMediaMetrics.value.reduce((totals, platform) => ({
        followers: totals.followers + platform.followers,
        clicks: totals.clicks + platform.clicks,
        conversions: totals.conversions + platform.conversions
    }), { followers: 0, clicks: 0, conversions: 0 })
})

// Chart computed properties
const chartData = computed(() => {
    return trafficData.value
})

const maxChartValue = computed(() => {
    const values = chartData.value.map(d => d[chartMetric.value])
    return Math.max(...values) * 1.1 // Add 10% padding
})

const yAxisTicks = computed(() => {
    const max = maxChartValue.value
    const ticks = []
    for (let i = 0; i <= 5; i++) {
        ticks.push({
            value: Math.round((max / 5) * i)
        })
    }
    return ticks
})

const generateLinePath = computed(() => {
    const points = chartData.value.map((point, index) => {
        const x = 70 + (index * 90)
        const y = 50 + (200 - (point[chartMetric.value] / maxChartValue.value * 200))
        return `${index === 0 ? 'M' : 'L'} ${x} ${y}`
    }).join(' ')
    return points
})

const generateAreaPath = computed(() => {
    const linePoints = chartData.value.map((point, index) => {
        const x = 70 + (index * 90)
        const y = 50 + (200 - (point[chartMetric.value] / maxChartValue.value * 200))
        return `${x} ${y}`
    })

    const firstX = 70
    const lastX = 70 + ((chartData.value.length - 1) * 90)
    const bottomY = 250

    return `M ${firstX} ${bottomY} L ${linePoints.join(' L ')} L ${lastX} ${bottomY} Z`
})

const averageValue = computed(() => {
    const values = chartData.value.map(d => d[chartMetric.value])
    const sum = values.reduce((a, b) => a + b, 0)
    return Math.round(sum / values.length)
})

const trendDirection = computed(() => {
    const data = chartData.value
    if (data.length < 2) return 0

    const firstValue = data[0][chartMetric.value]
    const lastValue = data[data.length - 1][chartMetric.value]

    return Number(((lastValue - firstValue) / firstValue * 100).toFixed(1))
})

// Methods
const handleWebsiteChange = () => {
    console.log('Website changed to:', selectedWebsite.value)
}

const handlePeriodChange = () => {
    console.log('Period changed to:', selectedPeriod.value)
}

const getEventColorClass = (type) => {
    const colors = {
        user: 'bg-blue-100 text-blue-600',
        seo: 'bg-green-100 text-green-600',
        traffic: 'bg-orange-100 text-orange-600',
        system: 'bg-gray-100 text-gray-600'
    }
    return colors[type] || 'bg-gray-100 text-gray-600'
}

const getEventIcon = (type) => {
    const icons = {
        user: 'UserIcon',
        seo: 'SearchIcon',
        traffic: 'TrendingUpIcon',
        system: 'ActivityIcon'
    }
    return icons[type] || 'ActivityIcon'
}

// Advanced methods for data manipulation
const getGrowthTrend = (data, key) => {
    if (data.length < 2) return 0
    const current = data[data.length - 1][key]
    const previous = data[data.length - 2][key]
    return ((current - previous) / previous * 100).toFixed(1)
}

const calculateEngagementScore = () => {
    const avgSessionTime = 272 // 4:32 in seconds
    const avgPages = userBehavior.value.pagesPerSession.average
    const returnRate = userBehavior.value.returnVisitors

    // Weighted engagement score
    return ((avgSessionTime / 300) * 0.4 + (avgPages / 5) * 0.3 + (returnRate / 100) * 0.3 * 100).toFixed(1)
}

const getTopKeyword = () => {
    return trendingKeywords.value.reduce((prev, current) =>
        (prev.volume > current.volume) ? prev : current
    )
}

const updateRealTimeMetrics = () => {
    // Simulate real-time metric updates
    const variation = () => Math.floor(Math.random() * 21) - 10 // -10 to +10

    kpiData.value.activeUsers = Math.max(200, kpiData.value.activeUsers + variation())
    kpiData.value.bounceRate = Math.max(25, Math.min(45, kpiData.value.bounceRate + (variation() / 10)))

    // Update user behavior metrics
    userBehavior.value.dailyActiveUsers = Math.max(2500, userBehavior.value.dailyActiveUsers + variation() * 10)
}

const generateInsights = () => {
    const insights = []

    // Traffic insights
    const currentUsers = kpiData.value.activeUsers
    if (currentUsers > 300) {
        insights.push({
            type: 'success',
            title: 'High Traffic Alert',
            message: `Currently experiencing ${currentUsers} active users - 25% above average`,
            action: 'Monitor server performance'
        })
    }

    // Conversion insights
    const conversionRate = parseFloat(averageConversionRate.value)
    if (conversionRate < 3.0) {
        insights.push({
            type: 'warning',
            title: 'Low Conversion Rate',
            message: `Conversion rate of ${conversionRate}% is below industry average`,
            action: 'Optimize checkout process'
        })
    }

    // Geographic insights
    const topCountry = topPerformingCountry.value
    insights.push({
        type: 'info',
        title: 'Top Market Performance',
        message: `${topCountry.country} accounts for ${topCountry.percentage}% of total traffic`,
        action: 'Consider localized marketing'
    })

    return insights
}

const predictiveAnalytics = () => {
    // Simple predictive analytics based on current trends
    const recentRevenue = revenueData.value.slice(-3)
    const avgGrowth = recentRevenue.reduce((acc, curr, index) => {
        if (index === 0) return 0
        return acc + ((curr.revenue - recentRevenue[index - 1].revenue) / recentRevenue[index - 1].revenue * 100)
    }, 0) / (recentRevenue.length - 1)

    const lastRevenue = recentRevenue[recentRevenue.length - 1].revenue
    const predictedRevenue = Math.round(lastRevenue * (1 + avgGrowth / 100))

    return {
        nextMonthRevenue: predictedRevenue,
        growthRate: avgGrowth.toFixed(1),
        confidence: avgGrowth > 0 ? 'High' : 'Medium',
        recommendation: avgGrowth > 5 ? 'Maintain current strategy' : 'Implement growth initiatives'
    }
}

const exportDashboardData = () => {
    const dashboardData = {
        kpi: kpiData.value,
        traffic: trafficData.value,
        geographic: geographicData.value,
        revenue: revenueData.value,
        social: socialMediaMetrics.value,
        timestamp: new Date().toISOString()
    }

    const dataStr = JSON.stringify(dashboardData, null, 2)
    const dataBlob = new Blob([dataStr], { type: 'application/json' })
    const url = URL.createObjectURL(dataBlob)

    const link = document.createElement('a')
    link.href = url
    link.download = `dashboard-data-${new Date().toISOString().split('T')[0]}.json`
    link.click()

    URL.revokeObjectURL(url)
}

const refreshAllData = () => {
    console.log('Refreshing all dashboard data...')
    updateRealTimeMetrics()
}

// Chart methods
const formatNumber = (num) => {
    if (num >= 1000000) {
        return (num / 1000000).toFixed(1) + 'M'
    } else if (num >= 1000) {
        return (num / 1000).toFixed(1) + 'K'
    }
    return num.toString()
}

const showTooltip = (event, point, index) => {
    const rect = event.target.closest('svg').getBoundingClientRect()

    // Calculate change from previous day
    let change = 0
    if (index > 0) {
        const prevValue = chartData.value[index - 1][chartMetric.value]
        const currentValue = point[chartMetric.value]
        change = Number(((currentValue - prevValue) / prevValue * 100).toFixed(1))
    }

    chartTooltip.value = {
        visible: true,
        x: event.clientX - rect.left + 10,
        y: event.clientY - rect.top - 10,
        date: point.date,
        value: point[chartMetric.value],
        change: change
    }

    hoveredPoint.value = point
    hoveredIndex.value = index
}

const hideChartTooltip = () => {
    chartTooltip.value.visible = false
    hoveredPoint.value = null
    hoveredIndex.value = -1
}

// Simulate real-time user activity
const startRealtimeUpdates = () => {
    const locations = ['New York', 'Los Angeles', 'Chicago', 'Houston', 'Phoenix', 'Philadelphia']
    const pages = ['/products/shoes', '/checkout', '/', '/blog/trends', '/about']

    realtimeInterval = setInterval(() => {
        const newUser = {
            id: Date.now(),
            location: locations[Math.floor(Math.random() * locations.length)],
            page: pages[Math.floor(Math.random() * pages.length)],
            timeOnline: Math.floor(Math.random() * 300) + 60
        }

        realtimeUsers.value = [newUser, ...realtimeUsers.value.slice(0, 4)]
    }, 3000)
}

const stopRealtimeUpdates = () => {
    if (realtimeInterval) {
        clearInterval(realtimeInterval)
        realtimeInterval = null
    }
}

// Enhanced real-time updates
const startAdvancedRealTimeUpdates = () => {
    advancedInterval = setInterval(() => {
        updateRealTimeMetrics()

        // Simulate new social media engagement
        socialMediaMetrics.value.forEach(platform => {
            if (Math.random() > 0.7) {
                platform.clicks += Math.floor(Math.random() * 10) + 1
                if (Math.random() > 0.8) {
                    platform.conversions += 1
                }
            }
        })

        // Update geographic data with small variations
        geographicData.value.forEach(country => {
            if (Math.random() > 0.8) {
                const change = Math.floor(Math.random() * 20) - 10
                country.visitors = Math.max(100, country.visitors + change)
            }
        })
    }, 10000) // Update every 10 seconds
}

const stopAdvancedUpdates = () => {
    if (advancedInterval) {
        clearInterval(advancedInterval)
        advancedInterval = null
    }
}

const startAllUpdates = () => {
    startRealtimeUpdates()
    startAdvancedRealTimeUpdates()
}

const stopAllUpdates = () => {
    stopRealtimeUpdates()
    stopAdvancedUpdates()
}

// Lifecycle
onMounted(() => {
    startAllUpdates()
})

onUnmounted(() => {
    stopAllUpdates()
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

.animate-fade-in {
    animation: fade-in 0.5s ease-out;
}

/* Custom scrollbar for better UX */
::-webkit-scrollbar {
    width: 6px;
}

::-webkit-scrollbar-track {
    background: #f1f1f1;
    border-radius: 3px;
}

::-webkit-scrollbar-thumb {
    background: #c1c1c1;
    border-radius: 3px;
}

::-webkit-scrollbar-thumb:hover {
    background: #a8a8a8;
}

/* Responsive grid adjustments */
@media (max-width: 768px) {
    .grid {
        gap: 1rem;
    }
}

/* Hover effects */
.hover\:shadow-md:hover {
    box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
}

/* Loading states */
.loading-skeleton {
    background: linear-gradient(90deg, #f0f0f0 25%, #e0e0e0 50%, #f0f0f0 75%);
    background-size: 200% 100%;
    animation: loading 1.5s infinite;
}

@keyframes loading {
    0% {
        background-position: 200% 0;
    }

    100% {
        background-position: -200% 0;
    }
}

/* Status indicators */
.status-online {
    position: relative;
}

.status-online::before {
    content: '';
    position: absolute;
    top: -2px;
    right: -2px;
    width: 8px;
    height: 8px;
    background-color: #10b981;
    border: 2px solid white;
    border-radius: 50%;
}

/* Chart placeholder styling */
.chart-placeholder {
    background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);
    border: 2px dashed #cbd5e0;
    border-radius: 8px;
}

/* Real-time activity animation */
@keyframes pulse-dot {
    0% {
        transform: scale(0.95);
        box-shadow: 0 0 0 0 rgba(16, 185, 129, 0.7);
    }

    70% {
        transform: scale(1);
        box-shadow: 0 0 0 10px rgba(16, 185, 129, 0);
    }

    100% {
        transform: scale(0.95);
        box-shadow: 0 0 0 0 rgba(16, 185, 129, 0);
    }
}

.pulse-green {
    animation: pulse-dot 2s infinite;
}

/* Button hover states */
.btn-secondary {
    transition: all 0.2s ease-in-out;
}

.btn-secondary:hover {
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
}

/* Card animations */
.card-hover {
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}

.card-hover:hover {
    transform: translateY(-2px);
}

/* Metric change indicators */
.metric-positive {
    color: #059669;
    background-color: #ecfdf5;
    padding: 2px 6px;
    border-radius: 4px;
    font-size: 0.75rem;
    font-weight: 600;
}

.metric-negative {
    color: #dc2626;
    background-color: #fef2f2;
    padding: 2px 6px;
    border-radius: 4px;
    font-size: 0.75rem;
    font-weight: 600;
}

/* Progress bars */
.progress-bar {
    background-color: #e5e7eb;
    border-radius: 9999px;
    overflow: hidden;
}

.progress-fill {
    height: 100%;
    background: linear-gradient(90deg, #8b5cf6, #a855f7);
    transition: width 0.5s ease-in-out;
    border-radius: 9999px;
}

/* Responsive text */
@media (max-width: 640px) {
    .text-responsive {
        font-size: 1.5rem;
    }
}

@media (min-width: 641px) {
    .text-responsive {
        font-size: 2rem;
    }
}

/* Focus states for accessibility */
select:focus,
button:focus {
    outline: 2px solid #3b82f6;
    outline-offset: 2px;
}

/* Dark mode support (if needed) */
@media (prefers-color-scheme: dark) {
    .dark-mode {
        background-color: #1f2937;
        color: #f9fafb;
    }

    .dark-mode .bg-white {
        background-color: #374151;
    }

    .dark-mode .text-gray-900 {
        color: #f9fafb;
    }

    .dark-mode .border-gray-100 {
        border-color: #4b5563;
    }
}

/* Dynamic Chart Animations */
.line-animate {
    stroke-dasharray: 1000;
    stroke-dashoffset: 1000;
    animation: drawLine 2s ease-in-out forwards;
}

@keyframes drawLine {
    to {
        stroke-dashoffset: 0;
    }
}

.point-animate {
    opacity: 0;
    animation: fadeInPoint 0.5s ease-in-out forwards;
}

@keyframes fadeInPoint {
    from {
        opacity: 0;
        transform: scale(0);
    }

    to {
        opacity: 1;
        transform: scale(1);
    }
}

/* Chart hover effects */
.data-points circle:hover {
    filter: drop-shadow(0 4px 8px rgba(59, 130, 246, 0.3));
    transition: all 0.2s ease;
}

/* Smooth transitions for metric changes */
.transition-chart {
    transition: all 0.8s cubic-bezier(0.4, 0, 0.2, 1);
}

/* Tooltip animations */
.chart-tooltip-enter-active,
.chart-tooltip-leave-active {
    transition: all 0.2s ease;
}

.chart-tooltip-enter-from,
.chart-tooltip-leave-to {
    opacity: 0;
    transform: translateY(-5px);
}
</style>
