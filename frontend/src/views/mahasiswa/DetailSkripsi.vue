<template>
  <div class="flex flex-col gap-6">
    <!-- Breadcrumbs -->
    <div class="flex flex-wrap items-center gap-2 text-sm">
      <router-link
        to="/mahasiswa/skripsi"
        class="text-text-secondary hover:text-primary font-medium"
        >Skripsi Saya</router-link
      >
      <span class="material-symbols-outlined text-text-secondary text-sm"
        >chevron_right</span
      >
      <span class="text-text-main font-bold">Detail Skripsi</span>
    </div>

    <!-- Loading -->
    <div v-if="loading" class="flex flex-col gap-6 animate-pulse">
      <div class="h-10 w-60 bg-gray-200 dark:bg-gray-700 rounded"></div>
      <div class="h-12 bg-gray-200 dark:bg-gray-700 rounded-lg"></div>
      <div class="h-96 bg-gray-200 dark:bg-gray-700 rounded-xl"></div>
    </div>

    <!-- Error -->
    <div
      v-else-if="error"
      class="bg-surface-light rounded-xl shadow-sm border border-border-light p-12 flex flex-col items-center justify-center gap-4 text-center"
    >
      <span class="material-symbols-outlined text-6xl text-red-400 opacity-60"
        >error</span
      >
      <h3 class="text-xl font-bold text-text-main">Gagal Memuat Data</h3>
      <p class="text-text-secondary max-w-md">{{ error }}</p>
      <button
        @click="fetchData"
        class="mt-2 px-6 py-2 bg-primary text-white rounded-lg font-bold text-sm hover:bg-blue-600 transition-colors"
      >
        Coba Lagi
      </button>
    </div>

    <template v-else-if="skripsi">
      <!-- Page Heading -->
      <div
        class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4"
      >
        <div class="flex flex-col gap-1">
          <h1 class="text-3xl font-bold tracking-tight text-text-main">
            Detail Skripsi
          </h1>
          <p class="text-text-secondary text-base">
            Kelola progres, dokumen, dan bimbingan skripsi Anda.
          </p>
        </div>
        <span
          :class="statusBadgeClass"
          class="px-3 py-1 rounded-full text-sm font-bold border"
        >
          Status: {{ statusLabel }}
        </span>
      </div>

      <!-- Tabs Navigation -->
      <div class="border-b border-border-light">
        <div class="flex gap-8 overflow-x-auto no-scrollbar">
          <router-link
            v-for="tab in tabs"
            :key="tab.id"
            :to="{ name: tab.routeName }"
            class="pb-3 pt-2 min-w-fit text-sm font-bold transition-all border-b-[3px]"
            :class="[
              $route.name === tab.routeName
                ? 'border-primary text-primary'
                : 'border-transparent text-text-secondary hover:text-text-main hover:border-gray-300',
            ]"
          >
            {{ tab.label }}
          </router-link>
        </div>
      </div>

      <!-- Tab Content -->
      <div class="mt-2 text-text-main">
        <router-view v-slot="{ Component }">
          <transition name="fade" mode="out-in">
            <component :is="Component" />
          </transition>
        </router-view>
      </div>
    </template>
  </div>
</template>

<script setup>
import { ref, computed, provide, onMounted } from "vue";
import { mahasiswaService } from "../../services/mahasiswaService";

const loading = ref(true);
const error = ref("");
const skripsi = ref(null);

