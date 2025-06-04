<template>
    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-2xl font-semibold text-gray-900">Analytics</h2>
                    <p class="text-gray-500 text-sm mt-0.5">Track your website performance</p>
                </div>
                <div class="flex items-center gap-2">
                    <button class="px-4 py-2 text-sm text-gray-600 hover:text-gray-900 transition-colors">
                        Export
                    </button>
                    <button
                        class="px-4 py-2 bg-gray-900 text-white text-sm rounded-lg hover:bg-gray-800 transition-colors">
                        Add Website
                    </button>
                </div>
            </div>
        </template>

        <div v-if="loading" class="flex justify-center py-32">
            <svg class="animate-spin h-8 w-8 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none"
                viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"></path>
            </svg>
        </div>

        <div v-else class="space-y-6">
            <!-- Website Cards -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                <div v-for="site in trackedWebsites" :key="site.id" @click="selectedSite = site"
                    class="bg-white rounded-lg p-5 border cursor-pointer transition-all"
                    :class="selectedSite?.id === site.id ? 'border-gray-900 shadow-sm' : 'border-gray-100 hover:border-gray-200'">

                    <div class="flex items-start justify-between mb-4">
                        <div>
                            <h3 class="font-medium text-gray-900">{{ site.domain }}</h3>
                            <p class="text-xs text-gray-500 mt-0.5">
                                {{ new Date(site.created_at).toLocaleDateString('en-US', {
                                    month: 'short', day:
                                        'numeric'
                                }) }}
                            </p>
                        </div>
                        <div class="w-2 h-2 bg-green-500 rounded-full"></div>
                    </div>

                    <div class="grid grid-cols-2 gap-4 mb-4">
                        <div>
                            <p class="text-xs text-gray-500">Sessions</p>
                            <p class="text-lg font-semibold text-gray-900">
                                {{ (site.traffic?.total_sessions || 0).toLocaleString() }}
                            </p>
                            <p class="text-xs text-green-600 mt-0.5">↑ 12%</p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500">Clicks</p>
                            <p class="text-lg font-semibold text-gray-900">
                                {{ (site.seo?.clicks || 0).toLocaleString() }}
                            </p>
                            <p class="text-xs text-green-600 mt-0.5">↑ 8%</p>
                        </div>
                    </div>

                    <div class="h-12">
                        <Line v-if="site.traffic?.daily" :data="{
                            labels: Object.keys(site.traffic.daily).slice(-7),
                            datasets: [{
                                data: Object.values(site.traffic.daily).slice(-7),
                                borderColor: selectedSite?.id === site.id ? '#2563eb' : '#10b981',
                                borderWidth: 2,
                                tension: 0.4,
                                pointRadius: 0,
                                fill: false
                            }]
                        }" :options="{
                            plugins: { legend: { display: false }, tooltip: { enabled: false } },
                            scales: { x: { display: false }, y: { display: false } },
                            responsive: true,
                            maintainAspectRatio: false
                        }" />
                    </div>
                </div>
            </div>

            <!-- Analytics Panel -->
            <div v-if="selectedSite" class="space-y-6">
                <!-- Metrics -->
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                    <div class="bg-white rounded-lg p-4 border border-gray-100">
                        <div class="flex items-center justify-between mb-2">
                            <p class="text-sm text-gray-500">Clicks</p>
                            <span class="text-xs text-green-600">+15%</span>
                        </div>
                        <p class="text-2xl font-semibold text-gray-900">
                            {{ (selectedSite.seo?.clicks || 0).toLocaleString() }}
                        </p>
                    </div>

                    <div class="bg-white rounded-lg p-4 border border-gray-100">
                        <div class="flex items-center justify-between mb-2">
                            <p class="text-sm text-gray-500">Sessions</p>
                            <span class="text-xs text-green-600">+22%</span>
                        </div>
                        <p class="text-2xl font-semibold text-gray-900">
                            {{ (selectedSite.traffic?.total_sessions || 0).toLocaleString() }}
                        </p>
                    </div>

                    <div class="bg-white rounded-lg p-4 border border-gray-100">
                        <div class="flex items-center justify-between mb-2">
                            <p class="text-sm text-gray-500">Impressions</p>
                            <span class="text-xs text-green-600">+8%</span>
                        </div>
                        <p class="text-2xl font-semibold text-gray-900">
                            {{ (selectedSite.seo?.impressions || 0).toLocaleString() }}
                        </p>
                    </div>

                    <div class="bg-white rounded-lg p-4 border border-gray-100">
                        <div class="flex items-center justify-between mb-2">
                            <p class="text-sm text-gray-500">CTR</p>
                            <span class="text-xs text-red-600">-2%</span>
                        </div>
                        <p class="text-2xl font-semibold text-gray-900">
                            {{ calculateCTR(selectedSite) }}%
                        </p>
                    </div>
                </div>

                <!-- Charts -->
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    <!-- Traffic Chart -->
                    <div class="bg-white rounded-lg p-6 border border-gray-100">
                        <div class="flex items-center justify-between mb-6">
                            <h3 class="font-medium text-gray-900">Traffic</h3>
                            <div class="flex gap-1">
                                <button @click="trafficChartType = 'line'"
                                    :class="['p-1.5 rounded text-sm',
                                        trafficChartType === 'line' ? 'bg-gray-100 text-gray-900' : 'text-gray-400 hover:text-gray-600']">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z">
                                        </path>
                                    </svg>
                                </button>
                                <button @click="trafficChartType = 'bar'"
                                    :class="['p-1.5 rounded text-sm',
                                        trafficChartType === 'bar' ? 'bg-gray-100 text-gray-900' : 'text-gray-400 hover:text-gray-600']">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M7 12l3-3 3 3 4-4M8 21l4-4 4 4M3 4h18M4 4h16v12a1 1 0 01-1 1H5a1 1 0 01-1-1V4z">
                                        </path>
                                    </svg>
                                </button>
                            </div>
                        </div>
                        <div class="h-64">
                            <component :is="trafficChartType === 'line' ? Line : Bar" v-if="selectedSite.traffic?.daily"
                                :data="getTrafficChartData()" :options="mainChartOptions" />
                        </div>
                    </div>

                    <!-- SEO Chart -->
                    <div class="bg-white rounded-lg p-6 border border-gray-100">
                        <div class="flex items-center justify-between mb-6">
                            <h3 class="font-medium text-gray-900">SEO Performance</h3>
                            <div class="flex items-center gap-4 text-xs">
                                <span class="flex items-center gap-1">
                                    <div class="w-2 h-2 bg-blue-600 rounded-full"></div>
                                    Clicks
                                </span>
                                <span class="flex items-center gap-1">
                                    <div class="w-2 h-2 bg-gray-400 rounded-full"></div>
                                    Impressions
                                </span>
                            </div>
                        </div>
                        <div class="h-64">
                            <Line v-if="selectedSite.seo?.daily" :data="getSEOChartData()" :options="seoChartOptions" />
                        </div>
                    </div>
                </div>

                <!-- Sources and Pages -->
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    <!-- Traffic Sources -->
                    <div class="bg-white rounded-lg p-6 border border-gray-100">
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="font-medium text-gray-900">Traffic Source</h3>
                            <button @click="goToAllTrafficSource(selectedSite.id)"
                                class="text-sm text-blue-600 hover:underline hover:text-blue-800 transition-colors underline">
                                View All
                            </button>
                        </div>
                        <div class="space-y-3">
                            <div v-for="([source, value], index) in Object.entries(selectedSite.traffic?.sources || {}).slice(0, 5)"
                                :key="source" class="flex items-center justify-between">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 bg-gray-50 rounded-lg flex items-center justify-center">
                                        <i :class="getSourceIcon(source)" class="text-sm"></i>
                                    </div>
                                    <div>
                                        <p class="text-sm font-medium text-gray-900">{{ source }}</p>
                                        <p class="text-xs text-gray-500">{{ getPercentage(value,
                                            selectedSite.traffic?.total_sessions) }}%</p>
                                    </div>
                                </div>
                                <p class="text-sm font-medium text-gray-900">{{ value.toLocaleString() }}</p>
                            </div>
                        </div>
                    </div>


                    <!-- Top Pages -->
                    <div class="bg-white rounded-lg p-6 border border-gray-100">
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="font-medium text-gray-900">Top Pages</h3>
                            <button @click="goToAllPages(selectedSite.id)"
                                class="text-sm text-blue-600 hover:underline hover:text-blue-800 transition-colors underline">
                                View All
                            </button>
                        </div>
                        <div class="space-y-3">
                            <div v-for="(top, index) in selectedSite.traffic?.topPages.slice(0, 5)" :key="index">
                                <div class="flex justify-between items-center mb-1">
                                    <p class="text-sm text-gray-700 truncate pr-2" :title="top.page">
                                        {{ top.page }}
                                    </p>
                                    <span class="text-sm font-medium text-gray-900">{{ top.views }}</span>
                                </div>
                                <div class="w-full bg-gray-100 rounded-full h-1.5">
                                    <div class="bg-blue-600 h-1.5 rounded-full transition-all duration-500"
                                        :style="{ width: `${(top.views / selectedSite.traffic?.topPages[0].views) * 100}%` }">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>


                <!-- Sources and Pages -->
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    <!-- Traffic Sources -->
                    <div class="bg-white rounded-lg p-6 border border-gray-100">
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="font-medium text-gray-900">Traffic Source</h3>
                            <button @click="goToAllTrafficSource(selectedSite.id)"
                                class="text-sm text-blue-600 hover:underline hover:text-blue-800 transition-colors underline">
                                View All
                            </button>
                        </div>
                        <div class="space-y-3">
                            <div v-for="([source, value], index) in Object.entries(selectedSite.traffic?.sources || {}).slice(0, 5)"
                                :key="source" class="flex items-center justify-between">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 bg-gray-50 rounded-lg flex items-center justify-center">
                                        <i :class="getSourceIcon(source)" class="text-sm"></i>
                                    </div>
                                    <div>
                                        <p class="text-sm font-medium text-gray-900">{{ source }}</p>
                                        <p class="text-xs text-gray-500">{{ getPercentage(value,
                                            selectedSite.traffic?.total_sessions) }}%</p>
                                    </div>
                                </div>
                                <p class="text-sm font-medium text-gray-900">{{ value.toLocaleString() }}</p>
                            </div>
                        </div>
                    </div>


                    <!-- Top Pages -->
                    <div class="bg-white rounded-lg p-6 border border-gray-100">
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="font-medium text-gray-900">Top Pages</h3>
                            <button @click="goToAllPages(selectedSite.id)"
                                class="text-sm text-blue-600 hover:underline hover:text-blue-800 transition-colors underline">
                                View All
                            </button>
                        </div>
                        <div class="space-y-3">
                            <div v-for="(top, index) in selectedSite.traffic?.topPages.slice(0, 5)" :key="index">
                                <div class="flex justify-between items-center mb-1">
                                    <p class="text-sm text-gray-700 truncate pr-2" :title="top.page">
                                        {{ top.page }}
                                    </p>
                                    <span class="text-sm font-medium text-gray-900">{{ top.views }}</span>
                                </div>
                                <div class="w-full bg-gray-100 rounded-full h-1.5">
                                    <div class="bg-blue-600 h-1.5 rounded-full transition-all duration-500"
                                        :style="{ width: `${(top.views / selectedSite.traffic?.topPages[0].views) * 100}%` }">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>


                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    <!-- Performance Metrics Card -->
                    <div class="bg-white rounded-lg p-6 border border-gray-100">
                        <div class="flex items-center justify-between mb-6">
                            <h3 class="font-medium text-gray-900">Performance Metrics</h3>
                            <span class="px-2 py-1 bg-gray-100 text-gray-700 text-xs font-medium rounded">
                                Grade {{ selectedSite.domainPerformance?.gtmetrix_grade || 'N/A' }}
                            </span>
                        </div>

                        <div class="space-y-4">
                            <!-- Performance Score -->
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 bg-gray-100 rounded-lg flex items-center justify-center">
                                        <svg class="w-4 h-4 text-gray-600" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M13 10V3L4 14h7v7l9-11h-7z" />
                                        </svg>
                                    </div>
                                    <div>
                                        <p class="text-sm font-medium text-gray-900">Performance Score</p>
                                        <p class="text-xs text-gray-500">Overall site speed</p>
                                    </div>
                                </div>
                                <div class="text-right">
                                    <p class="text-lg font-semibold text-gray-900">
                                        {{ selectedSite.domainPerformance?.performance_score?.toFixed(0) || 0 }}/100
                                    </p>
                                    <div class="w-16 bg-gray-200 rounded-full h-1.5 mt-1">
                                        <div class="bg-gray-900 h-1.5 rounded-full transition-all duration-500"
                                            :style="{ width: `${selectedSite.domainPerformance?.performance_score || 0}%` }">
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Load Time -->
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 bg-gray-100 rounded-lg flex items-center justify-center">
                                        <svg class="w-4 h-4 text-gray-600" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                    </div>
                                    <div>
                                        <p class="text-sm font-medium text-gray-900">Load Time</p>
                                        <p class="text-xs text-gray-500">Time to first byte</p>
                                    </div>
                                </div>
                                <p class="text-lg font-semibold text-gray-900">
                                    {{ selectedSite.domainPerformance?.load_time_seconds || 0 }}s
                                </p>
                            </div>

                            <!-- Content Size -->
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 bg-gray-100 rounded-lg flex items-center justify-center">
                                        <svg class="w-4 h-4 text-gray-600" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                                        </svg>
                                    </div>
                                    <div>
                                        <p class="text-sm font-medium text-gray-900">Content Size</p>
                                        <p class="text-xs text-gray-500">Total page weight</p>
                                    </div>
                                </div>
                                <p class="text-lg font-semibold text-gray-900">
                                    {{ formatBytes(selectedSite.domainPerformance?.content_length) }}
                                </p>
                            </div>

                            <!-- Structure Score -->
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 bg-gray-100 rounded-lg flex items-center justify-center">
                                        <svg class="w-4 h-4 text-gray-600" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                                        </svg>
                                    </div>
                                    <div>
                                        <p class="text-sm font-medium text-gray-900">Structure Score</p>
                                        <p class="text-xs text-gray-500">HTML structure quality</p>
                                    </div>
                                </div>
                                <div class="text-right">
                                    <p class="text-lg font-semibold text-gray-900">
                                        {{ selectedSite.domainPerformance?.structure_score?.toFixed(0) || 0 }}/100
                                    </p>
                                    <div class="w-16 bg-gray-200 rounded-full h-1.5 mt-1">
                                        <div class="bg-gray-900 h-1.5 rounded-full transition-all duration-500"
                                            :style="{ width: `${selectedSite.domainPerformance?.structure_score || 0}%` }">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Web Vitals Card -->
                    <div class="bg-white rounded-lg p-6 border border-gray-100">
                        <div class="flex items-center justify-between mb-6">
                            <h3 class="font-medium text-gray-900">Core Web Vitals</h3>
                            <span class="px-2 py-1 bg-gray-100 text-gray-700 text-xs font-medium rounded">
                                Google Metrics
                            </span>
                        </div>

                        <div class="space-y-4">
                            <!-- Largest Contentful Paint -->
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 bg-gray-100 rounded-lg flex items-center justify-center">
                                        <span class="text-xs font-bold text-gray-600">LCP</span>
                                    </div>
                                    <div>
                                        <p class="text-sm font-medium text-gray-900">Largest Contentful Paint</p>
                                        <p class="text-xs text-gray-500">Loading performance</p>
                                    </div>
                                </div>
                                <p class="text-lg font-semibold text-gray-900">
                                    {{ selectedSite.domainPerformance?.web_vitals?.largest_contentful_paint || 'N/A' }}
                                </p>
                            </div>

                            <!-- Total Blocking Time -->
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 bg-gray-100 rounded-lg flex items-center justify-center">
                                        <span class="text-xs font-bold text-gray-600">TBT</span>
                                    </div>
                                    <div>
                                        <p class="text-sm font-medium text-gray-900">Total Blocking Time</p>
                                        <p class="text-xs text-gray-500">Interactivity delay</p>
                                    </div>
                                </div>
                                <p class="text-lg font-semibold text-gray-900">
                                    {{ selectedSite.domainPerformance?.web_vitals?.total_blocking_time || 'N/A' }}
                                </p>
                            </div>

                            <!-- Cumulative Layout Shift -->
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 bg-gray-100 rounded-lg flex items-center justify-center">
                                        <span class="text-xs font-bold text-gray-600">CLS</span>
                                    </div>
                                    <div>
                                        <p class="text-sm font-medium text-gray-900">Cumulative Layout Shift</p>
                                        <p class="text-xs text-gray-500">Visual stability</p>
                                    </div>
                                </div>
                                <p class="text-lg font-semibold text-gray-900">
                                    {{ selectedSite.domainPerformance?.web_vitals?.cumulative_layout_shift || 'N/A' }}
                                </p>
                            </div>

                            <!-- Readability Score -->
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 bg-gray-100 rounded-lg flex items-center justify-center">
                                        <svg class="w-4 h-4 text-gray-600" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                                        </svg>
                                    </div>
                                    <div>
                                        <p class="text-sm font-medium text-gray-900">Readability Score</p>
                                        <p class="text-xs text-gray-500">Content clarity</p>
                                    </div>
                                </div>
                                <div class="text-right">
                                    <p class="text-lg font-semibold text-gray-900">
                                        {{ selectedSite.domainPerformance?.readability_score?.toFixed(1) || 0 }}/10
                                    </p>
                                    <div class="w-16 bg-gray-200 rounded-full h-1.5 mt-1">
                                        <div class="bg-gray-900 h-1.5 rounded-full transition-all duration-500"
                                            :style="{ width: `${(selectedSite.domainPerformance?.readability_score || 0) * 10}%` }">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Technology Stack Card -->
                    <div class="bg-white rounded-lg p-6 border border-gray-100">
                        <div class="flex items-center justify-between mb-6">
                            <h3 class="font-medium text-gray-900">Technology Stack</h3>
                            <span class="px-2 py-1 bg-gray-100 text-gray-700 text-xs font-medium rounded">
                                Detected
                            </span>
                        </div>

                        <div class="space-y-4">
                            <!-- CMS -->
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 bg-gray-100 rounded-lg flex items-center justify-center">
                                        <svg class="w-4 h-4 text-gray-600" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                        </svg>
                                    </div>
                                    <div>
                                        <p class="text-sm font-medium text-gray-900">Content Management</p>
                                        <p class="text-xs text-gray-500">Platform detected</p>
                                    </div>
                                </div>
                                <p class="text-sm font-medium text-gray-900">
                                    {{ selectedSite.domainPerformance?.technologies?.cms || 'Unknown' }}
                                </p>
                            </div>

                            <!-- Theme -->
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 bg-gray-100 rounded-lg flex items-center justify-center">
                                        <svg class="w-4 h-4 text-gray-600" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zm0 0h12a2 2 0 002-2v-4a2 2 0 00-2-2h-2.343M11 7.343l1.657-1.657a2 2 0 012.828 0l2.829 2.829a2 2 0 010 2.828l-8.486 8.485M7 17h4a2 2 0 002-2V9a2 2 0 00-2-2H7a2 2 0 00-2 2v6a2 2 0 002 2z" />
                                        </svg>
                                    </div>
                                    <div>
                                        <p class="text-sm font-medium text-gray-900">Theme</p>
                                        <p class="text-xs text-gray-500">Design framework</p>
                                    </div>
                                </div>
                                <p class="text-sm font-medium text-gray-900">
                                    {{ selectedSite.domainPerformance?.technologies?.theme || 'Unknown' }}
                                </p>
                            </div>

                            <!-- Server -->
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 bg-gray-100 rounded-lg flex items-center justify-center">
                                        <svg class="w-4 h-4 text-gray-600" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M5 12h14M5 12a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v4a2 2 0 01-2 2M5 12a2 2 0 00-2 2v4a2 2 0 002 2h14a2 2 0 002-2v-4a2 2 0 00-2-2m-2-4h.01M17 16h.01" />
                                        </svg>
                                    </div>
                                    <div>
                                        <p class="text-sm font-medium text-gray-900">Server</p>
                                        <p class="text-xs text-gray-500">Hosting infrastructure</p>
                                    </div>
                                </div>
                                <p class="text-sm font-medium text-gray-900">
                                    {{ selectedSite.domainPerformance?.technologies?.server || 'Unknown' }}
                                </p>
                            </div>

                            <!-- Analytics -->
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 bg-gray-100 rounded-lg flex items-center justify-center">
                                        <svg class="w-4 h-4 text-gray-600" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                                        </svg>
                                    </div>
                                    <div>
                                        <p class="text-sm font-medium text-gray-900">Analytics</p>
                                        <p class="text-xs text-gray-500">Tracking tools</p>
                                    </div>
                                </div>
                                <p class="text-sm font-medium text-gray-900">
                                    {{ selectedSite.domainPerformance?.technologies?.analytics?.join(', ') || 'None' }}
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Site Elements Card -->
                    <div class="bg-white rounded-lg p-6 border border-gray-100">
                        <div class="flex items-center justify-between mb-6">
                            <h3 class="font-medium text-gray-900">Site Elements</h3>
                            <span class="px-2 py-1 bg-gray-100 text-gray-700 text-xs font-medium rounded">
                                HTML Analysis
                            </span>
                        </div>

                        <div class="space-y-4">
                            <!-- Images -->
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 bg-gray-100 rounded-lg flex items-center justify-center">
                                        <svg class="w-4 h-4 text-gray-600" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                        </svg>
                                    </div>
                                    <div>
                                        <p class="text-sm font-medium text-gray-900">Images</p>
                                        <p class="text-xs text-gray-500">Total image count</p>
                                    </div>
                                </div>
                                <p class="text-lg font-semibold text-gray-900">
                                    {{ selectedSite.domainPerformance?.html_elements?.images || 0 }}
                                </p>
                            </div>

                            <!-- Scripts -->
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 bg-gray-100 rounded-lg flex items-center justify-center">
                                        <svg class="w-4 h-4 text-gray-600" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4" />
                                        </svg>
                                    </div>
                                    <div>
                                        <p class="text-sm font-medium text-gray-900">Scripts</p>
                                        <p class="text-xs text-gray-500">JavaScript files</p>
                                    </div>
                                </div>
                                <p class="text-lg font-semibold text-gray-900">
                                    {{ selectedSite.domainPerformance?.html_elements?.scripts || 0 }}
                                </p>
                            </div>

                            <!-- Links -->
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 bg-gray-100 rounded-lg flex items-center justify-center">
                                        <svg class="w-4 h-4 text-gray-600" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1" />
                                        </svg>
                                    </div>
                                    <div>
                                        <p class="text-sm font-medium text-gray-900">Links</p>
                                        <p class="text-xs text-gray-500">Internal & external</p>
                                    </div>
                                </div>
                                <p class="text-lg font-semibold text-gray-900">
                                    {{ selectedSite.domainPerformance?.html_elements?.links || 0 }}
                                </p>
                            </div>

                            <!-- Performance Summary -->
                            <div class="pt-2 border-t border-gray-100">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center gap-3">
                                        <div class="w-8 h-8 bg-gray-100 rounded-lg flex items-center justify-center">
                                            <svg class="w-4 h-4 text-gray-600" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                            </svg>
                                        </div>
                                        <div>
                                            <p class="text-sm font-medium text-gray-900">Overall Health</p>
                                            <p class="text-xs text-gray-500">Performance rating</p>
                                        </div>
                                    </div>
                                    <div class="text-right">
                                        <p class="text-sm font-medium"
                                            :class="getHealthColor(selectedSite.domainPerformance?.performance_score)">
                                            {{ getHealthStatus(selectedSite.domainPerformance?.performance_score) }}
                                        </p>
                                        <p class="text-xs text-gray-500">
                                            {{ selectedSite.domainPerformance?.performance_score?.toFixed(0) || 0 }}/100
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Empty State -->
            <div v-if="!selectedSite && trackedWebsites.length === 0" class="text-center py-16">
                <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z">
                        </path>
                    </svg>
                </div>
                <h3 class="font-medium text-gray-900 mb-1">No websites tracked</h3>
                <p class="text-sm text-gray-500 mb-4">Start monitoring your website performance</p>
                <button class="px-4 py-2 bg-blue-600 text-white text-sm rounded-lg hover:bg-gray-800 transition-colors">
                    Add Your First Website
                </button>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<script setup>
