<template>
  <div class="flex flex-col gap-6">
    <div
      class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4"
    >
      <div class="flex flex-col gap-1">
        <h1 class="text-text-main text-3xl font-bold leading-tight">
          Berita Acara Ujian
        </h1>
        <p class="text-text-secondary text-sm font-normal">
          Daftar mahasiswa yang telah menyelesaikan ujian dan siap untuk dicetak
          berita acara.
        </p>
      </div>
      <div class="flex items-center gap-3">
        <button
          @click="exportExcel"
          class="flex items-center gap-2 px-4 py-2 bg-white text-primary border border-primary/20 rounded-lg text-sm font-bold hover:bg-primary/5 transition-colors shadow-sm"
        >
          <span class="material-symbols-outlined text-[20px]">download</span>
          Rekap Excel
        </button>
      </div>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
      <div
        class="flex items-center p-5 bg-surface-light border border-border-light rounded-xl shadow-sm hover:shadow-md transition-shadow"
      >
        <div class="p-3 bg-blue-50 text-blue-600 rounded-lg mr-4">
          <span class="material-symbols-outlined">pending_actions</span>
        </div>
        <div>
          <p
            class="text-text-secondary text-xs font-bold uppercase tracking-wider mb-1"
          >
            Siap Generate
          </p>
          <h3 class="text-2xl font-bold text-text-main">
            {{ stats.siap_generate }}
          </h3>
          <p class="text-xs text-text-secondary mt-1">Mahasiswa Lulus Ujian</p>
        </div>
      </div>
      <div
        class="flex items-center p-5 bg-surface-light border border-border-light rounded-xl shadow-sm hover:shadow-md transition-shadow"
      >
        <div class="p-3 bg-green-50 text-green-600 rounded-lg mr-4">
          <span class="material-symbols-outlined">print</span>
        </div>
        <div>
          <p
            class="text-text-secondary text-xs font-bold uppercase tracking-wider mb-1"
          >
            Sudah Dicetak
          </p>
          <h3 class="text-2xl font-bold text-text-main">
            {{ stats.sudah_cetak }}
          </h3>
          <p class="text-xs text-text-secondary mt-1">Dokumen Fisik Tersedia</p>
        </div>
      </div>
      <div
        class="flex items-center p-5 bg-surface-light border border-border-light rounded-xl shadow-sm hover:shadow-md transition-shadow"
      >
        <div class="p-3 bg-orange-50 text-orange-600 rounded-lg mr-4">
          <span class="material-symbols-outlined">history_edu</span>
        </div>
        <div>
          <p
            class="text-text-secondary text-xs font-bold uppercase tracking-wider mb-1"
          >
            Total Ujian
          </p>
          <h3 class="text-2xl font-bold text-text-main">{{ stats.total }}</h3>
          <p class="text-xs text-text-secondary mt-1">Periode Semester Ini</p>
        </div>
      </div>
    </div>

    <!-- Loading -->
    <div v-if="loading" class="p-12 text-center">
      <div
        class="animate-spin rounded-full h-8 w-8 border-b-2 border-primary mx-auto"
      ></div>
      <p class="text-text-secondary text-sm mt-3">Memuat data...</p>
    </div>

    <!-- Table Container -->
    <div
      v-else
      class="flex flex-col rounded-xl border border-border-light bg-surface-light overflow-hidden shadow-sm"
    >
      <div
        class="p-5 border-b border-border-light flex flex-col md:flex-row justify-between items-center gap-4 bg-gray-50/50"
      >
        <div class="relative w-full md:w-80">
          <span
            class="material-symbols-outlined absolute left-3 top-2.5 text-text-secondary text-[20px]"
            >search</span
          >
          <input
            v-model="searchQuery"
            @input="debouncedSearch"
            class="w-full pl-10 pr-4 py-2 rounded-lg border border-border-light text-sm focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all"
            placeholder="Cari Mahasiswa atau NIM..."
            type="text"
          />
        </div>
        <div
          class="flex items-center gap-2 w-full md:w-auto overflow-x-auto pb-1 md:pb-0"
        >
          <span
            class="text-xs font-bold text-text-secondary uppercase tracking-wide mr-2 shrink-0"
            >Status:</span
          >
          <button
            @click="filterStatus = ''"
            :class="
              filterStatus === ''
                ? 'bg-primary text-white'
                : 'bg-sidebar-light text-text-secondary border border-border-light'
            "
            class="px-4 py-1.5 rounded-full text-xs font-bold shrink-0 transition-colors"
          >
            Semua
          </button>
          <button
            @click="filterStatus = 'belum_cetak'"
            :class="
              filterStatus === 'belum_cetak'
                ? 'bg-primary text-white'
                : 'bg-sidebar-light text-text-secondary border border-border-light'
            "
            class="px-4 py-1.5 rounded-full text-xs font-bold shrink-0 transition-colors"
          >
            Belum Dicetak
          </button>
          <button
            @click="filterStatus = 'selesai'"
            :class="
              filterStatus === 'selesai'
                ? 'bg-primary text-white'
                : 'bg-sidebar-light text-text-secondary border border-border-light'
            "
            class="px-4 py-1.5 rounded-full text-xs font-bold shrink-0 transition-colors"
          >
            Selesai
          </button>
        </div>
      </div>
      <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
          <thead>
            <tr class="bg-sidebar-light/50 border-b border-border-light">
              <th
                class="p-4 pl-6 text-[10px] font-bold tracking-widest text-text-secondary uppercase"
              >
                Mahasiswa
              </th>
              <th
                class="p-4 text-[10px] font-bold tracking-widest text-text-secondary uppercase w-1/3"
              >
                Judul Skripsi
              </th>
              <th
                class="p-4 text-[10px] font-bold tracking-widest text-text-secondary uppercase whitespace-nowrap"
              >
                Tanggal Ujian
              </th>
              <th
                class="p-4 text-[10px] font-bold tracking-widest text-text-secondary uppercase"
              >
                Hasil
              </th>
              <th
                class="p-4 pr-6 text-[10px] font-bold tracking-widest text-text-secondary uppercase text-right"
              >
                Aksi
              </th>
            </tr>
          </thead>
          <tbody class="divide-y divide-border-light">
            <tr v-if="beritaAcaraList.length === 0">
              <td colspan="5" class="p-12 text-center text-text-secondary">
                Tidak ada data
              </td>
            </tr>
            <tr
              v-for="item in beritaAcaraList"
              :key="item.id"
              class="group hover:bg-sidebar-light/30 transition-colors"
            >
              <td class="p-4 pl-6">
                <div class="flex items-center gap-3">
                  <div
                    class="size-10 rounded-full flex items-center justify-center text-xs font-bold"
                    :class="getAvatarColor(item.skripsi?.mahasiswa?.nama)"
                  >
                    {{ getInitials(item.skripsi?.mahasiswa?.nama) }}
                  </div>
                  <div>
                    <p class="font-bold text-text-main text-sm">
                      {{ item.skripsi?.mahasiswa?.nama || "-" }}
                    </p>
                    <p class="text-xs text-text-secondary font-medium">
                      {{ item.skripsi?.mahasiswa?.nim || "-" }}
                    </p>
                  </div>
                </div>
              </td>
              <td class="p-4">
                <p
                  class="text-sm text-text-main font-medium line-clamp-2 leading-snug"
                >
                  {{ item.skripsi?.judul || "-" }}
                </p>
              </td>
              <td class="p-4 whitespace-nowrap">
                <div class="flex flex-col">
                  <span class="text-sm font-bold text-text-main">{{
                    formatDate(item.tanggal_ujian)
                  }}</span>
                  <span class="text-xs text-text-secondary">{{
                    item.waktu_ujian || "-"
                  }}</span>
                </div>
              </td>
              <td class="p-4">
                <span
                  class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-bold"
                  :class="getHasilClass(item.hasil)"
                >
                  {{ getHasilLabel(item.hasil) }}
                </span>
              </td>
              <td class="p-4 pr-6 text-right">
                <button
                  v-if="item.hasil === 'lulus' && !item.berita_acara_tercetak"
                  @click="generateBA(item)"
                  :disabled="generating === item.id"
                  class="inline-flex items-center gap-2 px-4 py-2 text-xs font-bold text-white bg-primary rounded-lg hover:bg-primary/90 transition-all shadow-md shadow-primary/20 hover:shadow-lg disabled:opacity-50"
                >
                  <span class="material-symbols-outlined text-[16px]"
                    >description</span
                  >
                  {{ generating === item.id ? "Generating..." : "Generate BA" }}
                </button>
                <button
                  v-else-if="item.hasil !== 'lulus'"
                  class="inline-flex items-center gap-2 px-4 py-2 text-xs font-bold text-text-secondary bg-gray-100 rounded-lg cursor-not-allowed opacity-70"
                  disabled
                >
                  <span class="material-symbols-outlined text-[16px]"
                    >lock</span
                  >
                  Menunggu Revisi
                </button>
                <button
                  v-else
                  @click="downloadBA(item)"
                  class="inline-flex items-center gap-2 px-4 py-2 text-xs font-bold text-green-700 bg-green-50 rounded-lg hover:bg-green-100 transition-all"
                >
                  <span class="material-symbols-outlined text-[16px]"
                    >download</span
                  >
                  Download
                </button>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
      <div
        class="p-4 border-t border-border-light flex justify-between items-center bg-gray-50/50"
      >
        <p class="text-xs text-text-secondary font-medium">
          Menampilkan {{ pagination.from || 0 }}-{{ pagination.to || 0 }} dari
          {{ pagination.total }} data
        </p>
        <div class="flex gap-2">
          <button
            @click="goToPage(pagination.current_page - 1)"
            :disabled="pagination.current_page <= 1"
            class="p-1.5 rounded-lg border border-border-light bg-white hover:bg-gray-50 disabled:opacity-50 text-text-secondary transition-all"
          >
            <span class="material-symbols-outlined text-[18px]"
              >chevron_left</span
            >
          </button>
          <button
            @click="goToPage(pagination.current_page + 1)"
            :disabled="pagination.current_page >= pagination.last_page"
            class="p-1.5 rounded-lg border border-border-light bg-white hover:bg-gray-50 text-text-secondary transition-all"
          >
            <span class="material-symbols-outlined text-[18px]"
              >chevron_right</span
            >
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, reactive, watch } from "vue";
import adminService from "../../../services/adminService";

