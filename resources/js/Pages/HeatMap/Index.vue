<template>
    <AuthenticatedLayout>
        <div class="min-h-screen bg-gray-50">
            <!-- Top Navigation Bar -->
            <div class="bg-white border-b border-gray-200 px-6 py-4">
                <div class="flex items-center justify-between">
                    <!-- Left Side - Navigation Tabs -->
                    <div class="flex items-center space-x-8">
                        <div class="flex items-center space-x-6">
                            <button class="flex items-center px-3 py-2 text-sm font-medium text-blue-600 bg-blue-50 rounded-lg border border-blue-200">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                                </svg>
                                Heatmaps
                            </button>
                            <button class="flex items-center px-3 py-2 text-sm font-medium text-gray-600 hover:text-gray-900">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                                Data
                            </button>
                            <button class="flex items-center px-3 py-2 text-sm font-medium text-gray-600 hover:text-gray-900">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z" />
                                </svg>
                                Insights
                            </button>
                        </div>
                    </div>

                    <!-- Right Side - Controls -->
                    <div class="flex items-center space-x-4">
                        <!-- Date Range Picker -->
                        <div class="flex items-center border border-gray-300 rounded-lg">
                            <button class="flex items-center px-3 py-2 text-sm text-gray-700 hover:bg-gray-50">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                                Last 30 Days
                            </button>
                        </div>

                        <!-- Filters -->
                        <button class="flex items-center px-3 py-2 text-sm text-gray-700 border border-gray-300 rounded-lg hover:bg-gray-50">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" />
                            </svg>
                            Filters
                        </button>

                        <!-- Interactive Mode -->
                        <button class="flex items-center px-3 py-2 text-sm text-gray-700 border border-gray-300 rounded-lg hover:bg-gray-50">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 15l-2 5L9 9l11 4-5 2zm0 0l5 5M7.188 2.239l.777 2.897M5.136 7.965l-2.898-.777M13.95 4.05l-2.122 2.122m-5.657 5.656l-2.12 2.122" />
                            </svg>
                            Interactive Mode
                        </button>

                        <!-- View Options -->
                        <div class="flex items-center space-x-2 text-sm text-gray-600">
                            <span class="flex items-center">
                                <div class="w-3 h-3 bg-blue-500 rounded mr-1"></div>
                                5.9K
                            </span>
                            <span class="flex items-center">
                                <div class="w-3 h-3 bg-purple-500 rounded mr-1"></div>
                                149.5K
                            </span>
                            <span class="flex items-center">
                                <div class="w-3 h-3 bg-blue-500 rounded mr-1"></div>
                                9.2K
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="flex">
                <!-- Left Sidebar -->
                <div class="w-80 bg-white border-r border-gray-200 h-screen overflow-y-auto">
                    <div class="p-4">
                        <!-- Search -->
                        <div class="relative mb-6">
                            <svg class="absolute left-3 top-1/2 transform -translate-y-1/2 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                            <input
                                type="text"
                                placeholder="Search"
                                class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                            >
                        </div>

                        <!-- Page List -->
                        <div class="space-y-1">
                            <div v-for="page in availablePages" :key="page.id"
                                @click="selectPage(page.id)"
                                :class="selectedPageId === page.id ? 'bg-blue-50 border-blue-200 text-blue-800' : 'hover:bg-gray-50'"
                                class="flex items-center justify-between p-3 rounded-lg border cursor-pointer transition-colors">
                                <div class="flex items-center">
                                    <svg class="w-4 h-4 mr-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1" />
                                    </svg>
                                    <span class="font-medium">{{ page.url }}</span>
                                </div>
                                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1" />
                                </svg>
                            </div>
                        </div>

                        <!-- Sort Options -->
                        <div class="mt-6">
                            <div class="flex items-center justify-between text-sm text-gray-600">
                                <span>Sort by:</span>
                                <select class="text-sm border-none focus:outline-none">
                                    <option>Sessions</option>
                                    <option>Clicks</option>
                                    <option>Revenue</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Main Content -->
                <div class="flex-1 relative">
                    <!-- Heatmap Type Toggle (Top Right) -->
                    <div class="absolute top-4 left-4 z-10 flex bg-white rounded-lg border border-gray-300 shadow-sm">
                        <button v-for="type in heatmapTypes" :key="type.value"
                            @click="activeHeatmapType = type.value"
                            :class="activeHeatmapType === type.value
                                ? 'bg-blue-600 text-white'
                                : 'text-gray-600 hover:text-gray-900 hover:bg-gray-50'"
                            class="px-4 py-2 text-sm font-medium first:rounded-l-lg last:rounded-r-lg transition-colors">
                            {{ type.label }}
                        </button>
                    </div>

                    <!-- Zoom Controls -->
                    <div class="absolute top-4 right-4 z-10 flex items-center bg-white rounded-lg border border-gray-300 shadow-sm">
                        <button @click="zoomOut" :disabled="zoomLevel <= 0.5"
                            class="p-2 text-gray-600 hover:text-gray-900 disabled:opacity-50 disabled:cursor-not-allowed first:rounded-l-lg">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4" />
                            </svg>
                        </button>
                        <span class="px-3 py-2 text-sm font-medium text-gray-700 border-x border-gray-300">
                            {{ Math.round(zoomLevel * 100) }}%
                        </span>
                        <button @click="zoomIn" :disabled="zoomLevel >= 2"
                            class="p-2 text-gray-600 hover:text-gray-900 disabled:opacity-50 disabled:cursor-not-allowed last:rounded-r-lg">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                            </svg>
                        </button>
                    </div>

                    <!-- Main Heatmap Canvas -->
                    <div class="h-screen overflow-auto bg-gray-100" ref="canvasContainer">
                        <div class="relative min-h-full flex items-start justify-center p-8">
                            <!-- Page Screenshot with Heatmap -->
                            <div class="relative bg-white shadow-lg rounded-lg overflow-hidden"
                                :style="{ transform: `scale(${zoomLevel})`, transformOrigin: 'top center' }">

                                <!-- Website Screenshot -->
                                <img :src="selectedPage.screenshot"
                                    :alt="selectedPage.title"
                                    class="block w-full h-auto"
                                    :style="{ width: Math.min(selectedPage.width * 0.8, 800) + 'px' }"
                                    @load="onImageLoad">

                                <!-- Heatmap Overlay -->
                                <div class="absolute inset-0" :style="{ opacity: heatmapOpacity }">
                                    <!-- Click Heatmap -->
                                    <div v-if="activeHeatmapType === 'clicks'" class="absolute inset-0">
                                        <div v-for="click in clickData" :key="click.id"
                                            class="absolute rounded-full pointer-events-auto cursor-pointer"
                                            :style="{
                                                left: (click.x * 0.8 - click.radius) + 'px',
                                                top: (click.y * 0.8 - click.radius) + 'px',
                                                width: (click.radius * 2 * 0.8) + 'px',
                                                height: (click.radius * 2 * 0.8) + 'px',
                                                background: getHeatmapColor(click.intensity),
                                                filter: 'blur(' + (click.radius * 0.3) + 'px)'
                                            }"
                                            @mouseenter="showTooltip($event, click)"
                                            @mouseleave="hideTooltip">
                                        </div>
                                    </div>

                                    <!-- Hover Heatmap -->
                                    <div v-if="activeHeatmapType === 'hovers'" class="absolute inset-0">
                                        <div v-for="hover in hoverData" :key="hover.id"
                                            class="absolute rounded-full pointer-events-auto cursor-pointer"
                                            :style="{
                                                left: (hover.x * 0.8 - hover.radius) + 'px',
                                                top: (hover.y * 0.8 - hover.radius) + 'px',
                                                width: (hover.radius * 2 * 0.8) + 'px',
                                                height: (hover.radius * 2 * 0.8) + 'px',
                                                background: getHoverColor(hover.intensity),
                                                filter: 'blur(' + (hover.radius * 0.4) + 'px)'
                                            }"
                                            @mouseenter="showTooltip($event, hover)"
                                            @mouseleave="hideTooltip">
                                        </div>
                                    </div>

                                    <!-- Scroll Depth Overlay -->
                                    <div v-if="activeHeatmapType === 'scroll'" class="absolute inset-0">
                                        <div v-for="zone in scrollZones" :key="zone.id"
                                            class="absolute w-full pointer-events-auto cursor-pointer"
                                            :style="{
                                                top: (zone.top * 0.8) + 'px',
                                                height: (zone.height * 0.8) + 'px',
                                                background: getScrollColor(zone.intensity),
                                                opacity: zone.intensity / 100
                                            }"
                                            @mouseenter="showScrollTooltip($event, zone)"
                                            @mouseleave="hideTooltip">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Tooltip -->
                    <div v-if="tooltip.visible"
                        class="fixed bg-gray-900 text-white px-4 py-3 rounded-lg text-sm shadow-xl z-50 pointer-events-none max-w-xs"
                        :style="{ left: tooltip.x + 'px', top: tooltip.y + 'px' }">
                        <div class="font-semibold text-white">{{ tooltip.title }}</div>
                        <div class="text-gray-300 mt-1">{{ tooltip.content }}</div>
                        <div v-if="tooltip.stats" class="text-xs text-gray-400 mt-2 pt-2 border-t border-gray-700">
                            {{ tooltip.stats }}
                        </div>
                    </div>

                    <!-- Stats Panel (Bottom Right) -->


                    <!-- Opacity Control (Bottom Left) -->
                    <div class="absolute bottom-4 left-4 z-10 bg-white rounded-lg border border-gray-300 shadow-sm p-4">
                        <div class="flex items-center space-x-3">
                            <span class="text-sm font-medium text-gray-700">Opacity</span>
                            <input type="range" v-model="heatmapOpacity" min="0.1" max="1" step="0.1"
                                class="w-24 h-2 bg-gray-200 rounded-lg appearance-none cursor-pointer">
                            <span class="text-sm text-gray-600 min-w-12">{{ Math.round(heatmapOpacity * 100) }}%</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';

