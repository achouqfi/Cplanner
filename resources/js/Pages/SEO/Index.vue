<template>
    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-semibold text-gray-900">SEO Analytics</h1>
                    <p class="text-gray-500 text-sm mt-1">Monitor and optimize your website's search engine performance
                    </p>
                </div>
                <div class="flex items-center gap-3">
                    <button class="px-4 py-2 text-sm text-gray-600 hover:text-gray-900 transition-colors">
                        Export Report
                    </button>
                    <button @click="refreshAllData"
                        class="px-4 py-2 bg-blue-600 text-white text-sm rounded-lg hover:bg-blue-700 transition-colors">
                        Refresh Data
                    </button>
                    <button
                        class="px-4 py-2 bg-gray-900 text-white text-sm rounded-lg hover:bg-gray-800 transition-colors">
                        Request Indexing
                    </button>
                </div>
            </div>
        </template>

        <div v-if="loading" class="flex justify-center items-center py-20">
            <div class="w-6 h-6 border-2 border-gray-300 border-t-gray-900 rounded-full animate-spin"></div>
        </div>

        <div v-else class="space-y-8">
            <!-- Enhanced SEO Overview Cards -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                <div class="bg-white rounded-lg p-6 border border-gray-200">
                    <div class="flex items-center justify-between mb-2">
                        <div class="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center">
                            <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <span class="text-xs text-green-600 bg-green-100 px-2 py-1 rounded-lg">+12%</span>
                    </div>
                    <p class="text-sm text-gray-500 mb-1">Indexed Pages</p>
                    <p class="text-2xl font-semibold text-gray-900">{{ getIndexedCount() }}</p>
                    <p class="text-xs text-gray-400 mt-1">of {{ indexations.data.length }} total</p>
                </div>

                <div class="bg-white rounded-lg p-6 border border-gray-200">
                    <div class="flex items-center justify-between mb-2">
                        <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center">
                            <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                            </svg>
                        </div>
                        <span class="text-xs text-blue-600 bg-blue-100 px-2 py-1 rounded-lg">+8.3%</span>
                    </div>
                    <p class="text-sm text-gray-500 mb-1">Total Impressions</p>
                    <p class="text-2xl font-semibold text-gray-900">{{ getTotalImpressions().toLocaleString() }}</p>
                    <p class="text-xs text-gray-400 mt-1">last 30 days</p>
                </div>

                <div class="bg-white rounded-lg p-6 border border-gray-200">
                    <div class="flex items-center justify-between mb-2">
                        <div class="w-10 h-10 bg-purple-100 rounded-lg flex items-center justify-center">
                            <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M13 9l3 3m0 0l-3 3m3-3H8m13 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <span class="text-xs text-purple-600 bg-purple-100 px-2 py-1 rounded-lg">{{ getAvgCTR() }}%</span>
                    </div>
                    <p class="text-sm text-gray-500 mb-1">Total Clicks</p>
                    <p class="text-2xl font-semibold text-gray-900">{{ getTotalClicks().toLocaleString() }}</p>
                    <p class="text-xs text-gray-400 mt-1">{{ getAvgCTR() }}% CTR</p>
                </div>

                <div class="bg-white rounded-lg p-6 border border-gray-200">
                    <div class="flex items-center justify-between mb-2">
                        <div class="w-10 h-10 bg-yellow-100 rounded-lg flex items-center justify-center">
                            <svg class="w-5 h-5 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
                            </svg>
                        </div>
                        <span class="text-xs text-yellow-600 bg-yellow-100 px-2 py-1 rounded-lg">Pos {{ getAvgPosition() }}</span>
                    </div>
                    <p class="text-sm text-gray-500 mb-1">Avg SEO Score</p>
                    <p class="text-2xl font-semibold text-gray-900">{{ getAvgSEOScore() }}/100</p>
                    <p class="text-xs text-gray-400 mt-1">avg position {{ getAvgPosition() }}</p>
                </div>
            </div>

            <!-- Search Console Summary -->
            <div class="bg-white rounded-lg p-6 border border-gray-200">
                <h2 class="text-lg font-medium text-gray-900 mb-4">Search Console Performance</h2>
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    <div class="text-center">
                        <div class="text-2xl font-bold text-blue-600">{{ getTotalClicks().toLocaleString() }}</div>
                        <div class="text-sm text-gray-500">Total Clicks</div>
                        <div class="text-xs text-green-600">+15.3% vs last period</div>
                    </div>
                    <div class="text-center">
                        <div class="text-2xl font-bold text-purple-600">{{ getTotalImpressions().toLocaleString() }}</div>
                        <div class="text-sm text-gray-500">Total Impressions</div>
                        <div class="text-xs text-green-600">+8.7% vs last period</div>
                    </div>
                    <div class="text-center">
                        <div class="text-2xl font-bold text-orange-600">{{ getAvgCTR() }}%</div>
                        <div class="text-sm text-gray-500">Average CTR</div>
                        <div class="text-xs text-green-600">+2.1% vs last period</div>
                    </div>
                    <div class="text-center">
                        <div class="text-2xl font-bold text-red-600">{{ getAvgPosition() }}</div>
                        <div class="text-sm text-gray-500">Average Position</div>
                        <div class="text-xs text-green-600">-1.2 vs last period</div>
                    </div>
                </div>
            </div>

            <!-- Filters and Search -->
            <div class="bg-white rounded-lg p-6 border border-gray-200">
                <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                    <div class="flex items-center gap-4">
                        <div class="relative">
                            <svg class="absolute left-3 top-1/2 transform -translate-y-1/2 w-4 h-4 text-gray-400"
                                fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                            <input v-model="searchQuery" type="text" placeholder="Search URLs..."
                                class="pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        </div>
                        <select v-model="statusFilter"
                            class="px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="">All Status</option>
                            <option value="indexed">Indexed</option>
                            <option value="not-indexed">Not Indexed</option>
                            <option value="crawled">Crawled</option>
                            <option value="blocked">Blocked</option>
                        </select>
                        <select v-model="verdictFilter"
                            class="px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="">All Issues</option>
                            <option value="valid">No Issues</option>
                            <option value="warning">Warnings</option>
                            <option value="error">Errors</option>
                        </select>
                        <select v-model="nicheFilter"
                            class="px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="">All Niches</option>
                            <option value="food">Food & Recipe</option>
                            <option value="tech">Technology</option>
                            <option value="health">Health</option>
                            <option value="lifestyle">Lifestyle</option>
                        </select>
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="text-sm text-gray-500">{{ filteredIndexations.length }} pages</span>
                    </div>
                </div>
            </div>

            <!-- Enhanced SEO Pages Table -->
            <div class="bg-white rounded-lg border border-gray-200 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                    <h2 class="text-lg font-medium text-gray-900">Page Performance Analysis</h2>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    URL & Niche
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Index Status
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Search Performance
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    SEO Quality
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Schema Status
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Actions
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <tr v-for="item in paginatedIndexations" :key="item.id"
                                class="hover:bg-gray-50 transition-colors">
                                <td class="px-6 py-4">
                                    <div class="flex items-center">
                                        <div
                                            class="flex-shrink-0 w-8 h-8 bg-blue-100 rounded-lg flex items-center justify-center mr-3">
                                            <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1" />
                                            </svg>
                                        </div>
                                        <div>
                                            <p class="text-sm font-medium text-gray-900 truncate"
                                                style="max-width: 250px;" :title="item.url">
                                                {{ item.url }}
                                            </p>
                                            <div class="flex items-center gap-2 mt-1">
                                                <span :class="getNicheBadgeClass(item.detected_niche)"
                                                    class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium">
                                                    {{ item.detected_niche }}
                                                </span>
                                                <span class="text-xs text-gray-500">{{ getPageType(item.url) }}</span>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="space-y-1">
                                        <span :class="getStatusBadgeClass(item.status)"
                                            class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium">
                                            {{ item.status }}
                                        </span>
                                        <div class="flex items-center">
                                            <div :class="getIndexingIconClass(item.indexing_state)"
                                                class="w-2 h-2 rounded-full mr-2"></div>
                                            <span class="text-xs text-gray-600">{{ item.indexing_state }}</span>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="space-y-1">
                                        <div class="flex items-center justify-between">
                                            <span class="text-xs text-gray-500">Clicks:</span>
                                            <span class="text-sm font-medium text-gray-900">{{ item.search_data.clicks }}</span>
                                        </div>
                                        <div class="flex items-center justify-between">
                                            <span class="text-xs text-gray-500">Impressions:</span>
                                            <span class="text-sm text-gray-700">{{ item.search_data.impressions }}</span>
                                        </div>
                                        <div class="flex items-center justify-between">
                                            <span class="text-xs text-gray-500">CTR:</span>
                                            <span class="text-sm text-blue-600">{{ item.search_data.ctr }}%</span>
                                        </div>
                                        <div class="flex items-center justify-between">
                                            <span class="text-xs text-gray-500">Position:</span>
                                            <span class="text-sm font-medium" :class="item.search_data.position <= 10 ? 'text-green-600' : 'text-yellow-600'">
                                                {{ item.search_data.position }}
                                            </span>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="space-y-2">
                                        <div class="flex items-center justify-between">
                                            <span class="text-xs text-gray-500">SEO Score:</span>
                                            <div class="flex items-center">
                                                <div class="w-12 h-2 bg-gray-200 rounded-full mr-2">
                                                    <div class="h-2 rounded-full"
                                                        :class="getSEOScoreColor(item.seo_quality.score)"
                                                        :style="`width: ${item.seo_quality.score}%`"></div>
                                                </div>
                                                <span class="text-sm font-medium">{{ item.seo_quality.score }}</span>
                                            </div>
                                        </div>
                                        <div class="flex items-center">
                                            <span :class="getVerdictBadgeClass(item.verdict)"
                                                class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium">
                                                {{ getVerdictText(item.verdict) }}
                                            </span>
                                            <button v-if="item.verdict !== 'valid'" @click="showIssueDetails(item)"
                                                class="ml-2 text-xs text-blue-600 hover:text-blue-800">
                                                {{ item.seo_quality.issues.length }} issues
                                            </button>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="space-y-1">
                                        <div class="flex items-center">
                                            <svg :class="item.json_ld.valid ? 'text-green-500' : 'text-red-500'"
                                                class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                <path v-if="item.json_ld.valid" fill-rule="evenodd"
                                                    d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                                    clip-rule="evenodd" />
                                                <path v-else fill-rule="evenodd"
                                                    d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z"
                                                    clip-rule="evenodd" />
                                            </svg>
                                            <span class="text-xs" :class="item.json_ld.valid ? 'text-green-600' : 'text-red-600'">
                                                {{ item.json_ld.valid ? 'Valid' : 'Invalid' }}
                                            </span>
                                        </div>
                                        <div class="text-xs text-gray-500">
                                            Type: {{ item.json_ld.type || 'None' }}
                                        </div>
                                        <div v-if="!item.json_ld.valid" class="text-xs text-red-600">
                                            {{ item.json_ld.errors.length }} errors
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex flex-col gap-2">
                                        <button @click="fetchReport(item.url)"
                                            class="text-blue-600 hover:text-blue-800 text-xs font-medium">
                                            Full Report
                                        </button>
                                        <button @click="analyzeCompetitors(item.url)"
                                            class="text-purple-600 hover:text-purple-800 text-xs font-medium">
                                            Competitors
                                        </button>
                                        <button @click="requestIndexing(item.url)"
                                            class="text-green-600 hover:text-green-800 text-xs font-medium">
                                            Re-index
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="px-6 py-4 border-t border-gray-200 bg-gray-50">
                    <div class="flex items-center justify-between">
                        <div class="text-sm text-gray-700">
                            Showing {{ (currentPage - 1) * itemsPerPage + 1 }} to {{ Math.min(currentPage *
                                itemsPerPage,
                            filteredIndexations.length) }} of {{ filteredIndexations.length }} results
                        </div>
                        <div class="flex items-center gap-2">
                            <button @click="currentPage = Math.max(1, currentPage - 1)" :disabled="currentPage === 1"
                                class="px-3 py-1 text-sm border border-gray-300 rounded-md hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed">
                                Previous
                            </button>
                            <span class="text-sm text-gray-700">Page {{ currentPage }} of {{ totalPages }}</span>
                            <button @click="currentPage = Math.min(totalPages, currentPage + 1)"
                                :disabled="currentPage === totalPages"
                                class="px-3 py-1 text-sm border border-gray-300 rounded-md hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed">
                                Next
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Enhanced Detailed Report Modal -->
            <div v-if="report" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50"
                @click="closeReport">
                <div class="bg-white rounded-lg max-w-6xl w-full m-4 max-h-screen overflow-y-auto" @click.stop>
                    <div class="p-6 border-b border-gray-200">
                        <div class="flex items-center justify-between">
                            <h2 class="text-lg font-semibold text-gray-900">Comprehensive SEO Report</h2>
                            <button @click="closeReport" class="text-gray-400 hover:text-gray-600">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>
                        <p class="text-sm text-gray-500 mt-1 break-all">{{ report.url }}</p>
                    </div>

                    <div class="p-6 space-y-8">
                        <!-- Enhanced Overview -->
                        <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                            <div class="text-center p-4 bg-green-50 rounded-lg">
                                <div class="text-2xl font-bold text-green-600 mb-1">{{ report.seo_score }}/100</div>
                                <div class="text-sm text-green-700">SEO Score</div>
                            </div>
                            <div class="text-center p-4 bg-blue-50 rounded-lg">
                                <div class="text-2xl font-bold text-blue-600 mb-1">{{ report.search_visibility.position }}</div>
                                <div class="text-sm text-blue-700">Avg Position</div>
                            </div>
                            <div class="text-center p-4 bg-purple-50 rounded-lg">
                                <div class="text-2xl font-bold text-purple-600 mb-1">{{ report.search_visibility.ctr }}%</div>
                                <div class="text-sm text-purple-700">Click Rate</div>
                            </div>
                            <div class="text-center p-4 bg-orange-50 rounded-lg">
                                <div class="text-2xl font-bold text-orange-600 mb-1">{{ report.detected_niche }}</div>
                                <div class="text-sm text-orange-700">Content Niche</div>
                            </div>
                        </div>

                        <!-- Search Console Data -->
                        <div class="bg-gray-50 rounded-lg p-6">
                            <h3 class="font-semibold text-gray-900 mb-4">Search Console Performance</h3>
                            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                                <div>
                                    <div class="text-xl font-bold text-blue-600">{{ report.search_visibility.clicks }}</div>
                                    <div class="text-sm text-gray-600">Total Clicks</div>
                                    <div class="text-xs text-green-600">+12% vs last month</div>
                                </div>
                                <div>
                                    <div class="text-xl font-bold text-purple-600">{{ report.search_visibility.impressions }}</div>
                                    <div class="text-sm text-gray-600">Impressions</div>
                                    <div class="text-xs text-green-600">+8% vs last month</div>
                                </div>
                                <div>
                                    <div class="text-xl font-bold text-orange-600">{{ report.search_visibility.ctr }}%</div>
                                    <div class="text-sm text-gray-600">Click-Through Rate</div>
                                    <div class="text-xs text-green-600">+3.2% vs last month</div>
                                </div>
                                <div>
                                    <div class="text-xl font-bold text-red-600">{{ report.search_visibility.position }}</div>
                                    <div class="text-sm text-gray-600">Average Position</div>
                                    <div class="text-xs text-green-600">-2.1 vs last month</div>
                                </div>
                            </div>
                        </div>

                        <!-- Structured Data Analysis -->
                        <div class="bg-white border rounded-lg p-6">
                            <h3 class="font-semibold text-gray-900 mb-4">Structured Data (JSON-LD) Analysis</h3>
                            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                                <div>
                                    <div class="flex items-center mb-3">
                                        <svg :class="report.json_ld_valid ? 'text-green-500' : 'text-red-500'"
                                            class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                            <path v-if="report.json_ld_valid" fill-rule="evenodd"
                                                d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                                clip-rule="evenodd" />
                                            <path v-else fill-rule="evenodd"
                                                d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z"
                                                clip-rule="evenodd" />
                                        </svg>
                                        <span :class="report.json_ld_valid ? 'text-green-600' : 'text-red-600'" class="font-medium">
                                            JSON-LD {{ report.json_ld_valid ? 'Valid' : 'Invalid' }}
                                        </span>
                                    </div>
                                    <div class="space-y-2">
                                        <div v-if="report.structured_data">
                                            <label class="text-sm font-medium text-gray-600">Schema Type:</label>
                                            <p class="text-sm text-gray-900">{{ report.structured_data.type || 'Recipe' }}</p>
                                        </div>
                                        <div v-if="report.structured_data?.properties">
                                            <label class="text-sm font-medium text-gray-600">Required Properties:</label>
                                            <div class="mt-1 space-y-1">
                                                <div v-for="prop in report.structured_data.properties" :key="prop.name"
                                                    class="flex items-center text-sm">
                                                    <svg :class="prop.present ? 'text-green-500' : 'text-red-500'"
                                                        class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                                        <path v-if="prop.present" fill-rule="evenodd"
                                                            d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                                                            clip-rule="evenodd" />
                                                        <path v-else fill-rule="evenodd"
                                                            d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"
                                                            clip-rule="evenodd" />
                                                    </svg>
                                                    <span>{{ prop.name }}</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div v-if="!report.json_ld_valid">
                                    <label class="text-sm font-medium text-gray-600">Issues Found:</label>
                                    <div class="mt-2 space-y-1">
                                        <div v-for="error in (report.structured_data?.errors || [])" :key="error"
                                            class="text-sm text-red-600 bg-red-50 p-2 rounded">
                                            {{ error }}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Competitor Analysis -->
                        <div v-if="report.competitor_analysis" class="bg-white border rounded-lg p-6">
                            <h3 class="font-semibold text-gray-900 mb-4">Competitor Analysis</h3>
                            <div class="space-y-4">
                                <div v-for="competitor in report.competitor_analysis.top_competitors" :key="competitor.url"
                                    class="border rounded-lg p-4">
                                    <div class="flex items-center justify-between mb-2">
                                        <h4 class="font-medium text-gray-900">{{ competitor.title }}</h4>
                                        <span class="text-sm text-gray-500">Position {{ competitor.position }}</span>
                                    </div>
                                    <p class="text-sm text-gray-600 mb-3">{{ competitor.url }}</p>
                                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 text-sm">
                                        <div>
                                            <span class="text-gray-500">Word Count:</span>
                                            <span class="font-medium ml-1">{{ competitor.word_count }}</span>
                                        </div>
                                        <div>
                                            <span class="text-gray-500">Internal Links:</span>
                                            <span class="font-medium ml-1">{{ competitor.internal_links }}</span>
                                        </div>
                                        <div>
                                            <span class="text-gray-500">Schema Score:</span>
                                            <span class="font-medium ml-1">{{ competitor.schema_score }}/10</span>
                                        </div>
                                        <div>
                                            <span class="text-gray-500">Load Speed:</span>
                                            <span class="font-medium ml-1">{{ competitor.load_speed }}s</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Enhanced SEO Elements -->
                        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                            <div class="space-y-4">
                                <h3 class="font-semibold text-gray-900">SEO Elements Analysis</h3>
                                <div class="space-y-3">
                                    <div>
                                        <div class="flex items-center justify-between mb-1">
                                            <label class="text-sm font-medium text-gray-600">Title Tag</label>
                                            <span class="text-xs text-gray-500">{{ report.data?.title?.length || 0 }} chars</span>
                                        </div>
                                        <p class="text-sm text-gray-900 bg-gray-50 p-2 rounded">{{ report.data?.title || 'Delicious Honey Garlic Pork Chops Recipe' }}</p>
                                        <div class="flex items-center mt-1">
                                            <svg class="w-4 h-4 text-green-500 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                            </svg>
                                            <span class="text-xs text-green-600">Optimal length</span>
                                        </div>
                                    </div>

                                    <div>
                                        <div class="flex items-center justify-between mb-1">
                                            <label class="text-sm font-medium text-gray-600">Meta Description</label>
                                            <span class="text-xs text-gray-500">{{ report.data?.meta_description?.length || 0 }} chars</span>
                                        </div>
                                        <p class="text-sm text-gray-900 bg-gray-50 p-2 rounded">{{ report.data?.meta_description || 'Learn how to make tender, juicy honey garlic pork chops with this easy recipe.' }}</p>
                                        <div class="flex items-center mt-1">
                                            <svg class="w-4 h-4 text-green-500 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                            </svg>
                                            <span class="text-xs text-green-600">Good length</span>
                                        </div>
                                    </div>

                                    <div>
                                        <label class="text-sm font-medium text-gray-600">Canonical URL</label>
                                        <p class="text-sm text-gray-900 bg-gray-50 p-2 rounded break-all">{{ report.data?.canonical || report.url }}</p>
                                    </div>

                                    <div>
                                        <label class="text-sm font-medium text-gray-600">Focus Keywords</label>
                                        <div class="flex flex-wrap gap-1 mt-1">
                                            <span v-for="keyword in (report.focus_keywords || ['honey garlic pork chops', 'easy pork recipe'])"
                                                :key="keyword" class="px-2 py-1 bg-blue-100 text-blue-800 text-xs rounded">
                                                {{ keyword }}
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="space-y-4">
                                <h3 class="font-semibold text-gray-900">Content Structure</h3>
                                <div class="space-y-3">
                                    <div>
                                        <label class="text-sm font-medium text-gray-600">Heading Structure</label>
                                        <div class="space-y-1 mt-2">
                                            <div v-for="heading in (report.content_analysis?.headings || [])" :key="heading.text"
                                                class="flex items-center text-sm">
                                                <span :class="`text-${heading.level === 'h1' ? 'blue' : heading.level === 'h2' ? 'green' : 'gray'}-600 font-medium mr-2`">
                                                    {{ heading.level.toUpperCase() }}:
                                                </span>
                                                <span class="text-gray-900">{{ heading.text }}</span>
                                            </div>
                                        </div>
                                    </div>

                                    <div>
                                        <label class="text-sm font-medium text-gray-600">Content Metrics</label>
                                        <div class="grid grid-cols-2 gap-4 mt-2 text-sm">
                                            <div>
                                                <span class="text-gray-500">Word Count:</span>
                                                <span class="font-medium ml-1">{{ report.content_analysis?.word_count || '1,247' }}</span>
                                            </div>
                                            <div>
                                                <span class="text-gray-500">Reading Time:</span>
                                                <span class="font-medium ml-1">{{ report.content_analysis?.reading_time || '6' }} min</span>
                                            </div>
                                            <div>
                                                <span class="text-gray-500">Images:</span>
                                                <span class="font-medium ml-1">{{ report.content_analysis?.images || '8' }}</span>
                                            </div>
                                            <div>
                                                <span class="text-gray-500">Alt Tags:</span>
                                                <span class="font-medium ml-1">{{ report.content_analysis?.alt_tags || '6' }}/8</span>
                                            </div>
                                        </div>
                                    </div>

                                    <div>
                                        <label class="text-sm font-medium text-gray-600">Internal Links ({{ (report.data?.internal_links || []).length }})</label>
                                        <div class="max-h-32 overflow-y-auto space-y-1 mt-1">
                                            <div v-for="link in (report.data?.internal_links || [])" :key="link"
                                                class="text-xs text-gray-700 bg-gray-50 p-1 rounded">
                                                {{ link }}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- AI-Powered Recommendations -->
                        <div class="bg-gradient-to-r from-blue-50 to-purple-50 rounded-lg p-6">
                            <h3 class="font-semibold text-gray-900 mb-4">🤖 AI-Powered Recommendations</h3>
                            <div class="space-y-4">
                                <div v-for="recommendation in report.ai_recommendations" :key="recommendation.type"
                                    class="flex items-start gap-3 p-4 bg-white rounded-lg border">
                                    <div :class="`w-10 h-10 rounded-lg flex items-center justify-center ${getRecommendationIconBg(recommendation.priority)}`">
                                        <svg class="w-5 h-5" :class="getRecommendationIconColor(recommendation.priority)"
                                            fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                :d="getRecommendationIcon(recommendation.type)" />
                                        </svg>
                                    </div>
                                    <div class="flex-1">
                                        <div class="flex items-center justify-between mb-1">
                                            <h4 class="font-medium text-gray-900">{{ recommendation.title }}</h4>
                                            <span :class="`px-2 py-1 rounded text-xs font-medium ${getRecommendationBadge(recommendation.priority)}`">
                                                {{ recommendation.priority }}
                                            </span>
                                        </div>
                                        <p class="text-sm text-gray-600 mb-2">{{ recommendation.description }}</p>
                                        <div class="text-xs text-gray-500">
                                            <span class="font-medium">Impact:</span> {{ recommendation.impact }}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Technical SEO -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="bg-white border rounded-lg p-6">
                                <h3 class="font-semibold text-gray-900 mb-4">Technical SEO</h3>
                                <div class="space-y-3">
                                    <div class="flex items-center justify-between">
                                        <span class="text-sm text-gray-600">Page Load Speed</span>
                                        <span class="font-medium text-green-600">{{ report.technical?.load_speed || '1.2s' }}</span>
                                    </div>
                                    <div class="flex items-center justify-between">
                                        <span class="text-sm text-gray-600">Mobile Friendly</span>
                                        <span class="font-medium text-green-600">✅ Yes</span>
                                    </div>
                                    <div class="flex items-center justify-between">
                                        <span class="text-sm text-gray-600">HTTPS Secure</span>
                                        <span class="font-medium text-green-600">✅ Yes</span>
                                    </div>
                                    <div class="flex items-center justify-between">
                                        <span class="text-sm text-gray-600">XML Sitemap</span>
                                        <span class="font-medium text-green-600">✅ Found</span>
                                    </div>
                                    <div class="flex items-center justify-between">
                                        <span class="text-sm text-gray-600">Robots.txt</span>
                                        <span class="font-medium text-green-600">✅ Valid</span>
                                    </div>
                                </div>
                            </div>

                            <div class="bg-white border rounded-lg p-6">
                                <h3 class="font-semibold text-gray-900 mb-4">Content Quality</h3>
                                <div class="space-y-3">
                                    <div class="flex items-center justify-between">
                                        <span class="text-sm text-gray-600">Readability Score</span>
                                        <span class="font-medium text-green-600">{{ report.content_quality?.readability || '78/100' }}</span>
                                    </div>
                                    <div class="flex items-center justify-between">
                                        <span class="text-sm text-gray-600">Keyword Density</span>
                                        <span class="font-medium text-green-600">{{ report.content_quality?.keyword_density || '2.1%' }}</span>
                                    </div>
                                    <div class="flex items-center justify-between">
                                        <span class="text-sm text-gray-600">Duplicate Content</span>
                                        <span class="font-medium text-green-600">✅ None</span>
                                    </div>
                                    <div class="flex items-center justify-between">
                                        <span class="text-sm text-gray-600">Content Freshness</span>
                                        <span class="font-medium text-green-600">{{ report.content_quality?.freshness || 'Recent' }}</span>
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
import { ref, computed, onMounted } from 'vue';
import axios from 'axios';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';