const statusMap = {
  draft: {
    label: "Draft",
    class:
      "bg-gray-100 text-gray-600 border-gray-200 dark:bg-gray-800 dark:text-gray-400 dark:border-gray-700",
  },
  pengajuan: {
    label: "Pengajuan",
    class:
      "bg-yellow-50 text-yellow-700 border-yellow-100 dark:bg-yellow-900/30 dark:text-yellow-300 dark:border-yellow-800",
  },
  disetujui: {
    label: "Disetujui",
    class:
      "bg-green-50 text-green-700 border-green-200 dark:bg-green-900/30 dark:text-green-400 dark:border-green-800",
  },
  ditolak: {
    label: "Ditolak",
    class:
      "bg-red-50 text-red-700 border-red-200 dark:bg-red-900/30 dark:text-red-400 dark:border-red-800",
  },
  proposal: {
    label: "Tahap Proposal",
    class:
      "bg-blue-50 text-primary border-blue-200 dark:bg-blue-900/30 dark:text-blue-300 dark:border-blue-800",
  },
  sempro: {
    label: "Sudah Sempro",
    class:
      "bg-green-50 text-green-700 border-green-200 dark:bg-green-900/30 dark:text-green-400 dark:border-green-800",
  },
  penentuan_dospem: {
    label: "Penentuan Dospem",
    class:
      "bg-indigo-50 text-indigo-700 border-indigo-200 dark:bg-indigo-900/30 dark:text-indigo-400 dark:border-indigo-800",
  },
  dospem: {
    label: "Dospem Ditentukan",
    class:
      "bg-indigo-50 text-indigo-700 border-indigo-200 dark:bg-indigo-900/30 dark:text-indigo-400 dark:border-indigo-800",
  },
  bimbingan: {
    label: "Proses Bimbingan",
    class:
      "bg-blue-50 text-primary border-blue-200 dark:bg-blue-900/30 dark:text-blue-300 dark:border-blue-800",
  },
  pengajuan_sidang: {
    label: "Pengajuan Sidang",
    class:
      "bg-yellow-50 text-yellow-700 border-yellow-100 dark:bg-yellow-900/30 dark:text-yellow-300 dark:border-yellow-800",
  },
  pengajuan_sidang_acc: {
    label: "Sidang Disetujui",
    class:
      "bg-green-50 text-green-700 border-green-200 dark:bg-green-900/30 dark:text-green-400 dark:border-green-800",
  },
  pengajuan_sidang_tolak: {
    label: "Sidang Ditolak",
    class:
      "bg-red-50 text-red-700 border-red-200 dark:bg-red-900/30 dark:text-red-400 dark:border-red-800",
  },
  semhas: {
    label: "Seminar Hasil",
    class:
      "bg-blue-50 text-primary border-blue-200 dark:bg-blue-900/30 dark:text-blue-300 dark:border-blue-800",
  },
  sidang: {
    label: "Sidang",
    class:
      "bg-blue-50 text-primary border-blue-200 dark:bg-blue-900/30 dark:text-blue-300 dark:border-blue-800",
  },
  revisi: {
    label: "Revisi",
    class:
      "bg-orange-50 text-orange-700 border-orange-200 dark:bg-orange-900/30 dark:text-orange-400 dark:border-orange-800",
  },
  lulus: {
    label: "Lulus",
    class:
      "bg-green-50 text-green-700 border-green-200 dark:bg-green-900/30 dark:text-green-400 dark:border-green-800",
  },
};

const statusLabel = computed(
  () => statusMap[skripsi.value?.status]?.label || skripsi.value?.status || "-",
);
const statusBadgeClass = computed(
  () => statusMap[skripsi.value?.status]?.class || statusMap.draft.class,
);

const tabs = [
  { id: "progress", label: "Progress", routeName: "SkripsiProgress" },
  { id: "umum", label: "Profil & Judul", routeName: "SkripsiProfil" },
  { id: "pembimbing", label: "Pembimbing", routeName: "SkripsiPembimbing" },
  { id: "log", label: "Log Bimbingan", routeName: "SkripsiLog" },
  { id: "jadwal", label: "Jadwal", routeName: "SkripsiJadwal" },
  { id: "nilai", label: "Nilai", routeName: "SkripsiNilai" },
  { id: "dokumen", label: "Dokumen", routeName: "SkripsiDokumen" },
];

// Provide skripsi data to child components
provide("skripsi", skripsi);

const fetchData = async () => {
  loading.value = true;
  error.value = "";
  try {
    const res = await mahasiswaService.getSkripsiDetail();
    if (res.success) {
      skripsi.value = res.data;
    } else {
      error.value = res.message || "Gagal memuat data skripsi.";
    }
  } catch (err) {
    error.value =
      err.response?.data?.message || "Terjadi kesalahan saat memuat data.";
  } finally {
    loading.value = false;
  }
};

onMounted(fetchData);
</script>

<style scoped>
.fade-enter-active,
.fade-leave-active {
  transition: opacity 0.3s ease;
}

.fade-enter-from,
.fade-leave-to {
  opacity: 0;
}
</style>
