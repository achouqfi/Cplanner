<template>
    <AuthenticatedLayout>
        <div class="bg-white rounded-lg shadow-sm">
            <div class="p-6 border-b border-gray-200">
                <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
                    <div>
                        <h2 class="text-xl font-semibold text-gray-900">Page Analytics</h2>
                        <p class="text-sm text-gray-500 mt-1">Detailed performance metrics for all pages</p>
                    </div>
                    <div class="flex flex-wrap items-center gap-3">
                        <select v-model="sortBy" @change="fetchAnalytics"
                            class="px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                            <option value="pageviews">Most Page Views</option>
                            <option value="sessions">Most Sessions</option>
                            <option value="revenue">Highest Revenue</option>
                            <option value="events">Most Events</option>
                            <option value="users">Most Users</option>
                            <option value="bounceRate">Bounce Rate</option>
                            <option value="avgTime">Avg. Time on Page</option>
                        </select>
                        <select v-model="selectedCountry" @change="fetchAnalytics"
                            class="px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                            <option value="all">All Countries</option>
                            <option v-for="country in availableCountries" :key="country.code" :value="country.code">
                                {{ country.flag }} {{ country.name }}
                            </option>
                        </select>
                        <select v-model="selectedDateRange" @change="fetchAnalytics">
                            <option v-for="range in dateFilters" :key="range.value" :value="range.value">
                                {{ range.label }}
                            </option>
                        </select>
                        <input type="text" v-model="searchQuery" @input="fetchAnalytics" placeholder="Search pages..."
                            class="pl-3 pr-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                    </div>
                </div>
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mt-4">
                    <div class="bg-gray-50 rounded-lg p-3">
                        <p class="text-xs text-gray-500">Total Pages</p>
                        <p class="text-lg font-semibold text-gray-900">{{ meta.total }}</p>
                    </div>
                    <div class="bg-blue-50 rounded-lg p-3">
                        <p class="text-xs text-gray-500">Total Page Views</p>
                        <p class="text-lg font-semibold text-blue-900">{{ totalPageViews.toLocaleString() }}</p>
                    </div>
                    <div class="bg-green-50 rounded-lg p-3">
                        <p class="text-xs text-gray-500">Total Revenue</p>
                        <p class="text-lg font-semibold text-green-900">${{ totalRevenue.toLocaleString() }}</p>
                    </div>
                    <div class="bg-purple-50 rounded-lg p-3">
                        <p class="text-xs text-gray-500">Avg. Bounce Rate</p>
                        <p class="text-lg font-semibold text-purple-900">{{ avgBounceRate }}%</p>
                    </div>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50 border-b border-gray-200">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Page</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Page Views</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Sessions</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Users</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Events</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Bounce Rate</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Avg. Time</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <tr v-for="page in pages" :key="page.path">
                            <td class="px-6 py-4 text-sm">{{ page.path }}</td>
                            <td class="px-6 py-4 text-right text-sm">{{ page.pageviews }}</td>
                            <td class="px-6 py-4 text-right text-sm">{{ page.sessions }}</td>
                            <td class="px-6 py-4 text-right text-sm">{{ page.users }}</td>
                            <td class="px-6 py-4 text-right text-sm">{{ page.events }}</td>
                            <td class="px-6 py-4 text-right text-sm">{{ page.bounceRate }}%</td>
                            <td class="px-6 py-4 text-right text-sm">{{ page.avgTime }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div class="px-6 py-4 border-t border-gray-200">
                <div class="flex items-center justify-between">
                    <div class="text-sm text-gray-700">
                        Showing page {{ currentPage }} of {{ totalPages }}
                    </div>
                    <div class="space-x-2">
                        <button @click="currentPage--" :disabled="currentPage === 1"
                            class="px-3 py-1 text-sm border rounded disabled:opacity-50">Previous</button>
                        <button @click="currentPage++" :disabled="currentPage === totalPages"
                            class="px-3 py-1 text-sm border rounded disabled:opacity-50">Next</button>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<script setup>
import { ref, computed, onMounted, watch } from 'vue';
import axios from 'axios';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';

const pages = ref([]);
const meta = ref({ total: 0 });
const currentPage = ref(1);
const perPage = ref(15);
const sortBy = ref('pageviews');
const selectedCountry = ref('all');
const searchQuery = ref('');
const dateRange = ref(30);

const selectedDateRange = ref(30) // default 30 days

const dateFilters = [
    { label: 'Last 7 Days', value: 7 },
    { label: 'Last 30 Days', value: 30 },
    { label: 'Last 90 Days', value: 90 }
]

const totalPages = computed(() => Math.ceil(meta.value.total / perPage.value));

const availableCountries = ref([
    { code: 'US', name: 'United States', flag: '🇺🇸' },
    { code: 'UK', name: 'United Kingdom', flag: '🇬🇧' },
    { code: 'CA', name: 'Canada', flag: '🇨🇦' },
    { code: 'AU', name: 'Australia', flag: '🇦🇺' },
    { code: 'DE', name: 'Germany', flag: '🇩🇪' },
    { code: 'FR', name: 'France', flag: '🇫🇷' },
    { code: 'JP', name: 'Japan', flag: '🇯🇵' },
    { code: 'IN', name: 'India', flag: '🇮🇳' }
]);

const fetchAnalytics = async () => {
    try {
        const response = await axios.get('/analytics/pages-analytics', {
            params: {
                sort: sortBy.value,
                country: selectedCountry.value,
                search: searchQuery.value,
                page: currentPage.value,
                per_page: perPage.value,
                date_range: selectedDateRange.value
            }
        });
        pages.value = response.data.data;
        meta.value = response.data.meta;
    } catch (err) {
        console.error('Failed to fetch analytics:', err);
    }
};

onMounted(fetchAnalytics);
watch([sortBy, selectedCountry, searchQuery, currentPage, dateRange], fetchAnalytics);

const totalPageViews = computed(() => pages.value.reduce((acc, p) => acc + p.pageviews, 0));
const totalRevenue = computed(() => pages.value.reduce((acc, p) => acc + p.revenue, 0));
const avgBounceRate = computed(() => {
    if (pages.value.length === 0) return 0;
    const total = pages.value.reduce((sum, p) => sum + p.bounceRate, 0);
    return (total / pages.value.length).toFixed(1);
});
</script>

<style scoped>
@import url('https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css');
</style>