// Reactive data
const indexations = ref({ data: [] });
const loading = ref(true);
const report = ref(null);
const searchQuery = ref('');
const statusFilter = ref('');
const verdictFilter = ref('');
const nicheFilter = ref('');
const currentPage = ref(1);
const itemsPerPage = ref(10);

// Enhanced sample data with all new features
onMounted(async () => {
    try {
        await new Promise(resolve => setTimeout(resolve, 1000));

        indexations.value = {
            data: [
                {
                    id: 1,
                    url: '/spicy-honey-garlic-pork-chops/',
                    status: 'indexed',
                    verdict: 'valid',
                    indexing_state: 'indexed',
                    last_crawled_at: '2025-06-01T10:30:00Z',
                    detected_niche: 'Food & Recipe',
                    search_data: {
                        clicks: 1247,
                        impressions: 15632,
                        ctr: 7.98,
                        position: 3.2
                    },
                    seo_quality: {
                        score: 89,
                        issues: []
                    },
                    json_ld: {
                        valid: true,
                        type: 'Recipe',
                        errors: []
                    }
                },
                {
                    id: 2,
                    url: '/',
                    status: 'indexed',
                    verdict: 'warning',
                    indexing_state: 'indexed',
                    last_crawled_at: '2025-06-02T15:45:00Z',
                    detected_niche: 'Food & Recipe',
                    search_data: {
                        clicks: 3421,
                        impressions: 45632,
                        ctr: 7.5,
                        position: 2.1
                    },
                    seo_quality: {
                        score: 76,
                        issues: ['Meta description could be improved', 'Add more internal links']
                    },
                    json_ld: {
                        valid: false,
                        type: 'WebSite',
                        errors: ['Missing organization info']
                    }
                },
                {
                    id: 3,
                    url: '/lemon-bliss-tart/',
                    status: 'crawled',
                    verdict: 'valid',
                    indexing_state: 'submitted',
                    last_crawled_at: '2025-06-01T08:20:00Z',
                    detected_niche: 'Food & Recipe',
                    search_data: {
                        clicks: 234,
                        impressions: 4521,
                        ctr: 5.18,
                        position: 8.4
                    },
                    seo_quality: {
                        score: 92,
                        issues: []
                    },
                    json_ld: {
                        valid: true,
                        type: 'Recipe',
                        errors: []
                    }
                },
                {
                    id: 4,
                    url: '/cracked-out-chicken-tater-tot-casserole/',
                    status: 'not-indexed',
                    verdict: 'error',
                    indexing_state: 'blocked',
                    last_crawled_at: '2025-05-30T12:15:00Z',
                    detected_niche: 'Food & Recipe',
                    search_data: {
                        clicks: 12,
                        impressions: 187,
                        ctr: 6.42,
                        position: 24.7
                    },
                    seo_quality: {
                        score: 45,
                        issues: ['Missing meta description', 'No H1 tag', 'Broken internal links']
                    },
                    json_ld: {
                        valid: false,
                        type: 'None',
                        errors: ['No structured data found']
                    }
                },
                {
                    id: 5,
                    url: '/white-cheddar-and-spinach-chicken-burgers/',
                    status: 'indexed',
                    verdict: 'valid',
                    indexing_state: 'indexed',
                    last_crawled_at: '2025-06-02T09:30:00Z',
                    detected_niche: 'Food & Recipe',
                    search_data: {
                        clicks: 876,
                        impressions: 12453,
                        ctr: 7.03,
                        position: 4.8
                    },
                    seo_quality: {
                        score: 85,
                        issues: []
                    },
                    json_ld: {
                        valid: true,
                        type: 'Recipe',
                        errors: []
                    }
                },
                {
                    id: 6,
                    url: '/about/',
                    status: 'indexed',
                    verdict: 'warning',
                    indexing_state: 'indexed',
                    last_crawled_at: '2025-05-28T14:20:00Z',
                    detected_niche: 'About',
                    search_data: {
                        clicks: 145,
                        impressions: 3201,
                        ctr: 4.53,
                        position: 12.3
                    },
                    seo_quality: {
                        score: 68,
                        issues: ['Meta description too short', 'No schema markup']
                    },
                    json_ld: {
                        valid: false,
                        type: 'AboutPage',
                        errors: ['Missing author information']
                    }
                },
                {
                    id: 7,
                    url: '/contact/',
                    status: 'crawled',
                    verdict: 'valid',
                    indexing_state: 'submitted',
                    last_crawled_at: '2025-05-29T11:45:00Z',
                    detected_niche: 'Contact',
                    search_data: {
                        clicks: 89,
                        impressions: 1876,
                        ctr: 4.75,
                        position: 15.2
                    },
                    seo_quality: {
                        score: 72,
                        issues: []
                    },
                    json_ld: {
                        valid: true,
                        type: 'ContactPage',
                        errors: []
                    }
                },
                {
                    id: 8,
                    url: '/recipes/',
                    status: 'indexed',
                    verdict: 'valid',
                    indexing_state: 'indexed',
                    last_crawled_at: '2025-06-01T16:30:00Z',
                    detected_niche: 'Food & Recipe',
                    search_data: {
                        clicks: 2156,
                        impressions: 28734,
                        ctr: 7.51,
                        position: 3.8
                    },
                    seo_quality: {
                        score: 88,
                        issues: []
                    },
                    json_ld: {
                        valid: true,
                        type: 'CollectionPage',
                        errors: []
                    }
                }
            ]
        };
    } catch (e) {
        console.error(e);
    } finally {
        loading.value = false;
    }
});