// Reactive data
const selectedPageId = ref(1);
const activeHeatmapType = ref('clicks');
const heatmapOpacity = ref(0.6);
const zoomLevel = ref(1);
const selectedElement = ref(null);

// Sample data based on the reference image
const selectedElementData = {
    title: 'Product Gallery Image',
    clicks: 41,
    purchases: 2,
    revenue: 95.81,
    avgOrder: 47.91,
    conversionRate: 4.88,
    visibility: 100.00,
    revPerClick: 2.34,
    revPerSession: 0.23
};

// Page data
const availablePages = ref([
    {
        id: 1,
        title: 'Product Page',
        url: '/products/callie-sling-crossbody',
        screenshot: 'data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iMTAwMCIgaGVpZ2h0PSIxNDAwIiB2aWV3Qm94PSIwIDAgMTAwMCAxNDAwIiBmaWxsPSJub25lIiB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciPjxyZWN0IHdpZHRoPSIxMDAwIiBoZWlnaHQ9IjE0MDAiIGZpbGw9IiNGRkZGRkYiLz48cmVjdCB3aWR0aD0iMTAwMCIgaGVpZ2h0PSI4MCIgZmlsbD0iIzI1MjUyNSIvPjx0ZXh0IHg9IjUwMCIgeT0iNTAiIGZpbGw9IndoaXRlIiBmb250LXNpemU9IjE0IiB0ZXh0LWFuY2hvcj0ibWlkZGxlIj5GUkVFIFVTIFNISVBQSU5HPC90ZXh0PjxyZWN0IHg9IjUwIiB5PSIxMjAiIHdpZHRoPSI0MDAiIGhlaWdodD0iNDAwIiBmaWxsPSIjRjNGNEY2Ii8+PHRleHQgeD0iMjUwIiB5PSIzMzAiIGZpbGw9IiMzNzQxNTEiIGZvbnQtc2l6ZT0iMTgiIHRleHQtYW5jaG9yPSJtaWRkbGUiPlByb2R1Y3QgSW1hZ2U8L3RleHQ+PHJlY3QgeD0iNTUwIiB5PSIxNjAiIHdpZHRoPSI0MDAiIGhlaWdodD0iMzAwIiBmaWxsPSIjRkZGRkZGIi8+PHRleHQgeD0iNzUwIiB5PSIyMDAiIGZpbGw9IiMzNzQxNTEiIGZvbnQtc2l6ZT0iMjQiIHRleHQtYW5jaG9yPSJtaWRkbGUiPkNBTExJRSBTTElORy9DUk9TU0JPRFE8L3RleHQ+PHRleHQgeD0iNzUwIiB5PSIyNDAiIGZpbGw9IiMzNzQxNTEiIGZvbnQtc2l6ZT0iMjAiIHRleHQtYW5jaG9yPSJtaWRkbGUiPiQyMzUuMDA8L3RleHQ+PHJlY3QgeD0iNTUwIiB5PSI1MDAiIHdpZHRoPSIxNTAiIGhlaWdodD0iNDAiIGZpbGw9IiMzQjgyRjYiLz48dGV4dCB4PSI2MjUiIHk9IjUyNSIgZmlsbD0id2hpdGUiIGZvbnQtc2l6ZT0iMTQiIHRleHQtYW5jaG9yPSJtaWRkbGUiPkFERCBUTyBCQUc8L3RleHQ+PC9zdmc+',
        width: 1000,
        height: 1400
    },
    {
        id: 2,
        title: 'Collection Page',
        url: '/collections/crossbody',
        screenshot: 'data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iMTAwMCIgaGVpZ2h0PSIxMjAwIiB2aWV3Qm94PSIwIDAgMTAwMCAxMjAwIiBmaWxsPSJub25lIiB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciPjxyZWN0IHdpZHRoPSIxMDAwIiBoZWlnaHQ9IjEyMDAiIGZpbGw9IiNGRkZGRkYiLz48cmVjdCB3aWR0aD0iMTAwMCIgaGVpZ2h0PSI4MCIgZmlsbD0iIzI1MjUyNSIvPjx0ZXh0IHg9IjUwMCIgeT0iNTAiIGZpbGw9IndoaXRlIiBmb250LXNpemU9IjE0IiB0ZXh0LWFuY2hvcj0ibWlkZGxlIj5DUk9TU0JPRFkgQ09MTEVDVE9JTjwvdGV4dD48L3N2Zz4=',
        width: 1000,
        height: 1200
    },
    {
        id: 3,
        title: 'Homepage',
        url: '/',
        screenshot: 'data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iMTAwMCIgaGVpZ2h0PSIxNjAwIiB2aWV3Qm94PSIwIDAgMTAwMCAxNjAwIiBmaWxsPSJub25lIiB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciPjxyZWN0IHdpZHRoPSIxMDAwIiBoZWlnaHQ9IjE2MDAiIGZpbGw9IiNGRkZGRkYiLz48cmVjdCB3aWR0aD0iMTAwMCIgaGVpZ2h0PSI4MCIgZmlsbD0iIzI1MjUyNSIvPjx0ZXh0IHg9IjUwMCIgeT0iNTAiIGZpbGw9IndoaXRlIiBmb250LXNpemU9IjE0IiB0ZXh0LWFuY2hvcj0ibWlkZGxlIj5IT01FUEFHRTwvdGV4dD48L3N2Zz4=',
        width: 1000,
        height: 1600
    }
]);

