<template>
  <div class="max-w-7xl mx-auto flex flex-col gap-6 animate-fade-in-up">
    <div class="flex flex-col gap-1">
      <div class="flex items-center gap-2 text-sm text-text-secondary">
        <router-link to="/admin/dashboard" class="hover:text-primary transition-colors">
          Dashboard
        </router-link>
        <span>/</span>
        <span class="text-text-main font-medium">Dokumen</span>
      </div>
      <h1 class="text-text-main text-3xl font-bold leading-tight">Dokumen</h1>
      <p class="text-text-secondary text-sm">
        Rekap semua dokumen resmi mahasiswa untuk kebutuhan unduh cepat.
      </p>
    </div>

    <div class="bg-surface-light border border-border-light rounded-xl shadow-sm overflow-hidden">
      <div class="p-5 border-b border-border-light flex flex-col lg:flex-row lg:items-center gap-4 justify-between">
        <div class="relative flex-1 max-w-xl">
          <input
            v-model="search"
            @input="debouncedFetch"
            class="block w-full pl-10 pr-3 py-2.5 border border-border-light rounded-lg bg-background-light text-text-main placeholder-text-secondary focus:outline-none focus:ring-1 focus:ring-primary focus:border-primary sm:text-sm transition-shadow dark:bg-background"
            placeholder="Cari nama, NIM, atau judul..."
          />
          <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-text-secondary">
            search
          </span>
        </div>
        <button
          @click="fetchData"
          class="inline-flex items-center justify-center gap-2 px-4 py-2.5 border border-border-light rounded-lg text-text-secondary hover:text-primary hover:bg-background-light transition-colors"
        >
          <span class="material-symbols-outlined text-[20px]">refresh</span>
          Muat Ulang
        </button>
      </div>

      <div class="border-b border-border-light overflow-hidden">
        <SimpleBar
          class="document-tabs-scroll"
          data-simplebar-auto-hide="false"
        >
          <div class="flex gap-2 p-3 pb-4 min-w-max">
            <button
              v-for="tab in visibleTypes"
              :key="tab.key"
              @click="setType(tab.key)"
              class="px-3 py-2 rounded-lg text-xs font-semibold transition-colors flex items-center gap-2"
              :class="
                activeType === tab.key
                  ? 'bg-primary text-white shadow-sm'
                  : 'text-text-secondary hover:text-primary hover:bg-background-light'
              "
            >
              <span>{{ tab.label }}</span>
              <span
                class="px-1.5 py-0.5 rounded-full text-[10px]"
                :class="activeType === tab.key ? 'bg-white/20 text-white' : 'bg-sidebar-light text-text-secondary'"
              >
                {{ tab.count ?? 0 }}
              </span>
            </button>
          </div>
        </SimpleBar>
      </div>

      <div v-if="loading" class="p-12 text-center">
        <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-primary mx-auto"></div>
        <p class="text-text-secondary text-sm mt-3">Memuat dokumen...</p>
      </div>

      <div v-else class="overflow-x-auto">
        <table class="w-full text-left text-sm">
          <thead class="bg-sidebar-light/50 text-text-secondary border-b border-border-light">
            <tr>
              <th class="px-6 py-4 font-semibold">Mahasiswa</th>
              <th class="px-6 py-4 font-semibold">Dokumen</th>
              <th class="px-6 py-4 font-semibold">Nomor</th>
              <th class="px-6 py-4 font-semibold">Tanggal</th>
              <th class="px-6 py-4 font-semibold">Judul</th>
              <th class="px-6 py-4 text-right font-semibold">Aksi</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-border-light">
            <tr v-if="items.length === 0">
              <td colspan="6" class="px-6 py-12 text-center text-text-secondary">
                Tidak ada dokumen pada tab ini
              </td>
            </tr>
            <tr v-for="item in items" :key="`${item.type}-${item.id}`" class="hover:bg-sidebar-light/30 transition-colors">
              <td class="px-6 py-4">
                <div class="flex items-center gap-3">
                  <div
                    class="size-10 rounded-full flex items-center justify-center text-xs font-bold shrink-0"
                    :class="getAvatarColor(item.mahasiswa?.nama)"
                  >
                    {{ getInitials(item.mahasiswa?.nama) }}
                  </div>
                  <div>
                    <p class="font-bold text-text-main">{{ item.mahasiswa?.nama || "-" }}</p>
                    <p class="text-xs text-text-secondary">{{ item.mahasiswa?.nim || "-" }}</p>
                    <p class="text-xs text-text-secondary">{{ item.prodi?.nama || "-" }}</p>
                  </div>
                </div>
              </td>
              <td class="px-6 py-4">
                <span class="inline-flex items-center gap-2 px-2.5 py-1 rounded-full bg-blue-50 text-primary dark:bg-blue-900/20 dark:text-blue-400 text-xs font-bold">
                  <span class="material-symbols-outlined text-[16px]">description</span>
                  {{ item.label }}
                </span>
              </td>
              <td class="px-6 py-4">
                <code class="text-xs text-text-main bg-background-light border border-border-light rounded px-2 py-1 dark:bg-background">
                  {{ item.nomor || "Belum bernomor" }}
                </code>
              </td>
              <td class="px-6 py-4 text-text-secondary whitespace-nowrap">
                {{ formatDate(item.tanggal) }}
              </td>
              <td class="px-6 py-4 max-w-sm">
                <p class="text-text-main line-clamp-2" :title="item.judul">
                  {{ item.judul || "-" }}
                </p>
              </td>
              <td class="px-6 py-4">
                <div class="flex items-center justify-end gap-2">
                <button
                  @click="viewItem(item)"
                  :disabled="viewing === `${item.type}-${item.id}`"
                  class="inline-flex items-center gap-2 px-3 py-2 rounded-lg text-text-secondary hover:text-primary hover:bg-blue-50 dark:hover:bg-blue-900/20 transition-colors disabled:opacity-40 disabled:cursor-not-allowed"
                >
                  <span
                    v-if="viewing === `${item.type}-${item.id}`"
                    class="animate-spin rounded-full h-4 w-4 border-b-2 border-current"
                  ></span>
                  <span v-else class="material-symbols-outlined text-[20px]">visibility</span>
                  View
                </button>
                <button
                  @click="downloadItem(item)"
                  :disabled="downloading === `${item.type}-${item.id}`"
                  class="inline-flex items-center gap-2 px-3 py-2 rounded-lg text-text-secondary hover:text-primary hover:bg-blue-50 dark:hover:bg-blue-900/20 transition-colors disabled:opacity-40 disabled:cursor-not-allowed"
                >
                  <span
                    v-if="downloading === `${item.type}-${item.id}`"
                    class="animate-spin rounded-full h-4 w-4 border-b-2 border-current"
                  ></span>
                  <span v-else class="material-symbols-outlined text-[20px]">download</span>
                  Unduh
                </button>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <div v-if="pagination.last_page > 1" class="p-4 border-t border-border-light flex items-center justify-between gap-3">
        <p class="text-sm text-text-secondary">
          Halaman {{ pagination.current_page }} dari {{ pagination.last_page }}
        </p>
        <div class="flex gap-2">
          <button
            @click="goToPage(pagination.current_page - 1)"
            :disabled="pagination.current_page <= 1"
            class="px-3 py-2 border border-border-light rounded-lg text-text-secondary hover:text-primary disabled:opacity-40 disabled:cursor-not-allowed"
          >
            Sebelumnya
          </button>
          <button
            @click="goToPage(pagination.current_page + 1)"
            :disabled="pagination.current_page >= pagination.last_page"
            class="px-3 py-2 border border-border-light rounded-lg text-text-secondary hover:text-primary disabled:opacity-40 disabled:cursor-not-allowed"
          >
            Berikutnya
          </button>
        </div>
      </div>
    </div>
  </div>

  <Teleport to="body">
    <transition name="modal-fade">
      <div
        v-if="preview.show"
        class="fixed inset-0 z-[100] flex items-center justify-center bg-slate-950/75 p-3 sm:p-6 backdrop-blur-sm"
        @click.self="closePreview"
      >
        <div class="flex h-[92vh] w-full max-w-6xl flex-col overflow-hidden rounded-xl border border-border-light bg-surface-light shadow-2xl">
          <div class="flex min-h-16 items-center justify-between gap-4 border-b border-border-light px-4 py-3 sm:px-5">
            <div class="min-w-0">
              <h2 class="truncate text-base font-bold leading-tight text-text-main sm:text-lg">
                {{ preview.title }}
              </h2>
              <p class="mt-0.5 truncate text-xs text-text-secondary">
                {{ preview.fileName }}
              </p>
            </div>
            <div class="flex items-center gap-2 shrink-0">
              <button
                @click="downloadPreview"
                class="inline-flex items-center gap-2 rounded-lg px-3 py-2 text-sm text-text-secondary transition-colors hover:bg-blue-50 hover:text-primary dark:hover:bg-blue-900/20"
              >
                <span class="material-symbols-outlined text-[20px]">download</span>
                Unduh
              </button>
              <button
                @click="closePreview"
                class="inline-flex size-10 items-center justify-center rounded-lg text-text-secondary transition-colors hover:bg-red-50 hover:text-red-600 dark:hover:bg-red-900/20"
                aria-label="Tutup preview"
              >
                <span class="material-symbols-outlined text-[22px]">close</span>
              </button>
            </div>
          </div>
          <div class="min-h-0 flex-1 bg-[#202124]">
            <iframe
              v-if="preview.url"
              :src="preview.url"
              class="h-full w-full border-0"
              title="Preview dokumen PDF"
            ></iframe>
          </div>
        </div>
      </div>
    </transition>
  </Teleport>