// Enhanced computed properties
const filteredIndexations = computed(() => {
    let filtered = indexations.value.data;

    if (searchQuery.value) {
        filtered = filtered.filter(item =>
            item.url.toLowerCase().includes(searchQuery.value.toLowerCase())
        );
    }

    if (statusFilter.value) {
        filtered = filtered.filter(item => item.status === statusFilter.value);
    }

    if (verdictFilter.value) {
        filtered = filtered.filter(item => item.verdict === verdictFilter.value);
    }

    if (nicheFilter.value) {
        filtered = filtered.filter(item =>
            item.detected_niche.toLowerCase().includes(nicheFilter.value.toLowerCase())
        );
    }

    return filtered;
});

const paginatedIndexations = computed(() => {
    const start = (currentPage.value - 1) * itemsPerPage.value;
    const end = start + itemsPerPage.value;
    return filteredIndexations.value.slice(start, end);
});

const totalPages = computed(() => {
    return Math.ceil(filteredIndexations.value.length / itemsPerPage.value);
});

// Enhanced helper functions
const getIndexedCount = () => {
    return indexations.value.data.filter(item => item.status === 'indexed').length;
};

const getTotalClicks = () => {
    return indexations.value.data.reduce((sum, item) => sum + item.search_data.clicks, 0);
};