const selectedPage = computed(() => {
    return availablePages.value.find(page => page.id === selectedPageId.value) || availablePages.value[0];
});

const heatmapTypes = ref([
    { value: 'clicks', label: 'Clicks' },
    { value: 'hovers', label: 'Hovers' },
    { value: 'scroll', label: 'Scroll' }
]);

// Sample heatmap data
const clickData = ref([
    { id: 1, x: 250, y: 320, radius: 40, intensity: 95, clicks: 41, element: 'Product Gallery Image' },
    { id: 2, x: 625, y: 520, radius: 35, intensity: 88, clicks: 127, element: 'Add to Bag Button' },
    { id: 3, x: 750, y: 200, radius: 30, intensity: 72, clicks: 89, element: 'Product Title' },
    { id: 4, x: 750, y: 240, radius: 25, intensity: 65, clicks: 56, element: 'Price' },
    { id: 5, x: 100, y: 50, radius: 20, intensity: 58, clicks: 34, element: 'Logo' },
    { id: 6, x: 900, y: 50, radius: 18, intensity: 45, clicks: 23, element: 'Search' },
    { id: 7, x: 700, y: 350, radius: 22, intensity: 52, clicks: 67, element: 'Color Options' },
    { id: 8, x: 500, y: 800, radius: 15, intensity: 35, clicks: 12, element: 'Product Details' },
]);