</template>

<script setup>
import { computed, onMounted, onUnmounted, ref } from "vue";
import SimpleBar from "simplebar-vue";
import { useToast } from "vue-toastification";
import adminService from "../../../services/adminService";
import { useAuthStore } from "../../../stores/auth";

const toast = useToast();
const authStore = useAuthStore();
const loading = ref(false);
const downloading = ref(null);
const viewing = ref(null);
const search = ref("");
const activeType = ref("sk_penguji_sempro");
const types = ref([]);
const items = ref([]);
const preview = ref({
  show: false,
  url: "",
  title: "",
  fileName: "",
  item: null,
});
const pagination = ref({
  current_page: 1,
  last_page: 1,
  per_page: 10,
  total: 0,
});
let searchTimer = null;

const visibleTypes = computed(() =>
  types.value.filter(
    (type) =>
      authStore.semhasEnabled ||
      !["sk_penguji_semhas", "ba_semhas"].includes(type.key),
  ),
);

const fetchData = async (page = pagination.value.current_page || 1) => {
  loading.value = true;
  try {
    const response = await adminService.getDokumenResmi({
      type: activeType.value,
      search: search.value || undefined,
      page,
      per_page: pagination.value.per_page,
    });

    if (response.success) {
      types.value = response.types || [];
      const data = response.data || {};
      items.value = data.data || [];
      pagination.value = {
        current_page: data.current_page || 1,
        last_page: data.last_page || 1,
        per_page: data.per_page || 10,
        total: data.total || 0,
      };
    }
  } catch (error) {
    toast.error(error.response?.data?.message || "Gagal memuat dokumen");
  } finally {
    loading.value = false;
  }
};

