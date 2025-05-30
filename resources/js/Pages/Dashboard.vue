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

        <div class="space-y-6">
            <!-- Controls -->
            <div class="bg-white rounded-lg p-4 border border-gray-100">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                    <select v-model="selectedSite"
                        class="px-4 py-2 text-sm border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-gray-200 focus:border-transparent">
                        <option disabled value="null">Select website</option>
                        <option v-for="site in trackedWebsites" :key="site.id" :value="site">
                            {{ site.domain }}
                        </option>
                    </select>

                    <div class="flex gap-1 p-1 bg-gray-50 rounded-lg">
                        <button v-for="range in timeRanges" :key="range.value" @click="selectedTimeRange = range.value"
                            :class="[
                                'px-3 py-1.5 text-sm rounded-md transition-all',
                                selectedTimeRange === range.value
                                    ? 'bg-white text-gray-900 shadow-sm'
                                    : 'text-gray-500 hover:text-gray-700'
                            ]">
                            {{ range.label }}
                        </button>
                    </div>
                </div>
            </div>

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
