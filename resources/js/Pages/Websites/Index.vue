<template>
    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-2xl font-semibold text-gray-900">Analytics</h2>
                    <p class="text-gray-500 text-sm mt-0.5">Track your website performance</p>
                </div>
                <div class="flex items-center gap-2">
                    <button
                        class="px-4 py-2 bg-blue-600 text-white text-sm rounded-lg hover:bg-blue-700 transition-colors">
                        Add Website
                    </button>
                </div>
            </div>
        </template>

        <div class="space-y-6">
            <!-- Filters -->
            <div class="bg-white rounded-lg p-4 border border-gray-100">
                <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
                    <!-- Date Filter -->
                    <div class="flex items-center gap-3">
                        <label class="text-sm font-medium text-gray-700">Period:</label>
                        <div class="flex gap-1 p-1 bg-gray-50 rounded-lg border">
                            <button v-for="period in dateFilters" :key="period.value"
                                @click="selectedDateFilter = period.value" :class="[
                                    'px-3 py-1.5 text-sm rounded-md transition-all whitespace-nowrap',
                                    selectedDateFilter === period.value
                                        ? 'bg-blue-600 text-gray-100 shadow-sm'
                                        : 'text-gray-500 hover:text-gray-700'
                                ]">
                                {{ period.label }}
                            </button>
                        </div>

                        <!-- Custom Date Range -->
                        <div v-if="selectedDateFilter === 'custom'" class="flex items-center gap-2 ml-2">
                            <input type="date" v-model="customDateRange.start"
                                class="px-3 py-1.5 text-sm border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <span class="text-gray-400">to</span>
                            <input type="date" v-model="customDateRange.end"
                                class="px-3 py-1.5 text-sm border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>
                    </div>

                    <!-- Sort Filter -->
                    <div class="flex items-center gap-3">
                        <label class="text-sm font-medium text-gray-700">Sort by:</label>
                        <select v-model="sortBy"
                            class="px-4 py-2 text-sm border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="traffic">Most Traffic</option>
                            <option value="clicks">Most Clicked</option>
                            <option value="growth">Highest Growth</option>
                            <option value="ctr">Best CTR</option>
                            <option value="recent">Recently Added</option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- Summary Stats -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div class="bg-white rounded-lg p-4 border border-gray-100">
                    <p class="text-sm text-gray-500">Total Domains</p>
                    <p class="text-2xl font-semibold text-gray-900 mt-1">{{ filteredDomains.length }}</p>
                    <p class="text-xs text-gray-400 mt-1">in selected period</p>
                </div>
                <div class="bg-white rounded-lg p-4 border border-gray-100">
                    <p class="text-sm text-gray-500">Total Traffic</p>
                    <p class="text-2xl font-semibold text-gray-900 mt-1">{{ totalTraffic.toLocaleString() }}</p>
                    <p class="text-xs text-green-600 mt-1">↑ {{ averageGrowth.traffic }}%</p>
                </div>
                <div class="bg-white rounded-lg p-4 border border-gray-100">
                    <p class="text-sm text-gray-500">Total Clicks</p>
                    <p class="text-2xl font-semibold text-gray-900 mt-1">{{ totalClicks.toLocaleString() }}</p>
                    <p class="text-xs text-green-600 mt-1">↑ {{ averageGrowth.seo }}%</p>
                </div>
                <div class="bg-white rounded-lg p-4 border border-gray-100">
                    <p class="text-sm text-gray-500">Avg. CTR</p>
                    <p class="text-2xl font-semibold text-gray-900 mt-1">{{ averageCTR }}%</p>
                    <p class="text-xs text-red-600 mt-1">↓ 0.2%</p>
                </div>
            </div>

            <!-- Domains List -->
            <div class="bg-white rounded-lg border border-gray-100 overflow-hidden">
                <!-- Table Header -->
                <div class="px-6 py-3 border-b border-gray-100 bg-gray-50">
                    <div class="grid grid-cols-12 gap-4 text-xs font-medium text-gray-500 uppercase tracking-wider">
                        <div class="col-span-3">Domain</div>
                        <div class="col-span-2 text-right">Traffic</div>
                        <div class="col-span-2 text-right">SEO Clicks</div>
                        <div class="col-span-2 text-right">Impressions</div>
                        <div class="col-span-2 text-right">CTR</div>
                        <div class="col-span-1 text-right">Status</div>
                    </div>
                </div>

                <!-- Domain Rows -->
                <div v-if="sortedDomains.length > 0">
                    <div v-for="domain in sortedDomains" :key="domain.id"
                        class="px-6 py-4 hover:bg-gray-50 transition-colors border-b border-gray-100 last:border-b-0">
                        <div class="grid grid-cols-12 gap-4 items-center">
                            <!-- Domain -->
                            <div class="col-span-3">
                                <div class="flex items-center gap-3">
                                    <div
                                        class="w-10 h-10 bg-blue-50 rounded-lg flex items-center justify-center flex-shrink-0">
                                        <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9">
                                            </path>
                                        </svg>
                                    </div>
                                    <div>
                                        <p class="font-medium text-gray-900">{{ domain.name }}</p>
                                        <p class="text-xs text-gray-500">Added {{ formatDate(domain.created_at) }}</p>

                                    </div>
                                </div>
                            </div>

                            <!-- Traffic Details Button -->
                            <div class="col-span-2 text-right">
                                <p class="font-medium text-gray-900">{{ domain.traffic.current.toLocaleString() }}</p>
                                <div class="flex items-center justify-end gap-1 mt-1">
                                    <span v-if="domain.traffic.growth >= 0" class="text-xs text-green-600">
                                        ↑ {{ domain.traffic.growth }}%
                                    </span>
                                    <span v-else class="text-xs text-red-600">
                                        ↓ {{ Math.abs(domain.traffic.growth) }}%
                                    </span>
                                    <span class="text-xs text-gray-400">vs last period</span>
                                </div>
                                <Link href="/analytics"
                                    class="inline-flex items-center text-xs font-medium text-white bg-blue-600 hover:bg-blue-700 px-3 py-1.5 rounded shadow">
                                    View Traffic
                                    <svg class="w-3.5 h-3.5 ml-1" fill="none" stroke="currentColor" stroke-width="2"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
                                    </svg>
                                </Link>
                            </div>

                            <!-- SEO Details Button -->
                            <div class="col-span-2 text-right">
                                <p class="font-medium text-gray-900">{{ domain.seo.clicks.toLocaleString() }}</p>
                                <div class="flex items-center justify-end gap-1 mt-1">
                                    <span v-if="domain.seo.growth >= 0" class="text-xs text-green-600">
                                        ↑ {{ domain.seo.growth }}%
                                    </span>
                                    <span v-else class="text-xs text-red-600">
                                        ↓ {{ Math.abs(domain.seo.growth) }}%
                                    </span>
                                    <span class="text-xs text-gray-400">vs last period</span>
                                </div>
                                <Link :href="`/analytics/seo/${domain.id}`"
                                    class="inline-flex items-center text-xs font-medium text-white bg-green-600 hover:bg-green-700 px-3 py-1.5 rounded shadow">
                                    View SEO
                                    <svg class="w-3.5 h-3.5 ml-1" fill="none" stroke="currentColor" stroke-width="2"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
                                    </svg>
                                </Link>
                            </div>

                            <!-- Impressions -->
                            <div class="col-span-2 text-right">
                                <p class="font-medium text-gray-900">{{ domain.seo.impressions.toLocaleString() }}</p>
                                <div class="text-xs text-gray-500 mt-1">
                                    {{ formatNumber(domain.seo.impressions) }}
                                </div>
                            </div>

                            <!-- CTR -->
                            <div class="col-span-2 text-right">
                                <p class="font-medium text-gray-900">{{ domain.seo.ctr }}%</p>
                                <div class="w-full bg-gray-200 rounded-full h-1.5 mt-2">
                                    <div class="bg-blue-600 h-1.5 rounded-full transition-all duration-300"
                                        :style="{ width: `${Math.min(domain.seo.ctr * 10, 100)}%` }">
                                    </div>
                                </div>
                            </div>

                            <!-- Status -->
                            <div class="col-span-1 text-right">
                                <span v-if="domain.status === 'active'"
                                    class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-50 text-green-700">
                                    Active
                                </span>
                                <span v-else
                                    class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-gray-50 text-gray-700">
                                    Inactive
                                </span>
                            </div>
                        </div>

                        <!-- Mini Chart (expandable) -->
                        <div v-if="expandedDomain === domain.id" class="mt-4 pt-4 border-t border-gray-100">
                            <div class="grid grid-cols-2 gap-4">
                                <div class="bg-gray-50 rounded-lg p-4">
                                    <p class="text-sm font-medium text-gray-700 mb-3">Traffic Trend (Last 7 days)</p>
                                    <div style="height: 100px;">
                                        <Line :data="getChartData(domain.traffic.trend, 'Traffic')"
                                            :options="miniChartOptions" />
                                    </div>
                                </div>
                                <div class="bg-gray-50 rounded-lg p-4">
                                    <p class="text-sm font-medium text-gray-700 mb-3">SEO Trend (Last 7 days)</p>
                                    <div style="height: 100px;">
                                        <Line :data="getChartData(domain.seo.trend, 'Clicks')"
                                            :options="miniChartOptions" />
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Expand/Collapse Button -->
                        <button @click="toggleExpand(domain.id)"
                            class="mt-2 text-xs text-blue-600 hover:text-blue-700 transition-colors">
                            {{ expandedDomain === domain.id ? 'Hide Details' : 'Show Details' }}
                        </button>
                    </div>
                </div>

                <!-- Empty State -->
                <div v-else class="px-6 py-12 text-center">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9">
                        </path>
                    </svg>
                    <h3 class="mt-2 text-sm font-medium text-gray-900">No domains added</h3>
                    <p class="mt-1 text-sm text-gray-500">Get started by adding your first domain.</p>
                    <button
                        class="mt-4 px-4 py-2 bg-blue-600 text-white text-sm rounded-lg hover:bg-blue-700 transition-colors">
                        Add Your First Domain
                    </button>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<script setup>