const debouncedFetch = () => {
  clearTimeout(searchTimer);
  searchTimer = setTimeout(() => fetchData(1), 350);
};

const setType = (type) => {
  activeType.value = type;
  fetchData(1);
};

const goToPage = (page) => {
  if (page < 1 || page > pagination.value.last_page) return;
  fetchData(page);
};

const getPdfResponse = (item) => {
  if (item.type === "sk_tugas") return adminService.getSkTugasPdf(item.skripsi_id);
  if (item.type === "nota_bimbingan") return adminService.getNotaBimbinganPdf(item.skripsi_id);
  if (item.type === "sk_yudisium") return adminService.generateSKYudisiumPdf(item.skripsi_id);
  if (item.type.startsWith("sk_penguji_")) return adminService.getSkPengujiPdf(item.seminar_id);
  if (item.type.startsWith("ba_")) return adminService.getBeritaAcaraPdf(item.seminar_id);
  throw new Error("Jenis dokumen tidak dikenal");
};

const revokePreviewUrl = () => {
  if (preview.value.url) {
    window.URL.revokeObjectURL(preview.value.url);
  }
};

const viewItem = async (item) => {
  const key = `${item.type}-${item.id}`;
  viewing.value = key;
  try {
    const response = await getPdfResponse(item);
    const blob = new Blob([response.data], { type: "application/pdf" });
    revokePreviewUrl();
    preview.value = {
      show: true,
      url: window.URL.createObjectURL(blob),
      title: item.label || "Dokumen",
      fileName: getFileName(item),
      item,
    };
  } catch (error) {
    toast.error(error.response?.data?.message || "Gagal membuka preview dokumen");
  } finally {
    viewing.value = null;
  }
};

