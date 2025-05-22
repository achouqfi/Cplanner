<template>
    <section :id="id" class="relative overflow-hidden isolate via-green-50 to-yellow-50 py-32">
        <div class="max-w-5xl mx-auto px-6 text-center">
            <p class="text-sm font-semibold uppercase tracking-widest text-green-600 mb-4 animate-fade-in">Analytics Platform</p>

            <h1 class="text-gray-900 dark:text-white font-extrabold text-4xl sm:text-5xl lg:text-6xl leading-tight mb-6 animate-slide-up">
                The Most Modern<br />
                Analytics Platform<br />
                for Your Business
            </h1>

            <p class="text-gray-700 dark:text-gray-300 text-lg sm:text-xl leading-relaxed mb-10 max-w-3xl mx-auto animate-fade-in delay-300">
                Track real-time traffic, optimize user journeys, and gain insights across all your websites with Cplanner.
            </p>

            <div class="flex flex-col sm:flex-row justify-center gap-4 animate-fade-in delay-500">
                <Button
                    :href="route('register')"
                    variant="primary"
                    size="lg"
                >
                    Get Started →
                </Button>

                <Button
                    href="https://github.com/abdessamadbettal/Cplanner"
                    variant="secondary"
                    as="a"
                    size="lg"
                >
                    View Deno
                </Button>
            </div>

            <div class="mt-16 w-full max-w-3xl mx-auto animate-fade-in delay-700">
                <div class="rounded-xl shadow-lg bg-white dark:bg-gray-800 p-6">
                    <div class="h-56 w-full bg-gray-100 dark:bg-gray-700 rounded-lg flex items-center justify-center text-gray-400 text-xl font-semibold">
                        Dashboard Preview
                    </div>
                </div>
            </div>
        </div>

    </section>
</template>

<script setup>
import Button from '@/Components/Button.vue';

const props = defineProps({
  id: {
    type: String,
    default: 'hero'
  }
});
</script>

<style scoped>
    html {
    scroll-behavior: smooth;
    }

    /* Animations */
    @keyframes fade-in {
    0% { opacity: 0; transform: translateY(10px); }
    100% { opacity: 1; transform: translateY(0); }
    }

    @keyframes slide-up {
    0% { opacity: 0; transform: translateY(40px); }
    100% { opacity: 1; transform: translateY(0); }
    }

    .animate-fade-in {
    animation: fade-in 0.8s ease-out forwards;
    }

    .animate-slide-up {
    animation: slide-up 1s ease-out forwards;
    }

    .delay-300 { animation-delay: 0.3s; }
    .delay-500 { animation-delay: 0.5s; }
    .delay-700 { animation-delay: 0.7s; }
</style>
