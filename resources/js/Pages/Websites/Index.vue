<template>
    <AuthenticatedLayout>

        <div class="min-h-screen bg-gradient-to-br from-blue-50 via-white to-purple-50">
            <!-- Header Section -->
            <div class="bg-white shadow-sm border-b border-gray-200">
                <div class="px-6 py-6">
                    <div class="flex items-center justify-between mb-4">
                        <div>
                            <h1 class="text-3xl font-bold text-gray-900 mb-2">Website Performance Overview</h1>
                            <p class="text-gray-600 flex items-center">
                                <svg class="w-4 h-4 mr-2 text-blue-500" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                                Showing data for: {{ dateRangeText }}
                            </p>
                        </div>
                        <div class="flex items-center space-x-3">
                            <!-- Compare Toggle -->
                            <div class="flex items-center space-x-2">
                                <span class="text-sm text-gray-600">Compare with previous period</span>
                                <button @click="compareMode = !compareMode"
                                    :class="compareMode ? 'bg-blue-500' : 'bg-gray-300'"
                                    class="relative inline-flex h-6 w-11 items-center rounded-full transition-colors">
                                    <span :class="compareMode ? 'translate-x-6' : 'translate-x-1'"
                                        class="inline-block h-4 w-4 transform rounded-full bg-white transition-transform"></span>
                                </button>
                            </div>

                            <!-- Export Buttons -->
                            <div class="flex items-center space-x-2">
                                <button @click="exportData('pdf')"
                                    class="p-2 text-gray-600 hover:text-gray-900 hover:bg-gray-100 rounded-lg transition-colors">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                    </svg>
                                </button>
                                <button @click="exportData('csv')"
                                    class="p-2 text-gray-600 hover:text-gray-900 hover:bg-gray-100 rounded-lg transition-colors">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a4 4 0 01-4-4V5a4 4 0 014-4h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a4 4 0 01-4 4z" />
                                    </svg>
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Period Selector -->
                    <div class="flex items-center justify-between">
                        <div class="flex items-center space-x-2">
                            <button v-for="period in periods" :key="period.value" @click="selectedPeriod = period.value"
                                :class="selectedPeriod === period.value ? 'bg-blue-500 text-white' : 'bg-white text-gray-700 hover:bg-gray-50'"
                                class="px-4 py-2 text-sm font-medium rounded-lg border border-gray-300 transition-colors">
                                {{ period.label }}
                            </button>
                            <button @click="showCustomRange = true"
                                class="px-4 py-2 text-sm font-medium text-gray-700 bg-white rounded-lg border border-gray-300 hover:bg-gray-50 transition-colors flex items-center">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                                Custom Range
                            </button>
                        </div>

                        <!-- Sort Controls -->
                        <div class="flex items-center space-x-3">
                            <span class="text-sm text-gray-600">Sort by:</span>
                            <select v-model="sortBy"
                                class="border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                                <option value="traffic">Traffic</option>
                                <option value="engagement">Engagement</option>
                                <option value="seo">SEO Performance</option>
                                <option value="ctr">Click-through Rate</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>

            <div class=" pt-5 space-y-8">
                <!-- Enhanced Metric Cards -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-6">
                    <!-- Total Visitors Card -->
                    <div
                        class="bg-white rounded-xl p-6 shadow-sm border border-gray-100 hover:shadow-md transition-all duration-200 relative overflow-hidden">
                        <div class="flex items-start justify-between mb-4">
                            <div>
                                <div class="flex items-center space-x-2 mb-2">
                                    <p class="text-sm font-medium text-gray-600">Total Visitors</p>
                                    <button @mouseenter="showTooltip($event, 'Total unique visitors to your website')"
                                        @mouseleave="hideTooltip" class="text-gray-400 hover:text-gray-600">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                    </button>
                                </div>
                                <p class="text-3xl font-bold text-gray-900">{{ formatNumber(metrics.totalVisitors) }}
                                </p>
                                <div class="flex items-center mt-2">
                                    <span
                                        :class="metrics.visitorsChange > 0 ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'"
                                        class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium">
                                        <svg :class="metrics.visitorsChange > 0 ? 'text-green-500' : 'text-red-500'"
                                            class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                            <path v-if="metrics.visitorsChange > 0" fill-rule="evenodd"
                                                d="M3.293 9.707a1 1 0 010-1.414l6-6a1 1 0 011.414 0l6 6a1 1 0 01-1.414 1.414L11 5.414V17a1 1 0 11-2 0V5.414L5.707 9.707a1 1 0 01-1.414-1.414z"
                                                clip-rule="evenodd" />
                                            <path v-else fill-rule="evenodd"
                                                d="M16.707 10.293a1 1 0 010 1.414l-6 6a1 1 0 01-1.414 0l-6-6a1 1 0 111.414-1.414L9 14.586V3a1 1 0 012 0v11.586l4.293-4.293a1 1 0 011.414 0z"
                                                clip-rule="evenodd" />
                                        </svg>
                                        {{ Math.abs(metrics.visitorsChange) }}%
                                    </span>
                                </div>
                            </div>
                            <div class="p-3 bg-blue-100 rounded-xl">
                                <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z" />
                                </svg>
                            </div>
                        </div>
                        <!-- Mini Sparkline -->
                        <div class="h-8">
                            <svg class="w-full h-full" viewBox="0 0 100 20">
                                <path :d="generateSparkline(metrics.visitorsHistory)" fill="none" stroke="#3b82f6"
                                    stroke-width="2" />
                            </svg>
                        </div>
                    </div>

                    <!-- SEO Clicks Card -->
                    <div
                        class="bg-white rounded-xl p-6 shadow-sm border border-gray-100 hover:shadow-md transition-all duration-200 relative overflow-hidden">
                        <div class="flex items-start justify-between mb-4">
                            <div>
                                <div class="flex items-center space-x-2 mb-2">
                                    <p class="text-sm font-medium text-gray-600">SEO Clicks</p>
                                    <button @mouseenter="showTooltip($event, 'Clicks from search engine results')"
                                        @mouseleave="hideTooltip" class="text-gray-400 hover:text-gray-600">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                    </button>
                                </div>
                                <p class="text-3xl font-bold text-gray-900">{{ formatNumber(metrics.seoClicks) }}</p>
                                <div class="flex items-center mt-2">
                                    <span
                                        :class="metrics.seoClicksChange > 0 ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'"
                                        class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium">
                                        <svg :class="metrics.seoClicksChange > 0 ? 'text-green-500' : 'text-red-500'"
                                            class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                            <path v-if="metrics.seoClicksChange > 0" fill-rule="evenodd"
                                                d="M3.293 9.707a1 1 0 010-1.414l6-6a1 1 0 011.414 0l6 6a1 1 0 01-1.414 1.414L11 5.414V17a1 1 0 11-2 0V5.414L5.707 9.707a1 1 0 01-1.414-1.414z"
                                                clip-rule="evenodd" />
                                            <path v-else fill-rule="evenodd"
                                                d="M16.707 10.293a1 1 0 010 1.414l-6 6a1 1 0 01-1.414 0l-6-6a1 1 0 111.414-1.414L9 14.586V3a1 1 0 012 0v11.586l4.293-4.293a1 1 0 011.414 0z"
                                                clip-rule="evenodd" />
                                        </svg>
                                        {{ Math.abs(metrics.seoClicksChange) }}%
                                    </span>
                                </div>
                            </div>
                            <div class="p-3 bg-green-100 rounded-xl">
                                <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                </svg>
                            </div>
                        </div>
                        <div class="h-8">
                            <svg class="w-full h-full" viewBox="0 0 100 20">
                                <path :d="generateSparkline(metrics.seoClicksHistory)" fill="none" stroke="#10b981"
                                    stroke-width="2" />
                            </svg>
                        </div>
                    </div>

                    <!-- Impressions Card -->
                    <div
                        class="bg-white rounded-xl p-6 shadow-sm border border-gray-100 hover:shadow-md transition-all duration-200 relative overflow-hidden">
                        <div class="flex items-start justify-between mb-4">
                            <div>
                                <div class="flex items-center space-x-2 mb-2">
                                    <p class="text-sm font-medium text-gray-600">Impressions</p>
                                    <button
                                        @mouseenter="showTooltip($event, 'Times your site appeared in search results')"
                                        @mouseleave="hideTooltip" class="text-gray-400 hover:text-gray-600">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                    </button>
                                </div>
                                <p class="text-3xl font-bold text-gray-900">{{ formatNumber(metrics.impressions) }}</p>
                                <div class="flex items-center mt-2">
                                    <span
                                        :class="metrics.impressionsChange > 0 ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'"
                                        class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium">
                                        <svg :class="metrics.impressionsChange > 0 ? 'text-green-500' : 'text-red-500'"
                                            class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                            <path v-if="metrics.impressionsChange > 0" fill-rule="evenodd"
                                                d="M3.293 9.707a1 1 0 010-1.414l6-6a1 1 0 011.414 0l6 6a1 1 0 01-1.414 1.414L11 5.414V17a1 1 0 11-2 0V5.414L5.707 9.707a1 1 0 01-1.414-1.414z"
                                                clip-rule="evenodd" />
                                            <path v-else fill-rule="evenodd"
                                                d="M16.707 10.293a1 1 0 010 1.414l-6 6a1 1 0 01-1.414 0l-6-6a1 1 0 111.414-1.414L9 14.586V3a1 1 0 012 0v11.586l4.293-4.293a1 1 0 011.414 0z"
                                                clip-rule="evenodd" />
                                        </svg>
                                        {{ Math.abs(metrics.impressionsChange) }}%
                                    </span>
                                </div>
                            </div>
                            <div class="p-3 bg-purple-100 rounded-xl">
                                <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                </svg>
                            </div>
                        </div>
                        <div class="h-8">
                            <svg class="w-full h-full" viewBox="0 0 100 20">
                                <path :d="generateSparkline(metrics.impressionsHistory)" fill="none" stroke="#8b5cf6"
                                    stroke-width="2" />
                            </svg>
                        </div>
                    </div>

                    <!-- CTR Card with Progress Ring -->
                    <div
                        class="bg-white rounded-xl p-6 shadow-sm border border-gray-100 hover:shadow-md transition-all duration-200 relative overflow-hidden">
                        <div class="flex items-start justify-between mb-4">
                            <div>
                                <div class="flex items-center space-x-2 mb-2">
                                    <p class="text-sm font-medium text-gray-600">Click-through Rate</p>
                                    <button
                                        @mouseenter="showTooltip($event, 'Percentage of impressions that resulted in clicks')"
                                        @mouseleave="hideTooltip" class="text-gray-400 hover:text-gray-600">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                    </button>
                                </div>
                                <p class="text-3xl font-bold text-gray-900">{{ metrics.ctr }}%</p>
                                <div class="flex items-center mt-2">
                                    <span
                                        :class="metrics.ctrChange > 0 ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'"
                                        class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium">
                                        <svg :class="metrics.ctrChange > 0 ? 'text-green-500' : 'text-red-500'"
                                            class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                            <path v-if="metrics.ctrChange > 0" fill-rule="evenodd"
                                                d="M3.293 9.707a1 1 0 010-1.414l6-6a1 1 0 011.414 0l6 6a1 1 0 01-1.414 1.414L11 5.414V17a1 1 0 11-2 0V5.414L5.707 9.707a1 1 0 01-1.414-1.414z"
                                                clip-rule="evenodd" />
                                            <path v-else fill-rule="evenodd"
                                                d="M16.707 10.293a1 1 0 010 1.414l-6 6a1 1 0 01-1.414 0l-6-6a1 1 0 111.414-1.414L9 14.586V3a1 1 0 012 0v11.586l4.293-4.293a1 1 0 011.414 0z"
                                                clip-rule="evenodd" />
                                        </svg>
                                        {{ Math.abs(metrics.ctrChange) }}%
                                    </span>
                                </div>
                            </div>
                            <!-- Progress Ring -->
                            <div class="relative w-16 h-16">
                                <svg class="w-16 h-16 transform -rotate-90" viewBox="0 0 64 64">
                                    <circle cx="32" cy="32" r="28" stroke="#e5e7eb" stroke-width="4" fill="none" />
                                    <circle cx="32" cy="32" r="28" stroke="#f59e0b" stroke-width="4" fill="none"
                                        :stroke-dasharray="circumference"
                                        :stroke-dashoffset="circumference - (metrics.ctr / 10) * circumference"
                                        class="transition-all duration-500" />
                                </svg>
                                <div class="absolute inset-0 flex items-center justify-center">
                                    <span class="text-xs font-bold text-orange-600">{{ metrics.ctr }}%</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Top Performing Page Card -->
                    <div
                        class="bg-white rounded-xl p-6 shadow-sm border border-gray-100 hover:shadow-md transition-all duration-200 relative overflow-hidden">
                        <div class="flex items-start justify-between mb-4">
                            <div class="flex-1">
                                <div class="flex items-center space-x-2 mb-2">
                                    <p class="text-sm font-medium text-gray-600">Top Performing Page</p>
                                    <button @mouseenter="showTooltip($event, 'Your highest traffic page this period')"
                                        @mouseleave="hideTooltip" class="text-gray-400 hover:text-gray-600">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                    </button>
                                </div>
                                <p class="text-lg font-bold text-gray-900 mb-1 truncate">{{ topPage.title }}</p>
                                <a :href="topPage.url"
                                    class="text-sm text-blue-600 hover:text-blue-800 truncate block">{{ topPage.url
                                    }}</a>
                                <div class="flex items-center mt-3 space-x-4">
                                    <div class="text-center">
                                        <p class="text-xl font-bold text-gray-900">{{ formatNumber(topPage.visitors) }}
                                        </p>
                                        <p class="text-xs text-gray-500">Visitors</p>
                                    </div>
                                    <div class="text-center">
                                        <p class="text-xl font-bold text-gray-900">{{ topPage.bounceRate }}%</p>
                                        <p class="text-xs text-gray-500">Bounce Rate</p>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>

                <!-- Enhanced Performance Table -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h3 class="text-lg font-semibold text-gray-900">Website Performance Details</h3>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Domain</th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Traffic</th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        SEO Clicks</th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Impressions</th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        CTR</th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Engagement</th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Top Country</th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Top Keyword</th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Status</th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Actions</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                <template v-for="(domain, index) in sortedDomains" :key="domain.id">
                                    <!-- Main Row -->
                                    <tr class="hover:bg-gray-50 transition-colors">
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="flex items-center space-x-2">
                                                <span class="text-sm font-medium text-gray-900">{{
                                                    domain.url }}</span>

                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="flex items-center space-x-2">
                                                <span class="text-sm font-medium text-gray-900">{{
                                                    formatNumber(domain.traffic) }}</span>
                                                <span
                                                    :class="domain.trafficChange > 0 ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'"
                                                    class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium">
                                                    {{ domain.trafficChange > 0 ? '+' : '' }}{{ domain.trafficChange }}%
                                                </span>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="flex items-center space-x-2">
                                                <span class="text-sm font-medium text-gray-900">{{
                                                    formatNumber(domain.seoClicks) }}</span>
                                                <span
                                                    :class="domain.seoClicksChange > 0 ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'"
                                                    class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium">
                                                    {{ domain.seoClicksChange > 0 ? '+' : '' }}{{ domain.seoClicksChange
                                                    }}%
                                                </span>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="text-sm text-gray-900">{{ formatNumber(domain.impressions)
                                                }}</span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="flex items-center space-x-2">
                                                <div class="relative w-8 h-8">
                                                    <svg class="w-8 h-8 transform -rotate-90" viewBox="0 0 32 32">
                                                        <circle cx="16" cy="16" r="14" stroke="#e5e7eb" stroke-width="2"
                                                            fill="none" />
                                                        <circle cx="16" cy="16" r="14" :stroke="getCTRColor(domain.ctr)"
                                                            stroke-width="2" fill="none" :stroke-dasharray="87.96"
                                                            :stroke-dashoffset="87.96 - (domain.ctr / 10) * 87.96"
                                                            class="transition-all duration-500" />
                                                    </svg>
                                                    <div class="absolute inset-0 flex items-center justify-center">
                                                        <span class="text-[10px] font-medium">{{ domain.ctr }}%</span>
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="flex items-center space-x-1">
                                                <div class="w-16 bg-gray-200 rounded-full h-2">
                                                    <div :class="getEngagementColor(domain.engagementScore)"
                                                        class="h-2 rounded-full transition-all duration-500"
                                                        :style="{ width: domain.engagementScore + '%' }"></div>
                                                </div>
                                                <span class="text-sm font-medium text-gray-900">{{
                                                    domain.engagementScore }}%</span>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="flex items-center space-x-2">
                                                <span class="text-lg">{{ domain.topCountry.flag }}</span>
                                                <div>
                                                    <div class="text-sm font-medium text-gray-900">{{
                                                        domain.topCountry.name }}</div>
                                                    <div class="text-xs text-gray-500">{{ domain.topCountry.percentage
                                                        }}% of traffic</div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div>
                                                <div class="text-sm font-medium text-gray-900">{{ domain.topKeyword.term
                                                    }}</div>
                                                <div class="text-xs text-gray-500">Pos: {{ domain.topKeyword.position }}
                                                    • Vol: {{ formatNumber(domain.topKeyword.volume) }}</div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span :class="getStatusColor(domain.status)"
                                                class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium">
                                                {{ domain.status }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                            <button @click="toggleExpanded(domain.id)"
                                                class="text-blue-600 hover:text-blue-900 mr-3">
                                                {{ expandedDomains.includes(domain.id) ? 'Collapse' : 'Expand' }}
                                            </button>
                                            <button class="text-gray-400 hover:text-gray-600">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M12 5v.01M12 12v.01M12 19v.01M12 6a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2z" />
                                                </svg>
                                            </button>
                                        </td>
                                    </tr>

                                    <!-- Expanded Row -->
                                    <tr v-if="expandedDomains.includes(domain.id)" class="bg-gray-50">
                                        <td colspan="10" class="px-6 py-6">
                                            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                                                <!-- Traffic Chart -->
                                                <div class="bg-white rounded-lg p-4 shadow-sm">
                                                    <h4 class="text-sm font-medium text-gray-900 mb-3">Traffic Over Time
                                                    </h4>
                                                    <div class="h-32">
                                                        <svg class="w-full h-full" viewBox="0 0 300 120">
                                                            <path :d="generateMiniChart(domain.trafficHistory)"
                                                                fill="none" stroke="#3b82f6" stroke-width="2" />
                                                            <defs>
                                                                <linearGradient id="areaGradient" x1="0%" y1="0%"
                                                                    x2="0%" y2="100%">
                                                                    <stop offset="0%"
                                                                        style="stop-color:#3b82f6;stop-opacity:0.3" />
                                                                    <stop offset="100%"
                                                                        style="stop-color:#3b82f6;stop-opacity:0.05" />
                                                                </linearGradient>
                                                            </defs>
                                                            <path :d="generateMiniAreaChart(domain.trafficHistory)"
                                                                fill="url(#areaGradient)" />
                                                        </svg>
                                                    </div>
                                                </div>

                                                <!-- Traffic Sources Pie -->
                                                <div class="bg-white rounded-lg p-4 shadow-sm">
                                                    <h4 class="text-sm font-medium text-gray-900 mb-3">Traffic Sources
                                                    </h4>
                                                    <div class="space-y-2">
                                                        <div v-for="source in domain.trafficSources" :key="source.name"
                                                            class="flex items-center justify-between">
                                                            <div class="flex items-center space-x-2">
                                                                <div :class="source.color" class="w-3 h-3 rounded-full">
                                                                </div>
                                                                <span class="text-sm text-gray-700">{{ source.name
                                                                    }}</span>
                                                            </div>
                                                            <span class="text-sm font-medium text-gray-900">{{
                                                                source.percentage }}%</span>
                                                        </div>
                                                    </div>
                                                </div>

                                                <!-- Top Keywords -->
                                                <div class="bg-white rounded-lg p-4 shadow-sm">
                                                    <div class="flex items-center justify-between mb-3">
                                                        <h4 class="text-sm font-medium text-gray-900">Top 3 Keywords
                                                        </h4>
                                                        <button
                                                            class="text-blue-600 hover:text-blue-800 text-sm font-medium">View
                                                            Full Report</button>
                                                    </div>
                                                    <div class="space-y-3">
                                                        <div v-for="keyword in domain.topKeywords" :key="keyword.term"
                                                            class="flex items-center justify-between">
                                                            <div>
                                                                <div class="text-sm font-medium text-gray-900">{{
                                                                    keyword.term }}</div>
                                                                <div class="text-xs text-gray-500">Position {{
                                                                    keyword.position }}</div>
                                                            </div>
                                                            <div class="text-right">
                                                                <div class="text-sm font-medium text-gray-900">{{
                                                                    formatNumber(keyword.volume) }}</div>
                                                                <div class="text-xs text-gray-500">searches/mo</div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                </template>
                            </tbody>
                        </table>
                    </div>

                    <!-- Empty State -->
                    <div v-if="domains.length === 0" class="text-center py-12">
                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                        </svg>
                        <h3 class="mt-2 text-sm font-medium text-gray-900">No data available</h3>
                        <p class="mt-1 text-sm text-gray-500">Connect Google Search Console to see more detailed
                            analytics.</p>
                        <div class="mt-6">
                            <button
                                class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700">
                                <svg class="mr-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                                </svg>
                                Connect Google Search Console
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tooltip -->
            <div v-if="tooltip.visible"
                class="fixed bg-gray-900 text-white px-3 py-2 rounded-lg text-sm shadow-xl z-50 pointer-events-none"
                :style="{ left: tooltip.x + 'px', top: tooltip.y + 'px' }">
                {{ tooltip.content }}
            </div>

            <!-- Custom Date Range Modal -->
            <div v-if="showCustomRange"
                class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50"
                @click="showCustomRange = false">
                <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white" @click.stop>
                    <div class="mt-3">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Select Custom Date Range</h3>
                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Start Date</label>
                                <input type="date" v-model="customDateRange.start"
                                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">End Date</label>
                                <input type="date" v-model="customDateRange.end"
                                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                            </div>
                        </div>
                        <div class="flex justify-end space-x-3 mt-6">
                            <button @click="showCustomRange = false"
                                class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 rounded-md hover:bg-gray-200">
                                Cancel
                            </button>
                            <button @click="applyCustomRange"
                                class="px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-md hover:bg-blue-700">
                                Apply
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>

</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';

// Reactive data
const selectedPeriod = ref('This Week')
const sortBy = ref('traffic')
const compareMode = ref(false)
const expandedDomains = ref([])
const showCustomRange = ref(false)
const customDateRange = ref({ start: '', end: '' })

const tooltip = ref({
    visible: false,
    x: 0,
    y: 0,
    content: ''
})

// Period options
const periods = [
    { label: 'Today', value: 'Today' },
    { label: 'This Week', value: 'This Week' },
    { label: 'This Month', value: 'This Month' },
    { label: 'Last 30 days', value: 'Last 30 days' },
    { label: 'Last 90 days', value: 'Last 90 days' }
]

// Sample metrics data
const metrics = ref({
    totalVisitors: 45238,
    visitorsChange: 12.5,
    visitorsHistory: [1200, 1350, 1180, 1420, 1680, 1520, 1750],
    seoClicks: 8456,
    seoClicksChange: 957.1,
    seoClicksHistory: [890, 920, 850, 980, 1200, 1100, 1350],
    impressions: 125000,
    impressionsChange: 23.4,
    impressionsHistory: [15000, 16000, 14500, 17000, 19000, 18500, 20000],
    ctr: 6.8,
    ctrChange: 15.2
})

const topPage = ref({
    title: 'Summer Collection 2024',
    url: '/collections/summer-2024',
    visitors: 12450,
    bounceRate: 23.5
})

// Domain performance data
const domains = ref([
    {
        id: 1,
        name: 'mystore.com',
        url: 'https://mystore.com',
        favicon: 'https://mystore.com/favicon.ico',
        traffic: 15420,
        trafficChange: 12.5,
        seoClicks: 3240,
        seoClicksChange: 957.1,
        impressions: 45000,
        ctr: 7.2,
        engagementScore: 84,
        topCountry: { name: 'United States', flag: '🇺🇸', percentage: 45 },
        topKeyword: { term: 'summer shoes', position: 3, volume: 12400 },
        status: 'Active',
        trafficHistory: [12000, 13000, 12500, 14000, 15000, 14800, 15420],
        trafficSources: [
            { name: 'Organic', percentage: 45, color: 'bg-green-500' },
            { name: 'Direct', percentage: 25, color: 'bg-blue-500' },
            { name: 'Social', percentage: 20, color: 'bg-purple-500' },
            { name: 'Referral', percentage: 10, color: 'bg-orange-500' }
        ],
        topKeywords: [
            { term: 'summer shoes', position: 3, volume: 12400 },
            { term: 'running sneakers', position: 7, volume: 8900 },
            { term: 'casual footwear', position: 12, volume: 5600 }
        ]
    },
    {
        id: 2,
        name: 'blog.mystore.com',
        url: 'https://blog.mystore.com',
        favicon: 'https://blog.mystore.com/favicon.ico',
        traffic: 8750,
        trafficChange: -5.2,
        seoClicks: 1890,
        seoClicksChange: 23.8,
        impressions: 28000,
        ctr: 6.8,
        engagementScore: 72,
        topCountry: { name: 'United Kingdom', flag: '🇬🇧', percentage: 32 },
        topKeyword: { term: 'fashion trends 2024', position: 5, volume: 8900 },
        status: 'Warning',
        trafficHistory: [9200, 9100, 8900, 8800, 8600, 8700, 8750],
        trafficSources: [
            { name: 'Organic', percentage: 60, color: 'bg-green-500' },
            { name: 'Social', percentage: 25, color: 'bg-purple-500' },
            { name: 'Direct', percentage: 10, color: 'bg-blue-500' },
            { name: 'Referral', percentage: 5, color: 'bg-orange-500' }
        ],
        topKeywords: [
            { term: 'fashion trends 2024', position: 5, volume: 8900 },
            { term: 'style guide', position: 8, volume: 6700 },
            { term: 'outfit ideas', position: 15, volume: 4200 }
        ]
    }
])

// Computed properties
const dateRangeText = computed(() => {
    const today = new Date()
    const formatDate = (date) => date.toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' })

    switch (selectedPeriod.value) {
        case 'Today':
            return formatDate(today)
        case 'This Week':
            const weekStart = new Date(today.setDate(today.getDate() - today.getDay()))
            const weekEnd = new Date(today.setDate(today.getDate() - today.getDay() + 6))
            return `${formatDate(weekStart)} – ${formatDate(weekEnd)}`
        case 'This Month':
            const monthStart = new Date(today.getFullYear(), today.getMonth(), 1)
            const monthEnd = new Date(today.getFullYear(), today.getMonth() + 1, 0)
            return `${formatDate(monthStart)} – ${formatDate(monthEnd)}`
        default:
            return selectedPeriod.value
    }
})

const sortedDomains = computed(() => {
    return [...domains.value].sort((a, b) => {
        switch (sortBy.value) {
            case 'traffic':
                return b.traffic - a.traffic
            case 'engagement':
                return b.engagementScore - a.engagementScore
            case 'seo':
                return b.seoClicks - a.seoClicks
            case 'ctr':
                return b.ctr - a.ctr
            default:
                return 0
        }
    })
})

const circumference = 175.93 // 2 * π * 28

// Methods
const formatNumber = (num) => {
    if (num >= 1000000) {
        return (num / 1000000).toFixed(1) + 'M'
    } else if (num >= 1000) {
        return (num / 1000).toFixed(1) + 'K'
    }
    return num.toString()
}

const generateSparkline = (data) => {
    const width = 100
    const height = 20
    const max = Math.max(...data)
    const min = Math.min(...data)
    const range = max - min || 1

    return data.map((value, index) => {
        const x = (index / (data.length - 1)) * width
        const y = height - ((value - min) / range) * height
        return `${index === 0 ? 'M' : 'L'} ${x} ${y}`
    }).join(' ')
}

const generateMiniChart = (data) => {
    const width = 300
    const height = 120
    const max = Math.max(...data)
    const min = Math.min(...data)
    const range = max - min || 1

    return data.map((value, index) => {
        const x = (index / (data.length - 1)) * width
        const y = height - ((value - min) / range) * (height - 20) - 10
        return `${index === 0 ? 'M' : 'L'} ${x} ${y}`
    }).join(' ')
}

const generateMiniAreaChart = (data) => {
    const linePath = generateMiniChart(data)
    const firstX = 0
    const lastX = 300
    const bottomY = 120

    return `M ${firstX} ${bottomY} ${linePath} L ${lastX} ${bottomY} Z`
}

const getCTRColor = (ctr) => {
    if (ctr >= 5) return '#10b981'
    if (ctr >= 3) return '#f59e0b'
    return '#ef4444'
}

const getEngagementColor = (score) => {
    if (score >= 80) return 'bg-green-500'
    if (score >= 60) return 'bg-yellow-500'
    return 'bg-red-500'
}

const getStatusColor = (status) => {
    switch (status) {
        case 'Active':
            return 'bg-green-100 text-green-800'
        case 'Warning':
            return 'bg-yellow-100 text-yellow-800'
        case 'Not Tracked':
            return 'bg-red-100 text-red-800'
        default:
            return 'bg-gray-100 text-gray-800'
    }
}

const toggleExpanded = (domainId) => {
    const index = expandedDomains.value.indexOf(domainId)
    if (index > -1) {
        expandedDomains.value.splice(index, 1)
    } else {
        expandedDomains.value.push(domainId)
    }
}

const showTooltip = (event, content) => {
    tooltip.value = {
        visible: true,
        x: event.pageX + 10,
        y: event.pageY - 10,
        content
    }
}

const hideTooltip = () => {
    tooltip.value.visible = false
}

const handleFaviconError = (event) => {
    event.target.src = 'data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iMjQiIGhlaWdodD0iMjQiIHZpZXdCb3g9IjAgMCAyNCAyNCIgZmlsbD0ibm9uZSIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj4KPHBhdGggZD0iTTEyIDJMMTMuMDkgOC4yNkwyMCA5TDEzLjA5IDE1Ljc0TDEyIDIyTDEwLjkxIDE1Ljc0TDQgOUwxMC45MSA4LjI2TDEyIDJaIiBmaWxsPSIjOUNBM0FGIi8+Cjwvc3ZnPgo='
}

const exportData = (format) => {
    console.log(`Exporting data as ${format}`)
    // Implementation would go here
}

const applyCustomRange = () => {
    console.log('Applying custom date range:', customDateRange.value)
    showCustomRange.value = false
    // Implementation would go here
}

onMounted(() => {
    // Initialize component
})
</script>

<style scoped>
@keyframes fadeIn {
    from {
        opacity: 0;
        transform: translateY(10px);
    }

    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.animate-fade-in {
    animation: fadeIn 0.5s ease-out;
}

/* Custom scrollbar */
::-webkit-scrollbar {
    width: 6px;
    height: 6px;
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

/* Hover effects */
.hover\:shadow-md:hover {
    box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
}

/* Table row hover effects */
tbody tr:hover {
    background-color: #f9fafb;
}

/* Progress ring animations */
circle {
    transition: stroke-dashoffset 0.5s ease-in-out;
}

/* Toggle switch animation */
.toggle-switch {
    transition: all 0.2s ease-in-out;
}
</style>