import { ref, computed, onMounted, watch } from 'vue';
import axios from 'axios';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Line } from 'vue-chartjs';
import { Link } from '@inertiajs/vue3';
import {
    Chart as ChartJS,
    CategoryScale,
    LinearScale,
    PointElement,
    LineElement,
    Title,
    Tooltip,
    Legend,
    Filler
} from 'chart.js';

ChartJS.register(CategoryScale, LinearScale, PointElement, LineElement, Title, Tooltip, Legend, Filler);

const expandedDomain = ref(null);
const selectedDateFilter = ref('thisMonth');
const sortBy = ref('clicks');
const customDateRange = ref({ start: '', end: '' });
const loading = ref(false);
const filteredDomains = ref([]);
const sortedDomains = ref([]);

onMounted(async () => {
    loading.value = true;
    try {
        const res = await axios.get('/websites/analytics');
        filteredDomains.value = res.data.filteredDomains;
        sortedDomains.value = res.data.sortedDomains;
    } catch (e) {
        console.error('Error loading analytics:', e);
    } finally {
        loading.value = false;
    }
});

const dateFilters = [
    { label: 'Today', value: 'today' },
    { label: 'This Week', value: 'thisWeek' },
    { label: 'This Month', value: 'thisMonth' },
    { label: 'Last Month', value: 'lastMonth' },
    { label: 'Last 3 Months', value: 'last3Months' },
    { label: 'Custom Range', value: 'custom' }
];

