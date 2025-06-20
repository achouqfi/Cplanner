<script setup>
import Header from '@/Components/Layout/Header.vue';
import Footer from '@/Components/Layout/Footer.vue';
import { ref, onMounted } from 'vue';
import { useScrollLock } from '@vueuse/core';

defineProps({
    canLogin: Boolean,
    canRegister: Boolean,
    laravelVersion: {
        type: String,
        required: true,
    },
    phpVersion: {
        type: String,
        required: true,
    },
});

const menuItems = ref([
    { label: 'home.features', link: "/#features" },
    { label: 'home.pricing', link: "/#pricing" },
    { label: 'home.blogs', link: route('posts.index') },
    { label: 'home.documentation', link: "/#docs" },
]);


const isScrolled = ref(false);
const isLoaded = ref(false);

onMounted(() => {
    window.addEventListener('scroll', handleScroll);
    // Add a small delay to prevent layout shift
    setTimeout(() => {
        isLoaded.value = true;
    }, 100);
});

const handleScroll = () => {
    isScrolled.value = window.scrollY > 20;
};
</script>

<template>
    <div class="min-h-screen bg-gradient-to-br from-gray-50 via-gray-50 to-white dark:from-gray-900 dark:via-gray-900 dark:to-black transition-colors duration-300"
        :dir="$page.props.dir" :class="{ 'opacity-0': !isLoaded, 'opacity-100': isLoaded }">

        <img loading="lazy" id="background" class="absolute -left-20 top-0 max-w-[877px]"
            alt="Simplify your Laravel development with Laravel Starter! Build faster and smarter with tools like Inertia.js, Vue, Tailwind, multi-language support, SEO optimization, and Filament CMS - all ready to use."
            src="/assets/images/bg/bg-hero.svg" />

        <div class="relative flex min-h-screen flex-col selection:bg-[#FF2D20] selection:text-white">
            <Header :menuItems="menuItems" :canLogin="canLogin" :canRegister="canRegister" :isScrolled="isScrolled"
                class="sticky top-0 z-50 transition-all duration-300" :class="[
                    isScrolled ? 'bg-white/80 dark:bg-gray-900/80 backdrop-blur-lg shadow-sm' : 'bg-transparent'
                ]" />

            <div class="relative w-full max-w-1xl px-6  mx-auto flex-grow" :class="{ 'animate-fadeIn': isLoaded }">
                <main class="mt-6 pb-24">
                    <slot></slot>
                </main>
            </div>

            <Footer :laravelVersion="laravelVersion" :phpVersion="phpVersion" :menuItems="menuItems"
                class="relative z-10 mt-auto" />
        </div>
    </div>
</template>

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

.animate-fadeIn {
    animation: fadeIn 0.5s ease-out;
}
</style>
