<template>
    <AuthenticatedLayout>
        <div class="bg-gradient-to-br from-slate-50 to-blue-50 min-h-screen font-sans">
            <!-- Sticky Header -->
            <header class="sticky top-0 z-50 bg-white/80 backdrop-blur-md border-b border-gray-200 shadow-sm">
                <div class=" mx-auto px-6 py-4">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center space-x-4">
                            <h1 class="text-2xl font-bold text-gray-900">Link Analytics Dashboard</h1>
                            <div class="flex items-center space-x-2 text-sm text-gray-600">
                                <span>{{ allLinks.length }} Links</span>
                                <span>•</span>
                                <span>{{ getTotalTraffic() }} Total Views</span>
                            </div>
                        </div>

                        <div class="flex items-center space-x-4">
                            <!-- Search Bar -->
                            <div class="relative">
                                <i
                                    class="fas fa-search absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                                <input v-model="searchQuery" type="text" placeholder="Search links..."
                                    class="pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent w-64">
                            </div>

                            <!-- Date Range Selector -->
                            <select v-model="selectedDateRange"
                                class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500">
                                <option value="today">Today</option>
                                <option value="7d">Last 7 days</option>
                                <option value="30d">Last 30 days</option>
                                <option value="90d">Last 3 months</option>
                            </select>

                            <!-- Sort Options -->
                            <select v-model="sortBy"
                                class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500">
                                <option value="traffic">Sort by Traffic</option>
                                <option value="bounce">Sort by Bounce Rate</option>
                                <option value="conversion">Sort by Conversion</option>
                                <option value="recent">Most Recent</option>
                            </select>

                            <button @click="exportData"
                                class="flex items-center space-x-2 px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg transition-colors">
                                <i class="fas fa-download"></i>
                                <span>Export</span>
                            </button>
                        </div>
                    </div>
                </div>
            </header>

            <div class=" mx-auto  ">
                <!-- Overview Stats -->

                <!-- Traffic Sources Overview -->
                <div class="grid my-4 grid-cols-1 lg:grid-cols-3 gap-6">
                    <div class="lg:col-span-2 bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-6">Traffic Sources Distribution</h3>
                        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                            <div v-for="source in trafficSources" :key="source.name"
                                class="text-center p-4 rounded-lg border border-gray-100">
                                <div :class="source.iconClass" class="text-2xl mb-2"></div>
                                <div class="text-lg font-bold text-gray-900">{{ source.percentage }}%</div>
                                <div class="text-sm text-gray-600">{{ source.name }}</div>
                                <div class="text-xs text-gray-500 mt-1">{{ formatNumber(source.traffic) }} visits</div>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-6">Performance Alerts</h3>
                        <div class="space-y-4">
                            <div v-for="alert in performanceAlerts" :key="alert.id" :class="getAlertClass(alert.type)"
                                class="p-3 rounded-lg border-l-4">
                                <div class="flex items-start space-x-3">
                                    <i :class="alert.icon" class="mt-1"></i>
                                    <div>
                                        <p class="font-medium text-sm">{{ alert.title }}</p>
                                        <p class="text-xs text-gray-600 mt-1">{{ alert.description }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Link Performance Table -->
                <div class="bg-white my-4 rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h3 class="text-lg font-semibold text-gray-900">All Links Performance</h3>
                        <p class="text-sm text-gray-600 mt-1">Track performance metrics for each link</p>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Link</th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Traffic Trend</th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Today</th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        7 Days</th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Bounce Rate</th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Avg Time</th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Sources</th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Status</th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Actions</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                <tr v-for="link in filteredLinks" :key="link.id"
                                    class="hover:bg-gray-50 transition-colors">
                                    <!-- Link Info -->
                                    <td class="px-6 py-4">
                                        <div class="flex items-start space-x-3">

                                            <div class="min-w-0 flex-1">
                                                <div class="text-sm font-medium text-gray-900 truncate">{{ link.title }}
                                                </div>
                                                <div class="text-xs text-gray-500 truncate font-mono">{{ link.url }}
                                                </div>
                                                <div class="flex items-center space-x-2 mt-1">
                                                    <span :class="getTypeClass(link.type)"
                                                        class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium">
                                                        {{ link.type }}
                                                    </span>
                                                    <span class="text-xs text-gray-400">{{ link.lastUpdated }}</span>
                                                </div>
                                            </div>
                                        </div>
                                    </td>

                                    <!-- Traffic Trend -->
                                    <td class="px-6 py-4">
                                        <div class="flex items-center space-x-2">
                                            <div class="w-16 h-8">
                                                <canvas :ref="`chart-${link.id}`" class="w-full h-full"></canvas>
                                            </div>
                                            <div class="text-right">
                                                <div :class="getTrendClass(link.trend)" class="text-sm font-medium">
                                                    {{ link.trend > 0 ? '+' : '' }}{{ link.trend }}%
                                                </div>
                                                <div class="text-xs text-gray-500">vs last period</div>
                                            </div>
                                        </div>
                                    </td>

                                    <!-- Today Traffic -->
                                    <td class="px-6 py-4">
                                        <div class="text-sm font-medium text-gray-900">{{
                                            formatNumber(link.todayTraffic) }}</div>
                                        <div class="text-xs text-gray-500">views</div>
                                    </td>

                                    <!-- 7 Days Traffic -->
                                    <td class="px-6 py-4">
                                        <div class="text-sm font-medium text-gray-900">{{ formatNumber(link.weekTraffic)
                                            }}</div>
                                        <div class="text-xs text-gray-500">views</div>
                                    </td>

                                    <!-- Bounce Rate -->
                                    <td class="px-6 py-4">
                                        <div class="flex items-center space-x-2">
                                            <div class="w-12 bg-gray-200 rounded-full h-2">
                                                <div :class="getBounceRateColor(link.bounceRate)"
                                                    class="h-2 rounded-full" :style="`width: ${link.bounceRate}%`">
                                                </div>
                                            </div>
                                            <span :class="getBounceRateTextColor(link.bounceRate)"
                                                class="text-sm font-medium">{{ link.bounceRate }}%</span>
                                        </div>
                                    </td>

                                    <!-- Average Time -->
                                    <td class="px-6 py-4">
                                        <div class="text-sm font-medium text-gray-900">{{ link.avgTime }}</div>
                                        <div class="text-xs text-gray-500">avg session</div>
                                    </td>

                                    <!-- Traffic Sources -->
                                    <td class="px-6 py-4">
                                        <div class="flex items-center space-x-1">
                                            <div v-for="source in link.topSources" :key="source.name"
                                                :class="getSourceIcon(source.name)"
                                                class="w-6 h-6 rounded-full flex items-center justify-center text-xs">
                                            </div>
                                        </div>
                                        <div class="text-xs text-gray-500 mt-1">{{ link.topSources.length }} sources
                                        </div>
                                    </td>

                                    <!-- Status -->
                                    <td class="px-6 py-4">
                                        <span :class="getStatusClass(link.status)"
                                            class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium">
                                            <div :class="getStatusDot(link.status)"
                                                class="w-1.5 h-1.5 rounded-full mr-1"></div>
                                            {{ link.status }}
                                        </span>
                                    </td>

                                    <!-- Actions -->
                                    <td class="px-6 py-4">
                                        <div class="flex items-center space-x-2">
                                            <button @click="viewDetails(link)"
                                                class="text-indigo-600 hover:text-indigo-900 text-sm">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                            <button @click="optimizeLink(link)"
                                                class="text-green-600 hover:text-green-900 text-sm">
                                                <i class="fas fa-rocket"></i>
                                            </button>

                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Optimization Recommendations -->
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-6">🚀 Optimization Opportunities</h3>
                        <div class="space-y-4">
                            <div v-for="opportunity in optimizationOpportunities" :key="opportunity.id"
                                class="border border-gray-100 rounded-lg p-4">
                                <div class="flex items-start justify-between">
                                    <div class="flex-1">
                                        <h4 class="font-medium text-gray-900">{{ opportunity.title }}</h4>
                                        <p class="text-sm text-gray-600 mt-1">{{ opportunity.description }}</p>
                                        <div class="flex items-center space-x-4 mt-3">
                                            <span class="text-xs bg-green-100 text-green-800 px-2 py-1 rounded-full">
                                                Potential: +{{ opportunity.potentialGain }}% traffic
                                            </span>
                                            <span class="text-xs text-gray-500">{{ opportunity.effort }} effort</span>
                                        </div>
                                    </div>
                                    <button @click="implementOptimization(opportunity)"
                                        class="ml-4 px-3 py-1 bg-indigo-600 text-white text-xs rounded-lg hover:bg-indigo-700 transition-colors">
                                        Optimize
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-6">📊 Performance Insights</h3>
                        <div class="space-y-4">
                            <div v-for="insight in performanceInsights" :key="insight.id"
                                class="border-l-4 border-blue-500 bg-blue-50 p-4 rounded-r-lg">
                                <div class="flex items-start space-x-3">
                                    <i :class="insight.icon" class="text-blue-600 mt-1"></i>
                                    <div>
                                        <h4 class="font-medium text-gray-900">{{ insight.title }}</h4>
                                        <p class="text-sm text-gray-600 mt-1">{{ insight.description }}</p>
                                        <div class="mt-2">
                                            <span class="text-xs bg-blue-100 text-blue-800 px-2 py-1 rounded-full">
                                                {{ insight.metric }}
                                            </span>
                                        </div>
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
import { ref, computed, onMounted } from 'vue'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';

// Reactive data
const searchQuery = ref('')
const selectedDateRange = ref('7d')
const sortBy = ref('traffic')

// Sample links data
const allLinks = ref([
    {
        id: 1,
        title: 'Ultimate SEO Guide for 2024',
        url: '/blog/seo-guide-2024',
        type: 'Blog Post',
        todayTraffic: 1247,
        weekTraffic: 8934,
        bounceRate: 32,
        avgTime: '4m 32s',
        trend: 15.3,
        status: 'Performing Well',
        lastUpdated: '2 hours ago',
        topSources: [
            { name: 'Google', percentage: 65 },
            { name: 'Direct', percentage: 20 },
            { name: 'Social', percentage: 15 }
        ],
        dailyData: [120, 135, 145, 130, 156, 142, 178]
    },
    {
        id: 2,
        title: 'Product Launch Landing Page',
        url: '/launch/new-product',
        type: 'Landing Page',
        todayTraffic: 856,
        weekTraffic: 5234,
        bounceRate: 45,
        avgTime: '2m 15s',
        trend: -8.2,
        status: 'Needs Attention',
        lastUpdated: '1 day ago',
        topSources: [
            { name: 'Social', percentage: 50 },
            { name: 'Email', percentage: 30 },
            { name: 'Direct', percentage: 20 }
        ],
        dailyData: [98, 89, 76, 82, 71, 68, 85]
    },
    {
        id: 3,
        title: 'E-commerce Product Page',
        url: '/products/premium-headphones',
        type: 'Product Page',
        todayTraffic: 2341,
        weekTraffic: 15678,
        bounceRate: 28,
        avgTime: '6m 45s',
        trend: 22.7,
        status: 'Excellent',
        lastUpdated: '3 hours ago',
        topSources: [
            { name: 'Google', percentage: 70 },
            { name: 'Social', percentage: 15 },
            { name: 'Referral', percentage: 15 }
        ],
        dailyData: [180, 195, 210, 225, 240, 235, 250]
    },
    {
        id: 4,
        title: 'Free Tools Calculator',
        url: '/tools/roi-calculator',
        type: 'Tool',
        todayTraffic: 567,
        weekTraffic: 3456,
        bounceRate: 15,
        avgTime: '8m 12s',
        trend: 5.4,
        status: 'Growing',
        lastUpdated: '5 hours ago',
        topSources: [
            { name: 'Google', percentage: 80 },
            { name: 'Direct', percentage: 15 },
            { name: 'Referral', percentage: 5 }
        ],
        dailyData: [45, 52, 48, 55, 60, 58, 62]
    },
    {
        id: 5,
        title: 'Contact Us Page',
        url: '/contact',
        type: 'Contact',
        todayTraffic: 123,
        weekTraffic: 987,
        bounceRate: 65,
        avgTime: '1m 23s',
        trend: -12.1,
        status: 'Poor Performance',
        lastUpdated: '1 week ago',
        topSources: [
            { name: 'Direct', percentage: 60 },
            { name: 'Google', percentage: 25 },
            { name: 'Social', percentage: 15 }
        ],
        dailyData: [25, 22, 18, 15, 12, 14, 16]
    }
])

// Traffic sources overview
const trafficSources = ref([
    { name: 'Search', percentage: 45, traffic: 12456, iconClass: 'fas fa-search text-blue-600' },
    { name: 'Direct', percentage: 25, traffic: 6890, iconClass: 'fas fa-globe text-green-600' },
    { name: 'Social', percentage: 20, traffic: 5523, iconClass: 'fas fa-share-alt text-purple-600' },
    { name: 'Email', percentage: 10, traffic: 2767, iconClass: 'fas fa-envelope text-orange-600' }
])

// Performance alerts
const performanceAlerts = ref([
    {
        id: 1,
        type: 'warning',
        title: 'High Bounce Rate Alert',
        description: 'Contact page has 65% bounce rate',
        icon: 'fas fa-exclamation-triangle'
    },
    {
        id: 2,
        type: 'success',
        title: 'Traffic Spike',
        description: 'Product page up 22% this week',
        icon: 'fas fa-arrow-up'
    },
    {
        id: 3,
        type: 'info',
        title: 'SEO Opportunity',
        description: '3 pages ready for optimization',
        icon: 'fas fa-lightbulb'
    }
])

// Optimization opportunities
const optimizationOpportunities = ref([
    {
        id: 1,
        title: 'Improve Contact Page UX',
        description: 'High bounce rate suggests UX issues. Consider adding clear CTAs and reducing form fields.',
        potentialGain: 35,
        effort: 'Medium'
    },
    {
        id: 2,
        title: 'Add Internal Links to SEO Guide',
        description: 'Link to related articles to increase session duration and reduce bounce rate.',
        potentialGain: 18,
        effort: 'Low'
    },
    {
        id: 3,
        title: 'Optimize Landing Page Loading Speed',
        description: 'Page speed affects both SEO and conversion rates. Current load time: 3.2s',
        potentialGain: 25,
        effort: 'High'
    }
])

// Performance insights
const performanceInsights = ref([
    {
        id: 1,
        title: 'Peak Traffic Hours',
        description: 'Most traffic occurs between 2-4 PM. Consider scheduling content updates during this time.',
        metric: '2-4 PM peak',
        icon: 'fas fa-clock'
    },
    {
        id: 2,
        title: 'Mobile vs Desktop',
        description: '68% of traffic comes from mobile devices. Ensure mobile optimization is prioritized.',
        metric: '68% mobile',
        icon: 'fas fa-mobile-alt'
    },
    {
        id: 3,
        title: 'Content Performance',
        description: 'Blog posts generate 3x more engagement than product pages on average.',
        metric: '3x engagement',
        icon: 'fas fa-chart-bar'
    }
])

// Computed properties
const filteredLinks = computed(() => {
    let filtered = allLinks.value

    // Apply search filter
    if (searchQuery.value) {
        filtered = filtered.filter(link =>
            link.title.toLowerCase().includes(searchQuery.value.toLowerCase()) ||
            link.url.toLowerCase().includes(searchQuery.value.toLowerCase())
        )
    }

    // Apply sorting
    switch (sortBy.value) {
        case 'traffic':
            filtered.sort((a, b) => b.weekTraffic - a.weekTraffic)
            break
        case 'bounce':
            filtered.sort((a, b) => b.bounceRate - a.bounceRate)
            break
        case 'conversion':
            filtered.sort((a, b) => a.bounceRate - b.bounceRate) // Lower bounce = better conversion
            break
        case 'recent':
            filtered.sort((a, b) => new Date(b.lastUpdated) - new Date(a.lastUpdated))
            break
    }

    return filtered
})

// Methods
const getTotalTraffic = () => {
    return allLinks.value.reduce((sum, link) => sum + link.weekTraffic, 0)
}

const getNewLinksCount = () => {
    return allLinks.value.filter(link => link.lastUpdated.includes('hours') || link.lastUpdated.includes('day')).length
}

const getAverageBounceRate = () => {
    const total = allLinks.value.reduce((sum, link) => sum + link.bounceRate, 0)
    return Math.round(total / allLinks.value.length)
}

const getTopPerformer = () => {
    const top = allLinks.value.reduce((best, link) =>
        link.weekTraffic > best.weekTraffic ? link : best
    )
    const totalTraffic = getTotalTraffic()
    return {
        title: top.title,
        percentage: Math.round((top.weekTraffic / totalTraffic) * 100)
    }
}

const formatNumber = (num) => {
    if (num >= 1000000) return (num / 1000000).toFixed(1) + 'M'
    if (num >= 1000) return (num / 1000).toFixed(1) + 'K'
    return num.toString()
}

const getTrendClass = (trend) => {
    if (trend > 0) return 'text-green-600'
    if (trend < 0) return 'text-red-600'
    return 'text-gray-600'
}

const getBounceRateColor = (rate) => {
    if (rate <= 30) return 'bg-green-500'
    if (rate <= 50) return 'bg-yellow-500'
    return 'bg-red-500'
}

const getBounceRateTextColor = (rate) => {
    if (rate <= 30) return 'text-green-600'
    if (rate <= 50) return 'text-yellow-600'
    return 'text-red-600'
}

const getPageTypeIcon = (type) => {
    const icons = {
        'Blog Post': 'bg-blue-100 text-blue-600',
        'Landing Page': 'bg-green-100 text-green-600',
        'Product Page': 'bg-purple-100 text-purple-600',
        'Tool': 'bg-orange-100 text-orange-600',
        'Contact': 'bg-gray-100 text-gray-600'
    }
    return icons[type] || 'bg-gray-100 text-gray-600'
}

const getTypeClass = (type) => {
    const classes = {
        'Blog Post': 'bg-blue-100 text-blue-800',
        'Landing Page': 'bg-green-100 text-green-800',
        'Product Page': 'bg-purple-100 text-purple-800',
        'Tool': 'bg-orange-100 text-orange-800',
        'Contact': 'bg-gray-100 text-gray-800'
    }
    return classes[type] || 'bg-gray-100 text-gray-800'
}

const getSourceIcon = (sourceName) => {
    const icons = {
        'Google': 'bg-blue-100 text-blue-600',
        'Direct': 'bg-green-100 text-green-600',
        'Social': 'bg-purple-100 text-purple-600',
        'Email': 'bg-orange-100 text-orange-600',
        'Referral': 'bg-yellow-100 text-yellow-600'
    }
    return icons[sourceName] || 'bg-gray-100 text-gray-600'
}

const getStatusClass = (status) => {
    const classes = {
        'Excellent': 'bg-green-100 text-green-800',
        'Performing Well': 'bg-blue-100 text-blue-800',
        'Growing': 'bg-indigo-100 text-indigo-800',
        'Needs Attention': 'bg-yellow-100 text-yellow-800',
        'Poor Performance': 'bg-red-100 text-red-800'
    }
    return classes[status] || 'bg-gray-100 text-gray-800'
}

const getStatusDot = (status) => {
    const dots = {
        'Excellent': 'bg-green-500',
        'Performing Well': 'bg-blue-500',
        'Growing': 'bg-indigo-500',
        'Needs Attention': 'bg-yellow-500',
        'Poor Performance': 'bg-red-500'
    }
    return dots[status] || 'bg-gray-500'
}

const getAlertClass = (type) => {
    const classes = {
        'warning': 'bg-yellow-50 border-yellow-500',
        'success': 'bg-green-50 border-green-500',
        'info': 'bg-blue-50 border-blue-500',
        'error': 'bg-red-50 border-red-500'
    }
    return classes[type] || 'bg-gray-50 border-gray-500'
}

// Action methods
const exportData = () => {
    console.log('Exporting link analytics data...')
    // Implementation for data export
    // Example: Generate CSV or PDF report
    const csvData = allLinks.value.map(link => ({
        title: link.title,
        url: link.url,
        type: link.type,
        todayTraffic: link.todayTraffic,
        weekTraffic: link.weekTraffic,
        bounceRate: link.bounceRate,
        avgTime: link.avgTime,
        trend: link.trend,
        status: link.status
    }))

    // Convert to CSV and download
    const csv = convertToCSV(csvData)
    downloadCSV(csv, 'link-analytics-report.csv')
}

const viewDetails = (link) => {
    console.log('Viewing details for:', link.title)
    // Implementation for detailed view
    // Could open a modal or navigate to detailed analytics page
}

const optimizeLink = (link) => {
    console.log('Optimizing link:', link.title)
    // Implementation for optimization suggestions
    // Could open optimization wizard or show specific recommendations
}

const shareLink = (link) => {
    console.log('Sharing link:', link.title)
    // Implementation for sharing functionality
    // Could copy link to clipboard or open sharing options
    navigator.clipboard.writeText(link.url)
}

const implementOptimization = (opportunity) => {
    console.log('Implementing optimization:', opportunity.title)
    // Implementation for applying optimization
    // Could guide user through optimization steps
}

// Helper functions for export
const convertToCSV = (data) => {
    const headers = Object.keys(data[0]).join(',')
    const rows = data.map(row => Object.values(row).join(','))
    return [headers, ...rows].join('\n')
}

const downloadCSV = (csv, filename) => {
    const blob = new Blob([csv], { type: 'text/csv' })
    const url = window.URL.createObjectURL(blob)
    const a = document.createElement('a')
    a.href = url
    a.download = filename
    a.click()
    window.URL.revokeObjectURL(url)
}

// Chart initialization
const initMiniCharts = () => {
    // This would initialize small sparkline charts for each link
    // Requires Chart.js or similar charting library
    allLinks.value.forEach(link => {
        const canvas = document.querySelector(`[data-chart="${link.id}"]`)
        if (canvas) {
            // Initialize mini chart with link.dailyData
            console.log(`Initializing chart for link ${link.id}`)

            // Example Chart.js implementation:
            /*
            const ctx = canvas.getContext('2d')
            new Chart(ctx, {
              type: 'line',
              data: {
                labels: ['D1', 'D2', 'D3', 'D4', 'D5', 'D6', 'D7'],
                datasets: [{
                  data: link.dailyData,
                  borderColor: link.trend > 0 ? '#10b981' : '#ef4444',
                  backgroundColor: link.trend > 0 ? 'rgba(16, 185, 129, 0.1)' : 'rgba(239, 68, 68, 0.1)',
                  borderWidth: 2,
                  tension: 0.4,
                  pointRadius: 0,
                  fill: true
                }]
              },
              options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: {
                  x: { display: false },
                  y: { display: false }
                },
                elements: {
                  point: { radius: 0 }
                }
              }
            })
            */
        }
    })
}

// Lifecycle hooks
onMounted(() => {
    initMiniCharts()
})
</script>

<style scoped>
/* Additional custom styles if needed beyond Tailwind */
.font-sans {
    font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
}

/* Table hover effects */
tbody tr:hover {
    background-color: #f9fafb;
}

/* Sparkline chart containers */
canvas {
    display: block;
}

/* Custom scrollbar for table */
.overflow-x-auto::-webkit-scrollbar {
    height: 8px;
}

.overflow-x-auto::-webkit-scrollbar-track {
    background: #f1f5f9;
}

.overflow-x-auto::-webkit-scrollbar-thumb {
    background: #cbd5e1;
    border-radius: 4px;
}

.overflow-x-auto::-webkit-scrollbar-thumb:hover {
    background: #94a3b8;
}

/* Animation for progress bars */
.transition-all {
    transition: all 0.3s ease;
}

/* Custom animation for trending items */
@keyframes pulse {

    0%,
    100% {
        opacity: 1;
    }

    50% {
        opacity: 0.7;
    }
}

.animate-pulse {
    animation: pulse 2s infinite;
}

/* Tooltip styles */
.tooltip {
    position: relative;
}

.tooltip:hover::after {
    content: attr(data-tooltip);
    position: absolute;
    bottom: 100%;
    left: 50%;
    transform: translateX(-50%);
    background: #1f2937;
    color: white;
    padding: 0.5rem;
    border-radius: 0.375rem;
    font-size: 0.75rem;
    white-space: nowrap;
    z-index: 1000;
}

/* Loading states */
.loading {
    opacity: 0.5;
    pointer-events: none;
}

/* Card animations */
.card-enter-active,
.card-leave-active {
    transition: all 0.3s ease;
}

.card-enter-from,
.card-leave-to {
    opacity: 0;
    transform: translateY(20px);
}
</style>