watch(selectedDateFilter, async (newVal) => {
    loading.value = true;

    // Example fetch call
    const response = await axios.get(`/websites/analytics`, {
        params: { period: newVal }
    });

    filteredDomains.value = response.data.filteredDomains;
    sortedDomains.value = response.data.sortedDomains;

    loading.value = false;
});

const totalTraffic = computed(() => {
    return filteredDomains.value.reduce((sum, domain) => sum + domain.traffic.current, 0);
});

const totalClicks = computed(() => {
    return filteredDomains.value.reduce((sum, domain) => sum + domain.seo.clicks, 0);
});

const averageGrowth = computed(() => {
    const trafficGrowth = filteredDomains.value.reduce((sum, domain) => sum + domain.traffic.growth, 0) / (filteredDomains.value.length || 1);
    const seoGrowth = filteredDomains.value.reduce((sum, domain) => sum + domain.seo.growth, 0) / (filteredDomains.value.length || 1);
    return {
        traffic: trafficGrowth.toFixed(1),
        seo: seoGrowth.toFixed(1)
    };
});

const averageCTR = computed(() => {
    const totalCTR = filteredDomains.value.reduce((sum, domain) => sum + domain.seo.ctr, 0);
    return (totalCTR / (filteredDomains.value.length || 1)).toFixed(1);
});

const toggleExpand = (domainId) => {
    expandedDomain.value = expandedDomain.value === domainId ? null : domainId;
};

const formatDate = (date) => {
    return new Date(date).toLocaleDateString('en-US', {
        month: 'short',
        day: 'numeric',
        year: 'numeric'
    });
};

const formatNumber = (num) => {
    if (num >= 1000000) {
        return (num / 1000000).toFixed(1) + 'M';
    } else if (num >= 1000) {
        return (num / 1000).toFixed(1) + 'K';
    }
    return num.toString();
};

const getChartData = (trend, label) => {
    return {
        labels: ['Day 1', 'Day 2', 'Day 3', 'Day 4', 'Day 5', 'Day 6', 'Day 7'],
        datasets: [{
            label: label,
            data: trend,
            borderColor: '#2563eb',
            backgroundColor: 'rgba(37, 99, 235, 0.1)',
            fill: true,
            pointBackgroundColor: '#2563eb',
            pointBorderColor: '#fff',
            pointBorderWidth: 2
        }]
    };
};

const miniChartOptions = {
    responsive: true,
    maintainAspectRatio: false,
    plugins: {
        legend: { display: false },
        tooltip: {
            enabled: true,
            backgroundColor: 'rgba(0, 0, 0, 0.8)',
            titleColor: 'white',
            bodyColor: 'white',
            padding: 8,
            displayColors: false,
            callbacks: {
                label: (context) => context.parsed.y.toLocaleString()
            }
        }
    },
    scales: {
        x: {
            display: true,
            grid: { display: false },
            ticks: { display: false }
        },
        y: {
            display: true,
            grid: { borderDash: [2, 2], color: 'rgba(0, 0, 0, 0.05)' },
            ticks: { display: false }
        }
    },
    elements: {
        line: { borderWidth: 2, tension: 0.4 },
        point: {
            radius: 3,
            hoverRadius: 5,
            backgroundColor: '#2563eb',
            borderColor: '#2563eb'
        }
    }
};
</script>

<style scoped>
/* Additional scoped styling if needed */
</style>

<style scoped>
/* No additional styles needed */
</style>