const getTotalImpressions = () => {
    return indexations.value.data.reduce((sum, item) => sum + item.search_data.impressions, 0);
};

const getAvgCTR = () => {
    const avg = indexations.value.data.reduce((sum, item) => sum + item.search_data.ctr, 0) / indexations.value.data.length;
    return avg.toFixed(1);
};

const getAvgPosition = () => {
    const avg = indexations.value.data.reduce((sum, item) => sum + item.search_data.position, 0) / indexations.value.data.length;
    return avg.toFixed(1);
};

const getAvgSEOScore = () => {
    const avg = indexations.value.data.reduce((sum, item) => sum + item.seo_quality.score, 0) / indexations.value.data.length;
    return Math.round(avg);
};

const getIssuesCount = () => {
    return indexations.value.data.filter(item => item.verdict === 'error' || item.verdict === 'warning').length;
};

const getNicheBadgeClass = (niche) => {
    const classes = {
        'Food & Recipe': 'bg-orange-100 text-orange-800',
        'Technology': 'bg-blue-100 text-blue-800',
        'Health': 'bg-green-100 text-green-800',
        'Lifestyle': 'bg-purple-100 text-purple-800',
        'About': 'bg-gray-100 text-gray-800',
        'Contact': 'bg-gray-100 text-gray-800'
    };
    return classes[niche] || 'bg-gray-100 text-gray-800';
};