import { ref, onMounted, computed, watch } from 'vue';
import axios from 'axios';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Line, Bar } from 'vue-chartjs';
import {
    Chart as ChartJS,
    Title,
    Tooltip,
    Legend,
    LineElement,
    BarElement,
    CategoryScale,
    LinearScale,
    PointElement,
    Filler
} from 'chart.js';

ChartJS.register(
    Title,
    Tooltip,
    Legend,
    LineElement,
    BarElement,
    CategoryScale,
    LinearScale,
    PointElement,
    Filler
);

const trackedWebsites = ref([]);
const selectedSite = ref(null);
const loading = ref(true);
const chartDataLoading = ref(false);
const selectedTimeRange = ref('14D');
const trafficChartType = ref('line');

const timeRanges = [
    { label: '7D', value: '7D' },
    { label: '14D', value: '14D' },
    { label: '30D', value: '30D' },
    { label: '90D', value: '90D' }
];





// Chart Options
const mainChartOptions = {
    responsive: true,
    maintainAspectRatio: false,
    plugins: {
        legend: { display: false },
        tooltip: {
            backgroundColor: 'rgba(17, 24, 39, 0.95)',
            titleColor: 'white',
            bodyColor: 'white',
            padding: 8,
            borderWidth: 0,
            displayColors: false,
            titleFont: { size: 12 },
            bodyFont: { size: 12 },
            callbacks: {
                label: (context) => `${context.parsed.y.toLocaleString()} sessions`
            }
        }
    },
    scales: {
        x: {
            grid: { display: false },
            border: { display: false },
            ticks: {
                color: '#6b7280',
                font: { size: 11 }
            }
        },
        y: {
            beginAtZero: true,
            grid: {
                color: 'rgba(0, 0, 0, 0.05)',
                drawBorder: false
            },
            border: { display: false },
            ticks: {
                color: '#6b7280',
                font: { size: 11 },
                callback: (value) => value.toLocaleString()
            }
        }
    },
    elements: {
        line: { tension: 0.3 },
        point: { radius: 0, hoverRadius: 4 },
        bar: { borderRadius: 4 }
    }
};

