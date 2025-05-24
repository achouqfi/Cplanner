<script setup>
import { Head, Link } from '@inertiajs/vue3';
import Hero from '@/Components/Home/Hero.vue';
import Pricing from '@/Components/Home/Pricing.vue';
import BlogSection from '@/Components/Blog/BlogSection.vue';
import GeneralLayout from '@/Layouts/GeneralLayout.vue';
import StackSection from '@/Components/Home/StackSection.vue';

const props = defineProps({
    posts: {
        type: Array,
        required: true
    },
    currentLocale: {
        type: String,
        required: true
    }
});

const alternateUrls = {
    en: 'https://Cplanner.com/en',
    fr: 'https://Cplanner.com/fr',
    es: 'https://Cplanner.com/es'
};

const titles = {
    en: 'Cplanner - Laravel Vue Inertia Starter Kit',
    fr: 'Cplanner - Kit de démarrage Laravel Vue Inertia',
    es: 'Cplanner - Kit de inicio Laravel Vue Inertia'
};

const descriptions = {
    en: 'Production-ready Laravel starter kit with Vue 3, Inertia.js, Tailwind CSS, Filament admin, and built-in SEO optimization.',
    fr: 'Kit de démarrage Laravel prêt pour la production avec Vue 3, Inertia.js, Tailwind CSS, administration Filament et optimisation SEO intégrée.',
    es: 'Kit de inicio Laravel listo para producción con Vue 3, Inertia.js, Tailwind CSS, administración Filament y optimización SEO integrada.'
};
</script>

<template>
    <GeneralLayout>
        <Head>
            <title>{{ titles[currentLocale] }}</title>
            <meta name="description" :content="descriptions[currentLocale]" />
            <meta name="keywords"
                content="Laravel starter kit, Vue 3, Inertia.js, TailwindCSS, Filament CMS, SEO optimization, SSR, i18n" />

            <!-- Language alternates -->
            <link rel="alternate" hreflang="en" :href="alternateUrls.en" />
            <link rel="alternate" hreflang="fr" :href="alternateUrls.fr" />
            <link rel="alternate" hreflang="es" :href="alternateUrls.es" />
            <link rel="alternate" hreflang="x-default" :href="alternateUrls.en" />

            <!-- Open Graph / Facebook -->
            <meta property="og:type" content="website" />
            <meta property="og:url" :content="alternateUrls[currentLocale]" />
            <meta property="og:title" :content="titles[currentLocale]" />
            <meta property="og:description" :content="descriptions[currentLocale]" />
            <meta property="og:image" content="https://Cplanner.com/favicon.png" />
            <meta property="og:locale" :content="currentLocale" />
            <meta property="og:locale:alternate" content="en_US" />
            <meta property="og:locale:alternate" content="fr_FR" />
            <meta property="og:locale:alternate" content="es_ES" />

            <!-- Twitter -->
            <meta property="twitter:card" content="summary_large_image" />
            <meta property="twitter:url" :content="alternateUrls[currentLocale]" />
            <meta property="twitter:title" :content="titles[currentLocale]" />
            <meta property="twitter:description" :content="descriptions[currentLocale]" />
            <meta property="twitter:image" content="https://Cplanner.com/favicon.png" />
        </Head>

        <Hero />
        <StackSection />
        <Pricing />
        <BlogSection :posts="posts.data" v-if="posts.data.length > 0" />
    </GeneralLayout>
</template>