const getSEOScoreColor = (score) => {
    if (score >= 80) return 'bg-green-500';
    if (score >= 60) return 'bg-yellow-500';
    return 'bg-red-500';
};

const getStatusBadgeClass = (status) => {
    const classes = {
        'indexed': 'bg-green-100 text-green-800',
        'crawled': 'bg-blue-100 text-blue-800',
        'not-indexed': 'bg-red-100 text-red-800',
        'blocked': 'bg-gray-100 text-gray-800'
    };
    return classes[status] || 'bg-gray-100 text-gray-800';
};

const getVerdictBadgeClass = (verdict) => {
    const classes = {
        'valid': 'bg-green-100 text-green-800',
        'warning': 'bg-yellow-100 text-yellow-800',
        'error': 'bg-red-100 text-red-800'
    };
    return classes[verdict] || 'bg-gray-100 text-gray-800';
};

const getVerdictText = (verdict) => {
    const texts = {
        'valid': 'No Issues',
        'warning': 'Warning',
        'error': 'Error'
    };
    return texts[verdict] || verdict;
};

const getIndexingIconClass = (state) => {
    const classes = {
        'indexed': 'bg-green-500',
        'submitted': 'bg-yellow-500',
        'blocked': 'bg-red-500',
        'crawled': 'bg-blue-500'
    };
    return classes[state] || 'bg-gray-500';
};

