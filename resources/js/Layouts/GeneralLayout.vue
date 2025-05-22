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

const backgroundSvg = `
<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 877 968" class="w-[1000px] max-w-none absolute left-1/2 -translate-x-1/2 top-0 opacity-30 dark:opacity-10 pointer-events-none">
  <g clip-path="url(#a)">
    <circle cx="391" cy="391" r="390.5" stroke="#FF2D20" transform="matrix(-1 0 0 1 416 -56)"/>
    <circle cx="468" cy="468" r="467.5" stroke="#FF2D20" opacity=".3" transform="matrix(-1 0 0 1 493 -133)"/>
    <circle cx="558" cy="558" r="557.5" stroke="#FF2D20" opacity=".1" transform="matrix(-1 0 0 1 583 -223)"/>
    <g filter="url(#b)">
      <ellipse cx="583" cy="229.5" fill="#FF2D20" rx="583" ry="229.5" transform="matrix(-1 0 0 1 621 -9)"/>
    </g>
    <g filter="url(#c)">
      <ellipse cx="262" cy="184.5" fill="#fff" rx="262" ry="184.5" transform="matrix(-1 0 0 1 99 42)"/>
    </g>
  </g>
  <defs>
    <filter id="b" width="1614" height="907" x="-769" y="-233" color-interpolation-filters="sRGB" filterUnits="userSpaceOnUse">
      <feFlood flood-opacity="0" result="BackgroundImageFix"/>
      <feBlend in="SourceGraphic" in2="BackgroundImageFix" result="shape"/>
      <feGaussianBlur result="effect1_foregroundBlur" stdDeviation="112"/>
    </filter>
    <filter id="c" width="972" height="817" x="-649" y="-182" color-interpolation-filters="sRGB" filterUnits="userSpaceOnUse">
      <feFlood flood-opacity="0" result="BackgroundImageFix"/>
      <feBlend in="SourceGraphic" in2="BackgroundImageFix" result="shape"/>
      <feGaussianBlur result="effect1_foregroundBlur" stdDeviation="112"/>
    </filter>
    <clipPath id="a"><path fill="#fff" d="M877 0H0v968h877z"/></clipPath>
  </defs>
</svg>
`;

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
    <div
        class="min-h-screen bg-gradient-to-br from-gray-50 via-gray-50 to-white dark:from-gray-900 dark:via-gray-900 dark:to-black transition-colors duration-300"
        :dir="$page.props.dir"
        :class="{ 'opacity-0': !isLoaded, 'opacity-100': isLoaded }"
    >
        <!-- Background Pattern -->
        <img loading="lazy" id="background" class="absolute -left-20 top-0 max-w-[877px]" alt="Simplify your Laravel development with Laravel Starter! Build faster and smarter with tools like Inertia.js, Vue, Tailwind, multi-language support, SEO optimization, and Filament CMS - all ready to use."
        src="/assets/images/bg/bg-hero.svg"   />

        <!-- Main Content Container -->
        <div class="relative flex min-h-screen flex-col selection:bg-[#FF2D20] selection:text-white">
            <Header
                :menuItems="menuItems"
                :canLogin="canLogin"
                :canRegister="canRegister"
                :isScrolled="isScrolled"
                class="sticky top-0 z-50 transition-all duration-300"
                :class="[
                    isScrolled ? 'bg-white/80 dark:bg-gray-900/80 backdrop-blur-lg shadow-sm' : 'bg-transparent'
                ]"
            />

            <!-- Main Content Area -->
            <div
                class="relative w-full max-w-2xl px-6 lg:max-w-7xl mx-auto flex-grow"
                :class="{ 'animate-fadeIn': isLoaded }"
            >
                <main class="mt-6 pb-24">
                    <slot></slot>
                </main>
            </div>

            <Footer
                :laravelVersion="laravelVersion"
                :phpVersion="phpVersion"
                :menuItems="menuItems"
                class="relative z-10 mt-auto"
            />
        </div>
    </div>
</template>

<style scoped>
@keyframes fadeIn {
    from { opacity: 0; transform: translateY(10px); }
    to { opacity: 1; transform: translateY(0); }
}

.animate-fadeIn {
    animation: fadeIn 0.5s ease-out;
}
</style>