const hoverData = ref([
    { id: 1, x: 250, y: 320, radius: 60, intensity: 92, hovers: 234, duration: '4.2s' },
    { id: 2, x: 625, y: 520, radius: 45, intensity: 85, hovers: 189, duration: '3.8s' },
    { id: 3, x: 750, y: 350, radius: 40, intensity: 78, hovers: 156, duration: '3.1s' },
    { id: 4, x: 750, y: 200, radius: 35, intensity: 71, hovers: 134, duration: '2.9s' },
    { id: 5, x: 700, y: 350, radius: 30, intensity: 68, hovers: 123, duration: '2.5s' },
]);

const scrollZones = ref([
    { id: 1, top: 0, height: 150, intensity: 100, users: 1847, percentage: '100%' },
    { id: 2, top: 150, height: 150, intensity: 95, users: 1754, percentage: '95%' },
    { id: 3, top: 300, height: 150, intensity: 88, users: 1625, percentage: '88%' },
    { id: 4, top: 450, height: 150, intensity: 82, users: 1514, percentage: '82%' },
    { id: 5, top: 600, height: 150, intensity: 76, users: 1403, percentage: '76%' },
    { id: 6, top: 750, height: 150, intensity: 65, users: 1201, percentage: '65%' },
    { id: 7, top: 900, height: 150, intensity: 45, users: 831, percentage: '45%' },
    { id: 8, top: 1050, height: 150, intensity: 32, users: 591, percentage: '32%' },
    { id: 9, top: 1200, height: 200, intensity: 18, users: 332, percentage: '18%' },
]);

