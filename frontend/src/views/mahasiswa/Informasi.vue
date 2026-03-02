<template>
  <div class="flex flex-col gap-6">
    <!-- Page Heading -->
    <section class="flex flex-col gap-1">
      <h2 class="text-3xl font-bold tracking-tight text-text-main">
        Informasi & Panduan
      </h2>
      <p class="text-text-secondary text-base">
        Tanggal penting dan dokumen panduan skripsi
      </p>
    </section>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
      <!-- Tanggal Penting -->
      <section
        class="bg-surface-light rounded-xl shadow-sm border border-border-light overflow-hidden"
      >
        <div class="p-5 border-b border-border-light flex items-center gap-3">
          <div class="p-2 bg-amber-500/10 rounded-lg text-amber-600">
            <span class="material-symbols-outlined">event</span>
          </div>
          <div>
            <h3 class="text-lg font-bold text-text-main">Tanggal Penting</h3>
            <p class="text-sm text-text-secondary">
              Jadwal penting terkait skripsi
            </p>
          </div>
        </div>
        <div class="p-5">
          <div
            v-if="loadingDates"
            class="flex items-center justify-center py-10"
          >
            <span
              class="material-symbols-outlined text-3xl text-text-secondary animate-spin"
              >progress_activity</span
            >
          </div>
          <div
            v-else-if="dates.length === 0"
            class="py-10 text-center text-text-secondary"
          >
            <span
              class="material-symbols-outlined text-4xl mb-2 block opacity-40"
              >event_busy</span
            >
            <p class="text-sm">Belum ada tanggal penting.</p>
          </div>
          <div v-else class="space-y-3">
            <div
              v-for="(d, i) in dates"
              :key="i"
              class="flex items-center gap-4 p-4 rounded-xl border border-border-light bg-white dark:bg-white/5 transition-all hover:shadow-sm"
            >
              <div
                class="size-12 rounded-xl flex flex-col items-center justify-center text-center leading-tight shrink-0"
                :class="
                  isDatePast(d.tanggal)
                    ? 'bg-gray-100 text-gray-400 dark:bg-gray-800'
                    : 'bg-amber-50 text-amber-600 dark:bg-amber-900/30'
                "
              >
                <span class="text-lg font-bold">{{
                  formatDay(d.tanggal)
                }}</span>
                <span class="text-[10px] font-medium uppercase">{{
                  formatMonth(d.tanggal)
                }}</span>
              </div>
              <div class="min-w-0">
                <p
                  class="text-sm font-semibold text-text-main"
                  :class="{ 'line-through opacity-50': isDatePast(d.tanggal) }"
                >
                  {{ d.label }}
                </p>
                <p class="text-xs text-text-secondary mt-0.5">
                  {{ formatDateFull(d.tanggal) }}
                </p>
              </div>
              <div
                v-if="!isDatePast(d.tanggal)"
                class="ml-auto shrink-0 px-2.5 py-1 bg-amber-50 text-amber-700 dark:bg-amber-900/30 dark:text-amber-400 text-xs font-bold rounded-full"
              >
                {{ daysUntil(d.tanggal) }}
              </div>
            </div>
          </div>
        </div>
      </section>

      <!-- Panduan & Template -->
      <section
        class="bg-surface-light rounded-xl shadow-sm border border-border-light overflow-hidden"
      >
        <div class="p-5 border-b border-border-light flex items-center gap-3">
          <div class="p-2 bg-violet-500/10 rounded-lg text-violet-600">
            <span class="material-symbols-outlined">menu_book</span>
          </div>
          <div>
            <h3 class="text-lg font-bold text-text-main">Panduan & Template</h3>
            <p class="text-sm text-text-secondary">
              Dokumen panduan dan template skripsi
            </p>
          </div>
        </div>
        <div class="p-5">
          <div
            v-if="loadingPanduan"
            class="flex items-center justify-center py-10"
          >
            <span
              class="material-symbols-outlined text-3xl text-text-secondary animate-spin"
              >progress_activity</span
            >
          </div>
          <div
            v-else-if="panduan.length === 0"
            class="py-10 text-center text-text-secondary"
          >
            <span
              class="material-symbols-outlined text-4xl mb-2 block opacity-40"
              >folder_open</span
            >
            <p class="text-sm">Belum ada panduan tersedia.</p>
          </div>
          <div v-else class="space-y-3">
            <button
              v-for="p in panduan"
              :key="p.id"
              @click="downloadPanduan(p)"
              :disabled="downloading === p.id"
              class="flex items-center gap-4 p-4 rounded-xl border border-border-light bg-white dark:bg-white/5 hover:border-primary/30 hover:shadow-sm transition-all w-full text-left group"
            >
              <div
                class="size-12 rounded-xl flex items-center justify-center bg-red-50 dark:bg-red-900/20 text-red-500 shrink-0 group-hover:bg-red-100 dark:group-hover:bg-red-900/40 transition-colors"
              >
                <span class="material-symbols-outlined">description</span>
              </div>
              <div class="flex-1 min-w-0">
                <p class="text-sm font-semibold text-text-main truncate">
                  {{ p.nama_file }}
                </p>
                <p class="text-xs text-text-secondary mt-0.5">
                  {{ formatFileSize(p.ukuran) }}
                </p>
              </div>
              <div
                class="size-10 rounded-lg flex items-center justify-center bg-primary/10 text-primary shrink-0 group-hover:bg-primary group-hover:text-white transition-colors"
              >
                <span class="material-symbols-outlined text-[20px]">{{
                  downloading === p.id ? "hourglass_top" : "download"
                }}</span>
              </div>
            </button>
          </div>
        </div>
      </section>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from "vue";
