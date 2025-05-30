<script setup>
import { ref, onMounted } from 'vue';
import axios from 'axios';
import { InputError, InputLabel, TextInput } from '@/Components/Forms';
import Button from '@/Components/Button.vue';
import Modal from '@/Components/Modal.vue';

const trackedWebsites = ref([]);
const untrackedWebsites = ref([]);
const loading = ref(false);
const error = ref(null);

const showAddModal = ref(false);
const providerConnected = ref(false);

const fetchWebsites = async () => {
  loading.value = true;
  error.value = null;
  try {
    const tracked = await axios.get('/websites');
    trackedWebsites.value = tracked.data;

    const google = await axios.get('/access_websites');
    const trackedUrls = trackedWebsites.value.map(site => site.url);

    untrackedWebsites.value = google.data.websites.filter(site => {
      return !trackedUrls.includes(site.siteUrl);
    });

    providerConnected.value = true;
  } catch (err) {
    if (err.response?.status === 403) {
      providerConnected.value = false;
    } else {
      error.value = 'Failed to fetch websites.';
    }
  } finally {
    loading.value = false;
  }
};


onMounted(fetchWebsites);
</script>

<template>
  <AuthenticatedLayout>
    <template #header>
      <h2 class="text-2xl font-bold text-gray-800 dark:text-white">Your Connected Websites</h2>
    </template>

    <div class="">
      <div class="flex justify-between items-center">
        <h3 class="text-lg font-semibold text-gray-800 dark:text-white">Tracked Websites</h3>
        <Button @click="showAddModal = true">➕ Add Website</Button>
      </div>

      <!-- Website List -->
      <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        <div v-for="site in trackedWebsites" :key="site.id"
          class="bg-white dark:bg-gray-800 p-4 rounded-lg border border-gray-200 dark:border-gray-700">
          <h4 class="text-base font-bold text-gray-800 dark:text-white">{{ site.name || site.url }}</h4>
          <p class="text-sm text-gray-500 dark:text-gray-300">Domain: {{ site.domain }}</p>
        </div>
      </div>

      <Modal :show="showAddModal" @close="showAddModal = false">

      </Modal>
    </div>
  </AuthenticatedLayout>
</template>