const loading = ref(true);
const generating = ref(null);
const beritaAcaraList = ref([]);
const searchQuery = ref("");
const filterStatus = ref("");

const stats = reactive({
  siap_generate: 0,
  sudah_cetak: 0,
  total: 0,
});

const pagination = reactive({
  current_page: 1,
  last_page: 1,
  total: 0,
  from: 0,
  to: 0,
});

let searchTimeout = null;

const fetchBeritaAcara = async () => {
  try {
    loading.value = true;
    const params = {
      page: pagination.current_page,
      search: searchQuery.value,
      status: filterStatus.value,
    };
    const response = await adminService.getBeritaAcara(params);
    if (response.success) {
      beritaAcaraList.value = response.data.data || response.data;
      if (response.data.current_page) {
        Object.assign(pagination, {
          current_page: response.data.current_page,
          last_page: response.data.last_page,
          total: response.data.total,
          from: response.data.from,
          to: response.data.to,
        });
      }
      if (response.stats) {
        Object.assign(stats, response.stats);
      }
    }
  } catch (error) {
    console.error("Failed to fetch berita acara:", error);
  } finally {
    loading.value = false;
  }
};

const debouncedSearch = () => {
  clearTimeout(searchTimeout);
  searchTimeout = setTimeout(() => {
    pagination.current_page = 1;
    fetchBeritaAcara();
  }, 300);
};

