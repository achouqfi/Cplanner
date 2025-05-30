<script setup>
import { ref, computed } from 'vue';
import { usePage, Link } from '@inertiajs/vue3';
import NavLink from '@/Components/NavLink.vue';
import Logo from "@/Components/Icons/Logo.vue";

const sidebarToggle = ref(false);
const isCollapsed = ref(false);
const isDarkMode = ref(false);

const navigation = [
    {
        name: 'Dashboard',
        route: '/dashboard',
        icon: 'M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z'
    },
    {
        name: 'Websites',
        route: 'websites/list',
        icon: 'M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9'
    },
    {
        name: 'Real-Time',
        route: 'realtime.index',
        icon: 'M13 10V3L4 14h7v7l9-11h-7z'
    },
    {
        name: 'Heatmaps',
        route: 'heatmaps.index',
        icon: 'M17.657 18.657A8 8 0 016.343 7.343S7 9 9 10c0-2 .5-5 2.986-7C14 5 16.09 5.777 17.656 7.343A7.975 7.975 0 0120 13a7.975 7.975 0 01-2.343 5.657z'
    },
    {
        name: 'Path Analysis',
        route: 'paths.index',
        icon: 'M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 01.553-.894L9 2l6 3 5.447-2.724A1 1 0 0121 3.618v10.764a1 1 0 01-.553.894L15 18l-6-3z'
    },
    {
        name: 'SEO Analyzer',
        route: 'seo.index',
        icon: 'M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z'
    },
    {
        name: 'Keyword Research',
        route: 'keywords.index',
        icon: 'M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z'
    },
    {
        name: 'Settings',
        route: 'settings.index',
        icon: 'M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z M15 12a3 3 0 11-6 0 3 3 0 016 0z'
    }
];

const user = usePage().props.auth.user;

const toggleCollapse = () => {
    isCollapsed.value = !isCollapsed.value;
};

const toggleDarkMode = () => {
    isDarkMode.value = !isDarkMode.value;
};

// Helper functions for navbar
const getCurrentPageTitle = () => {
    const currentRoute = route().current();
    const navItem = navigation.find(item => route().current(item.route));
    return navItem ? navItem.name : 'Dashboard';
};

const getCurrentPageDescription = () => {
    const descriptions = {
        'dashboard': 'Monitor your website performance and analytics',
        'websites.index': 'Manage and configure your websites',
        'realtime.index': 'View live website activity and visitors',
        'heatmaps.index': 'Analyze user behavior and interaction patterns',
        'paths.index': 'Track user journey and navigation paths',
        'seo.index': 'Identify and fix SEO optimization issues',
        'keywords.index': 'Research and analyze keyword opportunities',
        'settings.index': 'Configure your account and preferences'
    };

    const currentRoute = route().current();
    return descriptions[currentRoute] || 'Welcome to your dashboard';
};
</script>

