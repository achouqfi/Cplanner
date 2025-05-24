<script setup>
import { ref } from 'vue';
import { useIntersectionObserver } from '@vueuse/core';

const testimonials = [
  {
    name: 'Alex Tan',
    title: 'Head of Growth @DataDock',
    img: 'https://i.pravatar.cc/100?img=49',
    quote:
      'Switching to Cplanner was a game changer. The dashboard is clean, the data is real-time, and our team can finally focus on growth—without trading privacy for performance.',
  },
  {
    name: 'Sophie Kim',
    title: 'Digital Strategist @Pivotly',
    img: 'https://i.pravatar.cc/100?img=12',
    quote:
      'Cplanner helped us simplify our analytics workflow. No more complex dashboards or data silos—everything we need is in one place, privacy-first.',
  },
  {
    name: 'Mohammed A.',
    title: 'Founder @TrackWise',
    img: 'https://i.pravatar.cc/100?img=38',
    quote:
      'What sets Cplanner apart is the attention to user experience. It just works—fast, accurate, and ethical. Highly recommend it for any modern team.',
  },
];

const target = ref(null);
const isVisible = ref(false);

const { stop } = useIntersectionObserver(
  target,
  ([{ isIntersecting }]) => {
    if (isIntersecting) {
      isVisible.value = true;
      stop();
    }
  },
  { threshold: 0.2 }
);
</script>

<template>
  <section
    ref="target"
    class="py-32 bg-gradient-to-b from-white via-gray-50 to-white dark:from-gray-900 dark:via-gray-800 dark:to-gray-900 relative z-10"
  >
    <div class="max-w-7xl mx-auto px-6 lg:px-12">
      <!-- Title -->
      <div
        class="text-center mb-20 transition-all duration-1000 ease-out"
        :class="isVisible ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-10'"
      >
        <h2 class="text-4xl sm:text-5xl font-extrabold text-gray-900 dark:text-white">
          Loved by teams who value speed, clarity, and privacy
        </h2>
        <p class="mt-4 max-w-2xl mx-auto text-lg text-gray-600 dark:text-gray-300">
          What professionals are saying about using Cplanner daily.
        </p>
      </div>

      <!-- Grid of Testimonials -->
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-10">
        <div
          v-for="(testimonial, index) in testimonials"
          :key="testimonial.name"
          class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-2xl p-8 shadow-xl transition-all duration-700 hover:scale-[1.02]"
          :class="isVisible ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-10'"
          :style="{ transitionDelay: `${index * 150}ms` }"
        >
          <blockquote class="text-gray-800 dark:text-gray-100 text-base leading-relaxed">
            <svg
              class="mb-4 w-8 h-8 text-blue-300 dark:text-blue-600"
              fill="currentColor"
              viewBox="0 0 32 32"
              aria-hidden="true"
            >
              <path d="M10.6 16c-.3 1.8-1.2 3.2-2.6 4.2-1.4 1-3.1 1.5-5 1.5v-4.3c1.2 0 2.1-.3 2.8-.8.7-.5 1.1-1.1 1.3-2H0C0 6.9 1.9 2 5.8 0l2.1 3.4c-1.9 1-3.1 2.9-3.4 5.5h6.1V16z"/>
            </svg>
            {{ testimonial.quote }}
          </blockquote>
          <div class="flex items-center gap-4 mt-6">
            <img :src="testimonial.img" alt="Avatar" class="w-12 h-12 rounded-full ring-2 ring-white dark:ring-gray-900 shadow" />
            <div>
              <p class="font-semibold text-gray-900 dark:text-white">{{ testimonial.name }}</p>
              <p class="text-sm text-gray-500 dark:text-gray-400">{{ testimonial.title }}</p>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>
</template>

<style scoped>
html {
  scroll-behavior: smooth;
}
</style>