const seoChartOptions = {
    ...mainChartOptions,
    plugins: {
        ...mainChartOptions.plugins,
        tooltip: {
            ...mainChartOptions.plugins.tooltip,
            callbacks: {
                label: (context) => {
                    const label = context.dataset.label;
                    const value = context.parsed.y.toLocaleString();
                    return `${label}: ${value}`;
                }
            }
        }
    }
};

// Helper Functions
const getSourceIcon = (source) => {
    source = source.toLowerCase();
    if (source.includes('google')) return 'fa-brands fa-google';
    if (source.includes('facebook')) return 'fa-brands fa-facebook';
    if (source.includes('twitter')) return 'fa-brands fa-twitter';
    if (source.includes('linkedin')) return 'fa-brands fa-linkedin';
    return 'fa-solid fa-link';
};

const calculateCTR = (site) => {
    if (!site.seo?.clicks || !site.seo?.impressions) return '0.0';
    return ((site.seo.clicks / site.seo.impressions) * 100).toFixed(1);
};

const getPercentage = (value, total) => {
    if (!total) return 0;
    return ((value / total) * 100).toFixed(1);
};

// Chart Data Functions
const getTrafficChartData = () => {
    const data = selectedSite.value.traffic?.daily || {};
    const labels = Object.keys(data);
    const values = Object.values(data);

    return {
        labels: labels.map(date => {
            const d = new Date(date);
            return d.toLocaleDateString('en-US', { month: 'short', day: 'numeric' });
        }),
        datasets: [{
            label: 'Sessions',
            data: values,
            borderColor: '#2563eb',
            backgroundColor: trafficChartType.value === 'line' ? 'transparent' : 'rgba(37, 99, 235, 0.8)',
            borderWidth: 2,
            fill: false
        }]
    };
};