import { mahasiswaService } from "../../services/mahasiswaService";

const loadingDates = ref(true);
const loadingPanduan = ref(true);
const dates = ref([]);
const panduan = ref([]);
const downloading = ref(null);

// Date helpers
const isDatePast = (dateStr) => new Date(dateStr) < new Date();
const formatDay = (dateStr) => new Date(dateStr).getDate();
const formatMonth = (dateStr) =>
  new Date(dateStr).toLocaleDateString("id-ID", { month: "short" });
const formatDateFull = (dateStr) =>
  new Date(dateStr).toLocaleDateString("id-ID", {
    weekday: "long",
    day: "numeric",
    month: "long",
    year: "numeric",
  });
const daysUntil = (dateStr) => {
  const diff = Math.ceil(
    (new Date(dateStr) - new Date()) / (1000 * 60 * 60 * 24),
  );
  if (diff === 0) return "Hari ini";
  if (diff === 1) return "Besok";
  return `${diff} hari lagi`;
};

const formatFileSize = (bytes) => {
  if (!bytes) return "0 B";
  const sizes = ["B", "KB", "MB", "GB"];
  const i = Math.floor(Math.log(bytes) / Math.log(1024));
  return (bytes / Math.pow(1024, i)).toFixed(1) + " " + sizes[i];
};

const downloadPanduan = async (p) => {
  downloading.value = p.id;
  try {
    const response = await mahasiswaService.downloadPanduan(p.id);
    const blob = new Blob([response.data]);
    const url = window.URL.createObjectURL(blob);
    const a = document.createElement("a");
    a.href = url;
    a.download = p.nama_file;
    a.click();
    window.URL.revokeObjectURL(url);
  } catch (err) {
    console.error("Failed to download panduan:", err);
  } finally {
    downloading.value = null;
  }
};

onMounted(async () => {
  try {
    const res = await mahasiswaService.getTanggalPenting();
    if (res.success) dates.value = res.data || [];
  } catch (err) {
    console.error("Failed to fetch dates:", err);
  } finally {
    loadingDates.value = false;
  }

  try {
    const res = await mahasiswaService.getPanduan();
    if (res.success) panduan.value = res.data || [];
  } catch (err) {
    console.error("Failed to fetch panduan:", err);
  } finally {
    loadingPanduan.value = false;
  }
});
</script>