const closePreview = () => {
  revokePreviewUrl();
  preview.value = {
    show: false,
    url: "",
    title: "",
    fileName: "",
    item: null,
  };
};

const downloadItem = async (item) => {
  const key = `${item.type}-${item.id}`;
  downloading.value = key;
  try {
    const response = await getPdfResponse(item);
    const blob = new Blob([response.data], { type: "application/pdf" });
    const url = window.URL.createObjectURL(blob);
    const link = document.createElement("a");
    link.href = url;
    link.download = getFileName(item);
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
    window.URL.revokeObjectURL(url);
  } catch (error) {
    toast.error(error.response?.data?.message || "Gagal mengunduh dokumen");
  } finally {
    downloading.value = null;
  }
};

const downloadPreview = () => {
  if (!preview.value.url || !preview.value.item) return;
  const link = document.createElement("a");
  link.href = preview.value.url;
  link.download = preview.value.fileName;
  document.body.appendChild(link);
  link.click();
  document.body.removeChild(link);
};

const getFileName = (item) => {
  const nim = item.mahasiswa?.nim || "mahasiswa";
  const names = {
    sk_tugas: "SK_Tugas",
    nota_bimbingan: "Nota_Bimbingan",
    sk_penguji_sempro: "SK_Penguji_Sempro",
    ba_sempro: "Berita_Acara_Sempro",
    sk_penguji_semhas: "SK_Penguji_Semhas",
    ba_semhas: "Berita_Acara_Semhas",
    sk_penguji_sidang: "SK_Penguji_Sidang",
    ba_sidang: "Berita_Acara_Sidang",
    sk_yudisium: "SK_Yudisium",
  };
  return `${names[item.type] || "Dokumen"}_${nim}.pdf`;
};

const formatDate = (date) => {
  if (!date) return "-";
  return new Date(date).toLocaleDateString("id-ID", {
    day: "2-digit",
    month: "short",
    year: "numeric",
  });
};

const getInitials = (name) =>
  (name || "?")
    .split(" ")
    .map((word) => word[0])
    .join("")
    .slice(0, 2)
    .toUpperCase();

const getAvatarColor = (name) => {
  const colors = [
    "bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-300",
    "bg-emerald-100 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-300",
    "bg-purple-100 text-purple-700 dark:bg-purple-900/30 dark:text-purple-300",
    "bg-amber-100 text-amber-700 dark:bg-amber-900/30 dark:text-amber-300",
  ];
  const index = (name || "").length % colors.length;
  return colors[index];
};

onMounted(() => fetchData(1));
onUnmounted(() => revokePreviewUrl());
</script>