const getSEOChartData = () => {
    const data = selectedSite.value.seo?.daily || {};
    const labels = Object.keys(data);

    return {
        labels: labels.map(date => {
            const d = new Date(date);
            return d.toLocaleDateString('en-US', { month: 'short', day: 'numeric' });
        }),
        datasets: [
            {
                label: 'Clicks',
                data: Object.values(data).map(d => d.clicks),
                borderColor: '#2563eb',
                backgroundColor: 'transparent',
                borderWidth: 2,
                fill: false
            },
            {
                label: 'Impressions',
                data: Object.values(data).map(d => d.impressions),
                borderColor: '#9ca3af',
                backgroundColor: 'transparent',
                borderWidth: 2,
                fill: false,
                borderDash: [5, 5]
            }
        ]
    };
};

// API Functions
const fetchTrackedWebsites = async () => {
    loading.value = true;
    try {
        // Simulate API delay for realistic loading
        await new Promise(resolve => setTimeout(resolve, 1000));

        const response = await axios.get('/websites');
        trackedWebsites.value = response.data.websites;

        // Add sample device data
        trackedWebsites.value = trackedWebsites.value.map(site => ({
            ...site,
            traffic: {
                ...site.traffic,
                devices: {
                    desktop: Math.floor(Math.random() * 1000) + 500,
                    mobile: Math.floor(Math.random() * 800) + 300,
                    tablet: Math.floor(Math.random() * 300) + 100
                }
            }
        }));

        if (trackedWebsites.value.length > 0) {
            selectedSite.value = trackedWebsites.value[0];
        }
    } catch (error) {
        console.error('Error fetching websites:', error);
    } finally {
        loading.value = false;
    }
};