const getPageType = (url) => {
    if (url === '/') return 'Homepage';
    if (url.includes('/recipe')) return 'Recipe';
    if (url.includes('/about')) return 'About Page';
    if (url.includes('/contact')) return 'Contact Page';
    if (url.includes('/blog')) return 'Blog Post';
    return 'Content Page';
};

const formatDate = (dateString) => {
    if (!dateString) return 'Never';
    const date = new Date(dateString);
    return date.toLocaleDateString('en-US', {
        month: 'short',
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
    });
};

const getRecommendationIconBg = (priority) => {
    const classes = {
        'high': 'bg-red-100',
        'medium': 'bg-yellow-100',
        'low': 'bg-blue-100'
    };
    return classes[priority] || 'bg-gray-100';
};

const getRecommendationIconColor = (priority) => {
    const classes = {
        'high': 'text-red-600',
        'medium': 'text-yellow-600',
        'low': 'text-blue-600'
    };
    return classes[priority] || 'text-gray-600';
};

const getRecommendationBadge = (priority) => {
    const classes = {
        'high': 'bg-red-100 text-red-800',
        'medium': 'bg-yellow-100 text-yellow-800',
        'low': 'bg-blue-100 text-blue-800'
    };
    return classes[priority] || 'bg-gray-100 text-gray-800';
};

