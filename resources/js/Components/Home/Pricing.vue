<script setup>
import { ref } from 'vue';

const billingCycle = ref('monthly');
import Button from '@/Components/Button.vue'

defineProps({
  billingCycle: {
    type: String,
    default: 'monthly'
  }
})


const prices = {
    monthly: [
        {
            name: 'Free',
            price: '0',
            description: 'Basic features for individuals',
            features: ['1 Website', 'Basic Analytics', 'Community Support'],
            cta: 'Get Started',
            highlighted: false,
        },
        {
            name: 'Pro',
            price: '19',
            description: 'Advanced tools for professionals',
            features: ['10 Websites', 'Real-time Data', 'Email Support', 'Custom Dashboards'],
            cta: 'Start Free Trial',
            highlighted: true,
        },
        {
            name: 'Enterprise',
            price: '49',
            description: 'For large teams & businesses',
            features: ['Unlimited Websites', 'Priority Support', 'API Access', 'Custom Integration'],
            cta: 'Contact Sales',
            highlighted: false,
        },
    ],
    yearly: [],
};

// Fill `yearly` using monthly base
prices.yearly = prices.monthly.map((plan, index) => ({
    ...plan,
    price: index === 0 ? '0' : String(Number(plan.price) * 10), // e.g., $19 → $190
}));

</script>

<template>
    <section class="bg-white dark:bg-gray-900 py-24">
        <div class="max-w-7xl mx-auto px-6 lg:px-8 text-center">
            <h2 class="text-3xl sm:text-4xl font-extrabold text-gray-900 dark:text-white mb-6">
                Simple, transparent pricing
            </h2>
            <p class="text-lg text-gray-600 dark:text-gray-300 max-w-2xl mx-auto">
                Choose the plan that’s right for your project. No hidden fees, cancel anytime.
            </p>

<div class="mt-8 flex justify-center">
  <div class="inline-flex items-center bg-gray-200 dark:bg-gray-700 p-1 rounded-full shadow-inner">
    <button
      @click="billingCycle = 'monthly'"
      :class="[
        'px-5 py-2 text-sm font-medium rounded-full transition-all duration-300',
        billingCycle === 'monthly'
          ? 'bg-white dark:bg-gray-900 text-blue-600 shadow'
          : 'text-gray-700 dark:text-gray-300'
      ]"
    >
      Monthly
    </button>

    <button
      @click="billingCycle = 'yearly'"
      :class="[
        'px-5 py-2 text-sm font-medium rounded-full transition-all duration-300',
        billingCycle === 'yearly'
          ? 'bg-white dark:bg-gray-900 text-blue-600 shadow'
          : 'text-gray-700 dark:text-gray-300'
      ]"
    >
      Yearly
    </button>
  </div>
</div>


            <!-- Pricing Cards -->
            <div class="mt-16 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-10">
                <div v-for="plan in prices[billingCycle]" :key="plan.name" :class="[
                    'rounded-xl border p-8 shadow-lg',
                    plan.highlighted ? 'border-blue-600 bg-blue-50 dark:bg-blue-900/10' : 'border-gray-200 dark:border-gray-700',
                ]">
                    <h3 class="text-xl font-semibold text-gray-800 dark:text-white">{{ plan.name }}</h3>
                    <p class="mt-2 text-gray-600 dark:text-gray-400">{{ plan.description }}</p>
                    <div class="mt-6 text-4xl font-extrabold text-gray-900 dark:text-white">
                        ${{ plan.price }}<span class="text-lg font-medium text-gray-500 dark:text-gray-400">/mo</span>
                    </div>
                    <ul class="mt-6 text-sm text-left text-gray-600 dark:text-gray-300 space-y-2">
                        <li v-for="feature in plan.features" :key="feature" class="flex items-center gap-2">
                            <svg class="w-4 h-4 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                                <path
                                    d="M16.7 5.3a1 1 0 010 1.4l-7.5 7.5a1 1 0 01-1.4 0L3.3 9.3a1 1 0 011.4-1.4l4.2 4.2 6.8-6.8a1 1 0 011.4 0z" />
                            </svg>
                            {{ feature }}
                        </li>
                    </ul>
                    <button class="mt-8 w-full bg-blue-600 text-white py-2 rounded-md hover:bg-blue-700 transition">
                        {{ plan.cta }}
                    </button>
                </div>
            </div>
        </div>
    </section>
</template>