watch(filterStatus, () => {
  pagination.current_page = 1;
  fetchBeritaAcara();
});

const goToPage = (page) => {
  if (page >= 1 && page <= pagination.last_page) {
    pagination.current_page = page;
    fetchBeritaAcara();
  }
};

const generateBA = async (item) => {
  try {
    generating.value = item.id;
    const response = await adminService.getBeritaAcaraPdf(
      item.seminar_id || item.id,
    );
    const blob = new Blob([response.data], { type: "application/pdf" });
    const url = window.URL.createObjectURL(blob);
    window.open(url, "_blank");
    fetchBeritaAcara();
  } catch (error) {
    console.error("Failed to generate BA:", error);
    alert("Gagal generate Berita Acara");
  } finally {
    generating.value = null;
  }
};

const downloadBA = async (item) => {
  try {
    const response = await adminService.getBeritaAcaraPdf(
      item.seminar_id || item.id,
    );
    const blob = new Blob([response.data], { type: "application/pdf" });
    const url = window.URL.createObjectURL(blob);
    const link = document.createElement("a");
    link.href = url;
    link.download = `berita-acara-${item.skripsi?.mahasiswa?.nim || item.id}.pdf`;
    link.click();
  } catch (error) {
    console.error("Failed to download BA:", error);
    alert("Gagal download Berita Acara");
  }
};

const exportExcel = () => {
  alert("Fitur export Excel akan segera tersedia");
};

const getInitials = (name) => {
  if (!name) return "?";
  return name
    .split(" ")
    .map((n) => n[0])
    .join("")
    .substring(0, 2)
    .toUpperCase();
};

const getAvatarColor = (name) => {
  const colors = [
    "bg-blue-100 text-blue-600",
    "bg-purple-100 text-purple-600",
    "bg-orange-100 text-orange-600",
    "bg-green-100 text-green-600",
  ];
  if (!name) return colors[0];
  const index = name.charCodeAt(0) % colors.length;
  return colors[index];
};

const formatDate = (date) => {
  if (!date) return "-";
  return new Date(date).toLocaleDateString("id-ID", {
    day: "numeric",
    month: "short",
    year: "numeric",
  });
};

const getHasilClass = (hasil) => {
  const classes = {
    lulus: "bg-green-50 text-green-700 border border-green-100",
    lulus_revisi: "bg-yellow-50 text-yellow-700 border border-yellow-100",
    tidak_lulus: "bg-red-50 text-red-700 border border-red-100",
  };
  return classes[hasil] || "bg-gray-50 text-gray-600";
};

const getHasilLabel = (hasil) => {
  const labels = {
    lulus: "Lulus",
    lulus_revisi: "Revisi",
    tidak_lulus: "Tidak Lulus",
  };
  return labels[hasil] || hasil;
};

onMounted(() => {
  fetchBeritaAcara();
});
</script>