// Tooltip
const tooltip = ref({
    visible: false,
    x: 0,
    y: 0,
    title: '',
    content: '',
    stats: ''
});

// Methods
const selectPage = (pageId) => {
    selectedPageId.value = pageId;
    selectedElement.value = null;
};

const showElementStats = (element) => {
    selectedElement.value = {
        title: element.element,
        clicks: element.clicks,
        purchases: Math.floor(element.clicks * 0.048),
        revenue: (element.clicks * 2.34).toFixed(2),
        avgOrder: (47.91).toFixed(2),
        conversionRate: (4.88).toFixed(2),
        visibility: (100.00).toFixed(2),
        revPerClick: (2.34).toFixed(2),
        revPerSession: (0.23).toFixed(2)
    };
};

const zoomIn = () => {
    if (zoomLevel.value < 2) {
        zoomLevel.value = Math.min(2, zoomLevel.value + 0.25);
    }
};

const zoomOut = () => {
    if (zoomLevel.value > 0.5) {
        zoomLevel.value = Math.max(0.5, zoomLevel.value - 0.25);
    }
};

const onImageLoad = () => {
    console.log('Page screenshot loaded');
};

const getHeatmapColor = (intensity) => {
    // Create gradient from blue (low) to red (high) - more vivid colors
    const ratio = intensity / 100;
    if (ratio < 0.3) {
        return `radial-gradient(circle, rgba(59, 130, 246, ${0.4 + ratio * 0.4}) 0%, rgba(59, 130, 246, ${0.1 + ratio * 0.2}) 70%, transparent 100%)`; // Blue
    } else if (ratio < 0.6) {
        return `radial-gradient(circle, rgba(245, 158, 11, ${0.5 + ratio * 0.3}) 0%, rgba(245, 158, 11, ${0.2 + ratio * 0.2}) 70%, transparent 100%)`; // Yellow/Orange
    } else {
        return `radial-gradient(circle, rgba(239, 68, 68, ${0.6 + ratio * 0.4}) 0%, rgba(239, 68, 68, ${0.3 + ratio * 0.2}) 70%, transparent 100%)`; // Red
    }
};

const getHoverColor = (intensity) => {
    const ratio = intensity / 100;
    return `radial-gradient(circle, rgba(147, 51, 234, ${0.3 + ratio * 0.4}) 0%, rgba(147, 51, 234, ${0.1 + ratio * 0.2}) 70%, transparent 100%)`; // Purple for hovers
};

const getScrollColor = (intensity) => {
    const ratio = intensity / 100;
    return `linear-gradient(to bottom, rgba(34, 197, 94, ${0.1 + ratio * 0.3}) 0%, rgba(34, 197, 94, ${0.05 + ratio * 0.15}) 100%)`;
};