const getRecommendationIcon = (type) => {
    const icons = {
        'meta': 'M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z',
        'schema': 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z',
        'content': 'M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z',
        'links': 'M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1',
        'technical': 'M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z'
    };
    return icons[type] || icons['content'];
};

// Enhanced action functions
const fetchReport = async (url) => {
    try {
        await new Promise(resolve => setTimeout(resolve, 500));

        report.value = {
            url: url,
            seo_score: Math.floor(Math.random() * 20) + 80,
            json_ld_valid: Math.random() > 0.3,
            detected_niche: 'Food & Recipes',
            search_visibility: {
                clicks: Math.floor(Math.random() * 2000) + 500,
                impressions: Math.floor(Math.random() * 30000) + 10000,
                ctr: (Math.random() * 5 + 3).toFixed(2),
                position: (Math.random() * 10 + 1).toFixed(1)
            },
            structured_data: {
                type: 'Recipe',
                properties: [
                    { name: 'name', present: true },
                    { name: 'author', present: true },
                    { name: 'datePublished', present: true },
                    { name: 'recipeIngredient', present: true },
                    { name: 'recipeInstructions', present: true },
                    { name: 'nutrition', present: false },
                    { name: 'image', present: true }
                ],
                errors: ['Missing nutrition information', 'Invalid image format']
            },
            competitor_analysis: {
                top_competitors: [
                    {
                        title: 'The Best Honey Garlic Pork Chops - Food Network',
                        url: 'https://foodnetwork.com/honey-garlic-pork-chops',
                        position: 1,
                        word_count: 1850,
                        internal_links: 12,
                        schema_score: 9,
                        load_speed: 1.8
                    },
                    {
                        title: 'Easy Honey Garlic Pork Chops Recipe - Allrecipes',
                        url: 'https://allrecipes.com/honey-garlic-pork-chops',
                        position: 2,
                        word_count: 1420,
                        internal_links: 8,
                        schema_score: 8,
                        load_speed: 2.1
                    }
                ]
            },
            ai_recommendations: [
                {
                    type: 'schema',
                    priority: 'high',
                    title: 'Add Missing Nutrition Schema',
                    description: 'Include nutrition information in your Recipe schema to improve rich snippet chances.',
                    impact: 'Could increase CTR by 15-25%'
                },
                {
                    type: 'content',
                    priority: 'medium',
                    title: 'Expand Content Depth',
                    description: 'Add cooking tips, variations, and storage instructions to match competitor content length.',
                    impact: 'Could improve ranking by 2-3 positions'
                },
                {
                    type: 'links',
                    priority: 'medium',
                    title: 'Increase Internal Linking',
                    description: 'Add 4-6 more relevant internal links to boost page authority and user engagement.',
                    impact: 'Could improve page authority by 10-15%'
                },
                {
                    type: 'technical',
                    priority: 'low',
                    title: 'Optimize Image Formats',
                    description: 'Convert images to WebP format for faster loading and better Core Web Vitals.',
                    impact: 'Could improve load speed by 0.3-0.5s'
                }
            ],
            content_analysis: {
                word_count: 1247,
                reading_time: 6,
                images: 8,
                alt_tags: 6,
                headings: [
                    { level: 'h1', text: 'Spicy Honey Garlic Pork Chops Recipe' },
                    { level: 'h2', text: 'Ingredients' },
                    { level: 'h2', text: 'Instructions' },
                    { level: 'h3', text: 'Preparing the Marinade' },
                    { level: 'h3', text: 'Cooking the Pork Chops' },
                    { level: 'h2', text: 'Serving Suggestions' },
                    { level: 'h2', text: 'Storage Tips' }
                ]
            },
            data: {
                title: url === '/' ? 'Heaven Food Recipe - Delicious Recipes for Every Occasion' :
                    url.includes('pork') ? 'Spicy Honey Garlic Pork Chops Recipe - Heaven Food Recipe' :
                        url.includes('lemon') ? 'Lemon Bliss Tart - Perfect Citrus Dessert Recipe' :
                            'Delicious Recipe - Heaven Food Recipe',
                meta_description: url.includes('pork') ?
                    'Learn how to make tender, juicy honey garlic pork chops with this easy recipe. Perfect for weeknight dinners with amazing sweet and savory flavors!' :
                    'Discover amazing recipes with our step-by-step guides. Perfect for home cooks of all skill levels!',
                canonical: `https://heavenfoodrecipe.com${url}`,
                h1: [url === '/' ? 'Welcome to Heaven Food Recipe' : 'Amazing Recipe Guide'],
                internal_links: ['/recipes/', '/about/', '/contact/', '/blog/', '/categories/dinner/', '/categories/dessert/']
            },
            focus_keywords: ['honey garlic pork chops', 'easy pork recipe', 'weeknight dinner'],
            technical: {
                load_speed: '1.2s',
                mobile_friendly: true,
                https_secure: true,
                sitemap_found: true,
                robots_valid: true
            },
            content_quality: {
                readability: '78/100',
                keyword_density: '2.1%',
                duplicate_content: false,
                freshness: 'Recent'
            }
        };
    } catch (e) {
        console.error('Error fetching report:', e);
    }
};