// Watch for site changes to simulate chart loading
watch(selectedSite, async (newSite) => {
    if (newSite) {
        chartDataLoading.value = true;
        await new Promise(resolve => setTimeout(resolve, 500));
        chartDataLoading.value = false;
    }
});

const formatBytes = (bytes) => {
    if (!bytes) return '0 B';
    const k = 1024;
    const sizes = ['B', 'KB', 'MB', 'GB'];
    const i = Math.floor(Math.log(bytes) / Math.log(k));
    return parseFloat((bytes / Math.pow(k, i)).toFixed(1)) + ' ' + sizes[i];
};

const getHealthStatus = (score) => {
    if (!score) return 'Unknown';
    if (score >= 90) return 'Excellent';
    if (score >= 75) return 'Good';
    if (score >= 50) return 'Fair';
    return 'Needs Work';
};

const getHealthColor = (score) => {
    if (!score) return 'text-gray-500';
    if (score >= 90) return 'text-green-600';
    if (score >= 75) return 'text-blue-600';
    if (score >= 50) return 'text-yellow-600';
    return 'text-red-600';
};


onMounted(fetchTrackedWebsites);
</script>

<style scoped>
/* Minimal custom styles */
select {
    background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 20 20'%3e%3cpath stroke='%236b7280' stroke-linecap='round' stroke-linejoin='round' stroke-width='1.5' d='M6 8l4 4 4-4'/%3e%3c/svg%3e");
    background-position: right 0.5rem center;
    background-repeat: no-repeat;
    background-size: 1.5em 1.5em;
    padding-right: 2.5rem;
}

/* Loading animation */
@keyframes spin {
    from {
        transform: rotate(0deg);
    }

    to {
        transform: rotate(360deg);
    }
}

.animate-spin {
    animation: spin 1s linear infinite;
}

/* Skeleton animation */
@keyframes pulse {

    0%,
    100% {
        opacity: 1;
    }

    50% {
        opacity: 0.5;
    }
}

.animate-pulse {
    animation: pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
}
</style>