const showTooltip = (event, data) => {
    tooltip.value = {
        visible: true,
        x: event.clientX + 10,
        y: event.clientY - 10,
        title: activeHeatmapType.value === 'clicks' ? `${data.clicks} Clicks` : `${data.hovers} Hovers`,
        content: data.element || 'Element',
        stats: `Intensity: ${data.intensity}%`
    };

    // Show element stats in sidebar
    showElementStats(data);
};

const showScrollTooltip = (event, zone) => {
    tooltip.value = {
        visible: true,
        x: event.clientX + 10,
        y: event.clientY - 10,
        title: `${zone.users} Users`,
        content: `${zone.percentage} reached this point`,
        stats: `Scroll depth: ${zone.intensity}%`
    };
};

const hideTooltip = () => {
    tooltip.value.visible = false;
};

// Lifecycle
onMounted(() => {
    // Auto-select first element for demo
    setTimeout(() => {
        selectedElement.value = selectedElementData;
    }, 1000);
});
</script>

<style scoped>
/* Custom scrollbar */
.overflow-y-auto::-webkit-scrollbar,
.overflow-auto::-webkit-scrollbar {
    width: 6px;
    height: 6px;
}

.overflow-y-auto::-webkit-scrollbar-track,
.overflow-auto::-webkit-scrollbar-track {
    background: #f1f5f9;
}

.overflow-y-auto::-webkit-scrollbar-thumb,
.overflow-auto::-webkit-scrollbar-thumb {
    background: #cbd5e1;
    border-radius: 3px;
}

.overflow-y-auto::-webkit-scrollbar-thumb:hover,
.overflow-auto::-webkit-scrollbar-thumb:hover {
    background: #94a3b8;
}

/* Smooth transitions */
.transition-colors {
    transition: color 0.2s ease, background-color 0.2s ease, border-color 0.2s ease;
}

/* Range input styling */
input[type="range"] {
    -webkit-appearance: none;
    appearance: none;
    background: transparent;
    cursor: pointer;
}

input[type="range"]::-webkit-slider-track {
    background: #e5e7eb;
    height: 8px;
    border-radius: 4px;
}

input[type="range"]::-webkit-slider-thumb {
    -webkit-appearance: none;
    appearance: none;
    background: #3b82f6;
    height: 16px;
    width: 16px;
    border-radius: 50%;
    border: 2px solid #ffffff;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
}

input[type="range"]::-webkit-slider-thumb:hover {
    background: #2563eb;
    transform: scale(1.1);
}

/* Button hover effects */
button:hover {
    transform: translateY(-1px);
}

button:active {
    transform: translateY(0);
}

/* Focus states */
button:focus,
input:focus,
select:focus {
    outline: 2px solid #3b82f6;
    outline-offset: 2px;
}

/* Heatmap interaction styles */
.pointer-events-auto:hover {
    filter: brightness(1.2) !important;
    transform: scale(1.05);
    transition: all 0.2s ease;
}

/* Loading states */
.animate-pulse {
    animation: pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
}

@keyframes pulse {
    0%, 100% {
        opacity: 1;
    }
    50% {
        opacity: 0.5;
    }
}

/* Responsive design */
@media (max-width: 1024px) {
    .w-80 {
        width: 16rem;
    }
}

@media (max-width: 768px) {
    .w-80 {
        width: 100%;
        height: auto;
        position: absolute;
        top: 0;
        left: 0;
        z-index: 20;
        transform: translateX(-100%);
        transition: transform 0.3s ease;
    }

    .w-80.show {
        transform: translateX(0);
    }
}

/* High contrast mode */
@media (prefers-contrast: high) {
    .border-gray-200,
    .border-gray-300 {
        border-color: rgb(0 0 0);
        border-width: 2px;
    }
}

/* Reduced motion */
@media (prefers-reduced-motion: reduce) {
    .transition-colors,
    button:hover,
    .pointer-events-auto:hover {
        transition: none;
        transform: none;
    }

    .animate-pulse {
        animation: none;
    }
}

/* Print styles */
@media print {
    .absolute {
        position: static;
    }

    .shadow-lg,
    .shadow-xl {
        box-shadow: none;
    }
}
</style>
