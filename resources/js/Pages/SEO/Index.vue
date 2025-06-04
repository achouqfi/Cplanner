<template>
  <AuthenticatedLayout>
    <div class="p-6">
      <h1 class="text-2xl font-bold mb-4">SEO Path Indexation</h1>

      <div v-if="loading" class="text-gray-500">Loading...</div>
      <div v-else>
        <table class="min-w-full table-auto border">
          <thead>
            <tr class="bg-gray-100">
              <th class="px-4 py-2 border">URL</th>
              <th class="px-4 py-2 border">Status</th>
              <th class="px-4 py-2 border">Verdict</th>
              <th class="px-4 py-2 border">Indexing</th>
              <th class="px-4 py-2 border">Crawled At</th>
              <th class="px-4 py-2 border">Actions</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="item in indexations.data" :key="item.id">
              <td class="border px-4 py-2 text-sm">{{ item.url }}</td>
              <td class="border px-4 py-2 text-sm">{{ item.status }}</td>
              <td class="border px-4 py-2 text-sm">{{ item.verdict }}</td>
              <td class="border px-4 py-2 text-sm">{{ item.indexing_state }}</td>
              <td class="border px-4 py-2 text-sm">{{ item.last_crawled_at }}</td>
              <td class="border px-4 py-2 text-sm">
                <button @click="fetchReport(item.url)" class="text-blue-500 hover:underline">Report</button>
              </td>
            </tr>
          </tbody>
        </table>

        <div v-if="report" class="mt-6 border p-4 rounded">
          <h2 class="text-lg font-semibold mb-2">Report for: {{ report.url }}</h2>
          <p><strong>Title:</strong> {{ report.data.title }}</p>
          <p><strong>Meta Description:</strong> {{ report.data.meta_description }}</p>
          <p><strong>Canonical:</strong> {{ report.data.canonical }}</p>
          <p><strong>JSON-LD Valid:</strong> {{ report.json_ld_valid ? '✅ Yes' : '❌ No' }}</p>
          <p><strong>Niche Detected:</strong> {{ report.detected_niche }}</p>

          <h3 class="mt-4 font-semibold">H1 Tags</h3>
          <ul class="list-disc ml-6">
            <li v-for="h1 in report.data.h1" :key="h1">{{ h1 }}</li>
          </ul>

          <h3 class="mt-4 font-semibold">Internal Links</h3>
          <ul class="list-disc ml-6">
            <li v-for="link in report.data.internal_links" :key="link">{{ link }}</li>
          </ul>
        </div>
      </div>
    </div>
  </AuthenticatedLayout>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import axios from 'axios';

const indexations = ref({ data: [] });
const loading = ref(true);
const report = ref(null);

onMounted(async () => {
  try {
    const res = await axios.get('/seo');
    indexations.value = res.data;
  } catch (e) {
    console.error(e);
  } finally {
    loading.value = false;
  }
});

const fetchReport = async (url) => {
  try {
    const res = await axios.get('/seo/report', { params: { url } });
    report.value = res.data;
  } catch (e) {
    console.error('Error fetching report:', e);
  }
};
</script>

<style scoped>
table {
  border-collapse: collapse;
  width: 100%;
}
th, td {
  text-align: left;
}
</style>