<template>
    <div :class="isDarkMode ? 'dark' : ''" class="flex min-h-screen bg-gray-50 dark:bg-gray-900">
        <!-- Sidebar -->
        <aside
            class="w-64 h-screen fixed lg:relative bg-white dark:bg-gray-800 border-r border-gray-200 dark:border-gray-700 overflow-y-auto">
            <div class="flex flex-col h-full">
                <!-- Header -->
                <div class="pt-4 px-4 border-b border-gray-100 dark:border-gray-700">
                    <Logo class="h-12 " />
                </div>

                <!-- Navigation -->
                <nav class="flex-1 px-4 py-6 space-y-1">
                    <Link v-for="item in navigation" :key="item.route" :href="item.route" :class="[
                        'flex items-center gap-3 px-3 py-2.5 text-sm font-medium rounded-lg transition-all duration-200 group',
                        route().current(item.route)
                            ? 'bg-blue-50 dark:bg-blue-900/20 text-blue-700 dark:text-blue-300'
                            : 'text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700/50 hover:text-gray-900 dark:hover:text-white'
                    ]">
                    <svg :class="[
                        'w-5 h-5 transition-colors duration-200',
                        route().current(item.route)
                            ? 'text-blue-600 dark:text-blue-400'
                            : 'text-gray-400 group-hover:text-gray-600 dark:group-hover:text-gray-300'
                    ]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" :d="item.icon" />
                    </svg>
                    <span>{{ item.name }}</span>

                    <!-- Active indicator -->
                    <div v-if="route().current(item.route)" class="ml-auto w-2 h-2 bg-blue-500 rounded-full">
                    </div>
                    </Link>
                </nav>

                <!-- Bottom Section -->
                <div class="p-4 border-t border-gray-100 dark:border-gray-700 space-y-4">
                    <!-- User Info -->
                    <div class="flex items-center gap-3 px-3 py-2">
                        <div
                            class="w-8 h-8 bg-blue-500 rounded-full flex items-center justify-center text-white text-sm font-semibold">
                            {{ user?.name?.charAt(0) || 'U' }}
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-medium text-gray-900 dark:text-white truncate">
                                {{ user?.name || 'User' }}
                            </p>
                            <p class="text-xs text-gray-500 dark:text-gray-400 truncate">
                                {{ user?.email || 'user@example.com' }}
                            </p>
                        </div>
                    </div>

                    <!-- Dark Mode Toggle -->
                    <button @click="toggleDarkMode"
                        class="w-full flex items-center gap-3 px-3 py-2.5 text-sm font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700/50 rounded-lg transition-colors">
                        <svg v-if="!isDarkMode" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 3v1m0 16v1m8.66-8.66h1M3.34 12H2.34m15.36 6.36l.71.71M5.64 5.64l-.71-.71m0 13.42l.71.71M18.36 5.64l-.71-.71" />
                        </svg>
                        <svg v-else class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z" />
                        </svg>
                        <span>{{ isDarkMode ? 'Light Mode' : 'Dark Mode' }}</span>
                    </button>
                </div>
            </div>
        </aside>

        <!-- Main Content -->
        <main class="flex-1 flex flex-col h-screen">
            <!-- Navbar -->
            <header class="bg-white dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700 px-6 py-4">
                <div class="flex items-center justify-between">
                    <!-- Page Title & Breadcrumb -->
                    <div class="flex items-center gap-4">
                        <!-- Mobile menu button -->
                        <button @click="sidebarToggle = !sidebarToggle"
                            class="lg:hidden p-2 rounded-lg text-gray-500 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M4 6h16M4 12h16M4 18h16" />
                            </svg>
                        </button>

                        <div>
                            <h1 class="text-xl font-semibold text-gray-900 dark:text-white">
                                {{ getCurrentPageTitle() }}
                            </h1>
                            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                                {{ getCurrentPageDescription() }}
                            </p>
                        </div>
                    </div>

                    <!-- Right side actions -->
                    <div class="flex items-center gap-4">
                        <!-- Search -->
                        <div class="relative hidden md:block">
                            <svg class="absolute left-3 top-1/2 transform -translate-y-1/2 w-4 h-4 text-gray-400"
                                fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                            <input type="text" placeholder="Search..."
                                class="w-64 pl-10 pr-4 py-2 text-sm bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400">
                        </div>

                        <!-- Notifications -->
                        <button
                            class="relative p-2 text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg transition-colors">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M15 17h5l-5 5v-5zM9 17H4l5 5v-5zM12 3v1m0 16v1m8.66-8.66h1M3.34 12H2.34" />
                            </svg>
                            <!-- Notification badge -->
                            <span class="absolute -top-1 -right-1 w-3 h-3 bg-red-500 rounded-full"></span>
                        </button>

                        <!-- User menu -->
                        <div class="flex items-center gap-3">
                            <div class="hidden sm:block text-right">
                                <p class="text-sm font-medium text-gray-900 dark:text-white">
                                    {{ user?.name || 'User' }}
                                </p>
                                <p class="text-xs text-gray-500 dark:text-gray-400">
                                    {{ user?.email || 'user@example.com' }}
                                </p>
                            </div>
                            <button
                                class="w-8 h-8 bg-blue-500 hover:bg-blue-600 rounded-full flex items-center justify-center text-white text-sm font-semibold transition-colors">
                                {{ user?.name?.charAt(0) || 'U' }}
                            </button>
                        </div>
                    </div>
                </div>
            </header>

            <!-- Page Content -->
            <div class="flex-1 overflow-y-auto p-6">
                <slot />
            </div>
        </main>
    </div>
</template>

<style scoped></style>