const analyzeCompetitors = async (url) => {
    try {
        await new Promise(resolve => setTimeout(resolve, 1000));
        alert(`Competitor analysis initiated for: ${url}\n\nAnalyzing top 10 competitors for content gaps, keyword opportunities, and schema implementation...`);
    } catch (e) {
        console.error('Error analyzing competitors:', e);
    }
};

const requestIndexing = async (url) => {
    try {
        await new Promise(resolve => setTimeout(resolve, 1000));
        alert(`Indexing request submitted for: ${url}`);
    } catch (e) {
        console.error('Error requesting indexing:', e);
    }
};

const refreshAllData = async () => {
    loading.value = true;
    try {
        await new Promise(resolve => setTimeout(resolve, 2000));
        // Simulate refreshing search console data
        indexations.value.data.forEach(item => {
            item.search_data.clicks += Math.floor(Math.random() * 50);
            item.search_data.impressions += Math.floor(Math.random() * 500);
            item.search_data.ctr = (item.search_data.clicks / item.search_data.impressions * 100).toFixed(2);
        });
        alert('All SEO data refreshed successfully!');
    } catch (e) {
        console.error('Error refreshing data:', e);
    } finally {
        loading.value = false;
    }
};

const showIssueDetails = (item) => {
    const issueDetails = item.seo_quality.issues.length > 0 ?
        item.seo_quality.issues.join('\n• ') :
        'No specific issues found';

    alert(`SEO Issues for ${item.url}:\n\n• ${issueDetails}\n\nRecommendation: Use the "Full Report" to get detailed AI-powered suggestions for improvements.`);
};

const closeReport = () => {
    report.value = null;
};
</script>

<style scoped>
/* Enhanced transitions and animations */
* {
    transition-property: color, background-color, border-color, opacity, transform;
    transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1);
    transition-duration: 200ms;
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

/* Enhanced table hover effects */
tbody tr:hover {
    transform: translateY(-1px);
    box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
}

/* Modal backdrop with blur */
.fixed.inset-0 {
    backdrop-filter: blur(4px);
}

/* Custom scrollbar styling */
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

.overflow-y-auto::-webkit-scrollbar-thumb:hover {
    background: #94a3b8;
}

/* Enhanced focus states */
button:focus,
input:focus,
select:focus {
    outline: 2px solid #3b82f6;
    outline-offset: 2px;
}

/* Progress bar animations */
.h-2 {
    transition: width 0.5s ease-in-out;
}

/* Badge hover effects */
.bg-green-100,
.bg-yellow-100,
.bg-red-100,
.bg-blue-100,
.bg-orange-100,
.bg-purple-100 {
    transition: all 0.2s ease;
}

/* Button hover animations */
button:hover {
    transform: translateY(-1px);
}

button:active {
    transform: translateY(0);
}

/* Card hover effects */
.bg-white:hover {
    box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
}

/* Disabled button styles */
button:disabled {
    cursor: not-allowed;
    opacity: 0.5;
}

button:disabled:hover {
    transform: none;
}

/* Gradient text effects */
.bg-gradient-to-r {
    background-image: linear-gradient(to right, #eff6ff, #f3e8ff);
}

/* Icon animations */
svg {
    transition: transform 0.2s ease;
}

button:hover svg {
    transform: scale(1.1);
}

/* Table cell content alignment */
td > div {
    min-height: 2rem;
    display: flex;
    align-items: center;
}

/* Modal content animations */
.fixed .bg-white {
    animation: modalSlide 0.3s ease-out;
}

@keyframes modalSlide {
    from {
        opacity: 0;
        transform: translateY(-20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

/* Recommendation card hover effects */
.bg-white.rounded-lg.border:hover {
    border-color: #e5e7eb;
    box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
}

/* Performance metric animations */
.text-2xl {
    transition: color 0.3s ease;
}

/* Search input enhancements */
input[type="text"]:focus {
    box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
}

/* Select dropdown styling */
select {
    background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 20 20'%3e%3cpath stroke='%236b7280' stroke-linecap='round' stroke-linejoin='round' stroke-width='1.5' d='m6 8 4 4 4-4'/%3e%3c/svg%3e");
    background-position: right 0.5rem center;
    background-repeat: no-repeat;
    background-size: 1.5em 1.5em;
    padding-right: 2.5rem;
}

/* Status indicator pulse animation */
.w-2.h-2.rounded-full {
    animation: pulse 2s infinite;
}

@keyframes pulse {
    0%, 100% {
        opacity: 1;
    }
    50% {
        opacity: 0.5;
    }
}

/* Responsive text truncation */
.truncate {
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
}

/* Enhanced card spacing */
.space-y-8 > * + * {
    margin-top: 2rem;
}

.space-y-6 > * + * {
    margin-top: 1.5rem;
}

.space-y-4 > * + * {
    margin-top: 1rem;
}

.space-y-3 > * + * {
    margin-top: 0.75rem;
}

.space-y-2 > * + * {
    margin-top: 0.5rem;
}

.space-y-1 > * + * {
    margin-top: 0.25rem;
}
</style>
