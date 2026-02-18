<template>
  <div class="max-w-7xl mx-auto flex flex-col gap-8 animate-fade-in-up">
    <div class="flex flex-col gap-1">
      <h1 class="text-text-main text-3xl font-bold leading-tight">
        Penerbitan Nota Bimbingan
      </h1>
      <p class="text-text-secondary text-sm font-normal">
        Daftar mahasiswa yang telah menyelesaikan bimbingan dan siap mengajukan
        ujian.
      </p>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
      <div
        class="flex flex-col gap-2 rounded-xl p-6 bg-surface-light border border-border-light shadow-sm hover:shadow-md transition-shadow"
      >
        <div class="flex items-center justify-between">
          <p
            class="text-text-secondary text-xs font-bold uppercase tracking-wider"
          >
            Siap Cetak
          </p>
          <div class="bg-primary/10 p-2 rounded-lg text-primary">
            <span class="material-symbols-outlined">print</span>
          </div>
        </div>
        <div class="mt-2">
          <p class="text-text-main text-3xl font-bold leading-tight">
            {{ stats.siap_cetak }}
          </p>
          <div class="flex items-center gap-1 mt-1">
            <span class="material-symbols-outlined text-primary text-[18px]"
              >check_circle</span
            >
            <p class="text-primary text-xs font-semibold">Siap diterbitkan</p>
          </div>
        </div>
      </div>
      <div
        class="flex flex-col gap-2 rounded-xl p-6 bg-surface-light border border-border-light shadow-sm hover:shadow-md transition-shadow"
      >
        <div class="flex items-center justify-between">
          <p
            class="text-text-secondary text-xs font-bold uppercase tracking-wider"
          >
            Menunggu Upload
          </p>
          <div class="bg-orange-100 p-2 rounded-lg text-orange-600">
            <span class="material-symbols-outlined">upload_file</span>
          </div>
        </div>
        <div class="mt-2">
          <p class="text-text-main text-3xl font-bold leading-tight">
            {{ stats.menunggu_upload }}
          </p>
          <div class="flex items-center gap-1 mt-1">
            <span class="material-symbols-outlined text-orange-600 text-[18px]"
              >pending</span
            >
            <p class="text-orange-600 text-xs font-semibold">
              Bimbingan belum cukup
            </p>
          </div>
        </div>
      </div>
      <div
        class="flex flex-col gap-2 rounded-xl p-6 bg-surface-light border border-border-light shadow-sm hover:shadow-md transition-shadow"
      >
        <div class="flex items-center justify-between">
          <p
            class="text-text-secondary text-xs font-bold uppercase tracking-wider"
          >
            Total Diterbitkan
          </p>
          <div class="bg-green-100 p-2 rounded-lg text-green-600">
            <span class="material-symbols-outlined">verified</span>
          </div>
        </div>
        <div class="mt-2">
          <p class="text-text-main text-3xl font-bold leading-tight">
            {{ stats.total_diterbitkan }}
          </p>
          <div class="flex items-center gap-1 mt-1">
            <span class="material-symbols-outlined text-green-600 text-[18px]"
              >calendar_month</span
            >
            <p class="text-green-600 text-xs font-semibold">Nota terbit</p>
          </div>
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
      class="flex flex-col bg-surface-light border border-border-light rounded-xl shadow-sm"
    >
      <div
        class="p-5 border-b border-border-light flex flex-col md:flex-row gap-4 items-center justify-between"
      >
        <div>
          <h3 class="text-text-main text-lg font-bold">
            Mahasiswa Selesai Bimbingan
          </h3>
          <p class="text-text-secondary text-sm">
            Daftar mahasiswa yang telah mendapat persetujuan pembimbing untuk
            maju ujian.
          </p>
        </div>
        <div class="flex flex-col md:flex-row gap-3 w-full md:w-auto">
          <div class="relative w-full md:w-64">
            <input
              v-model="searchQuery"
              @input="debouncedSearch"
              class="w-full pl-10 pr-4 py-2 rounded-lg border border-border-light text-sm focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all bg-background-light text-text-main placeholder-text-secondary dark:bg-background"
              placeholder="Cari mahasiswa..."
            />
            <span
              class="material-symbols-outlined absolute left-3 top-2.5 text-[18px] text-text-secondary"
              >search</span
            >
          </div>
          <button
            @click="exportData"
            :disabled="exporting"
            class="flex items-center justify-center gap-2 text-white text-sm font-bold bg-primary hover:bg-primary/90 px-4 py-2 rounded-lg transition-colors shadow-sm shadow-primary/20 w-full md:w-auto disabled:opacity-50"
          >
            <span
              v-if="exporting"
              class="animate-spin rounded-full h-4 w-4 border-b-2 border-white"
            ></span>
            <span v-else class="material-symbols-outlined text-[18px]"
              >download</span
            >
            {{ exporting ? "Mengunduh..." : "Export Data" }}
          </button>
        </div>
      </div>
      <div class="overflow-x-auto">
        <table class="w-full text-left text-sm whitespace-nowrap">
          <thead
            class="bg-sidebar-light/50 text-text-secondary font-medium border-b border-border-light"
          >
            <tr>
              <th class="px-6 py-4">Mahasiswa</th>
              <th class="px-6 py-4">Pembimbing</th>
              <th class="px-6 py-4 text-center">Bimbingan</th>
              <th class="px-6 py-4">Status Nota</th>
              <th class="px-6 py-4">Terakhir Bimbingan</th>
              <th class="px-6 py-4 text-right">Aksi</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-border-light">
            <tr v-if="notaBimbinganList.length === 0">
              <td colspan="6" class="p-12 text-center text-text-secondary">
                Tidak ada data
              </td>
            </tr>
            <tr
              v-for="item in notaBimbinganList"
              :key="item.id"
              class="group hover:bg-sidebar-light/30 transition-colors"
            >
              <td class="px-6 py-4">
                <div class="flex items-center gap-3">
                  <div
                    class="size-10 rounded-full flex items-center justify-center text-xs font-bold shrink-0"
                    :class="getAvatarColor(item.mahasiswa?.nama)"
                  >
                    {{ getInitials(item.mahasiswa?.nama) }}
                  </div>
                  <div>
                    <p class="font-bold text-text-main text-sm">
                      {{ item.mahasiswa?.nama || "-" }}
                    </p>
                    <p
                      class="text-xs text-text-secondary font-medium font-mono"
                    >
                      {{ item.mahasiswa?.nim || "-" }}
                    </p>
                    <p
                      class="text-xs text-text-secondary mt-0.5 truncate max-w-[200px]"
                      :title="item.judul"
                    >
                      {{ item.judul || "-" }}
                    </p>
                  </div>
                </div>
              </td>
              <td class="px-6 py-4">
                <div class="flex flex-col gap-1.5">
                  <div
                    v-for="p in item.pembimbing"
                    :key="p.id"
                    class="flex items-center gap-2"
                  >
                    <div
                      class="bg-blue-100 flex items-center justify-center size-7 rounded-full text-primary border border-blue-200 shrink-0"
                    >
                      <span class="material-symbols-outlined text-[14px]"
                        >person</span
                      >
                    </div>
                    <div>
                      <p class="font-semibold text-text-main text-xs">
                        {{ p.dosen?.full_name || "-" }}
                      </p>
                      <span class="text-[10px] text-text-secondary"
                        >NIP. {{ p.dosen?.nip || "-" }}</span
                      >
                    </div>
                  </div>
                </div>
              </td>
              <td class="px-6 py-4 text-center">
                <div class="inline-flex flex-col items-center">
                  <span class="text-lg font-bold text-text-main">{{
                    item.approved_bimbingan || 0
                  }}</span>
                  <span class="text-[10px] text-text-secondary font-medium"
                    >/ {{ item.total_bimbingan || 0 }} sesi</span
                  >
                </div>
              </td>
              <td class="px-6 py-4">
                <span
                  class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-medium"
                  :class="getStatusClass(item.nota_status)"
                >
                  <span class="material-symbols-outlined text-[14px]">{{
                    getStatusIcon(item.nota_status)
                  }}</span>
                  {{ getStatusLabel(item.nota_status) }}
                </span>
              </td>
              <td class="px-6 py-4 text-xs font-medium text-text-secondary">
                {{ formatDate(item.tanggal_selesai) }}
              </td>
              <td class="px-6 py-4 text-right">
                <div class="flex items-center justify-end gap-2">
                  <button
                    @click="cetakNota(item)"
                    :disabled="generating === item.id"
                    class="inline-flex items-center justify-center gap-2 px-4 py-2 text-xs font-bold text-white bg-primary rounded-lg hover:bg-primary/90 transition-all shadow-sm shadow-primary/20 disabled:opacity-50"
                  >
                    <span
                      v-if="generating === item.id"
                      class="animate-spin rounded-full h-3.5 w-3.5 border-b-2 border-white"
                    ></span>
                    <span v-else class="material-symbols-outlined text-[16px]"
                      >print</span
                    >
                    {{ generating === item.id ? "Loading..." : "Cetak Nota" }}
                  </button>
                  <button
                    @click="viewDetail(item)"
                    class="size-8 flex items-center justify-center rounded-lg border border-border-light text-text-secondary hover:text-primary hover:bg-white hover:border-primary transition-all"
                    title="Lihat Detail"
                  >
                    <span class="material-symbols-outlined text-[18px]"
                      >visibility</span
                    >
                  </button>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
      <!-- Pagination -->
      <div
        class="flex items-center justify-between px-6 py-4 border-t border-border-light"
      >
        <p class="text-sm text-text-secondary">
          Menampilkan
          <span class="font-medium text-text-main">{{
            pagination.from || 0
          }}</span>
          sampai
          <span class="font-medium text-text-main">{{
            pagination.to || 0
          }}</span>
          dari
          <span class="font-medium text-text-main">{{ pagination.total }}</span>
          data
        </p>
        <div class="flex gap-1">
          <button
            @click="goToPage(pagination.current_page - 1)"
            :disabled="pagination.current_page <= 1"
            class="px-3 py-1.5 rounded-md border border-border-light text-text-secondary text-sm font-medium hover:bg-background-light disabled:opacity-50"
          >
            <span class="material-symbols-outlined text-[18px]"
              >chevron_left</span
            >
          </button>
          <button
            class="px-3 py-1.5 rounded-md bg-primary text-white text-sm font-medium"
          >
            {{ pagination.current_page }}
          </button>
          <button
            @click="goToPage(pagination.current_page + 1)"
            :disabled="pagination.current_page >= pagination.last_page"
            class="px-3 py-1.5 rounded-md border border-border-light text-text-secondary text-sm font-medium hover:bg-background-light disabled:opacity-50"
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
import { ref, onMounted, reactive } from "vue";
import { useRouter } from "vue-router";
import adminService from "../../../services/adminService";

const router = useRouter();
const loading = ref(true);
const generating = ref(null);
const exporting = ref(false);
const notaBimbinganList = ref([]);
const searchQuery = ref("");

const stats = reactive({
  siap_cetak: 0,
  menunggu_upload: 0,
  total_diterbitkan: 0,
});

const pagination = reactive({
  current_page: 1,
  last_page: 1,
  total: 0,
  from: 0,
  to: 0,
});

let searchTimeout = null;

const fetchNotaBimbingan = async () => {
  try {
    loading.value = true;
    const params = {
      page: pagination.current_page,
      search: searchQuery.value,
    };
    const response = await adminService.getNotaBimbingan(params);
    if (response.success) {
      notaBimbinganList.value = response.data.data || response.data;
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
    console.error("Failed to fetch nota bimbingan:", error);
  } finally {
    loading.value = false;
  }
};

const debouncedSearch = () => {
  clearTimeout(searchTimeout);
  searchTimeout = setTimeout(() => {
    pagination.current_page = 1;
    fetchNotaBimbingan();
  }, 300);
};

const goToPage = (page) => {
  if (page >= 1 && page <= pagination.last_page) {
    pagination.current_page = page;
    fetchNotaBimbingan();
  }
};

const cetakNota = async (item) => {
  try {
    generating.value = item.id;
    const response = await adminService.getNotaBimbinganPdf(item.id);
    const blob = new Blob([response.data], { type: "application/pdf" });
    const url = window.URL.createObjectURL(blob);
    window.open(url, "_blank");
  } catch (error) {
    console.error("Failed to generate nota:", error);
    alert("Gagal generate Nota Bimbingan");
  } finally {
    generating.value = null;
  }
};

const viewDetail = (item) => {
  router.push(`/admin/bimbingan/${item.id}`);
};

const exportData = async () => {
  try {
    exporting.value = true;
    const params = { search: searchQuery.value };
    const response = await adminService.exportNotaBimbingan(params);
    const blob = new Blob([response.data], {
      type: "text/csv;charset=utf-8;",
    });
    const url = window.URL.createObjectURL(blob);
    const link = document.createElement("a");
    link.href = url;
    link.setAttribute(
      "download",
      `nota_bimbingan_${new Date().toISOString().slice(0, 10)}.csv`,
    );
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
    window.URL.revokeObjectURL(url);
  } catch (error) {
    console.error("Failed to export data:", error);
    alert("Gagal export data");
  } finally {
    exporting.value = false;
  }
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

const getStatusClass = (status) => {
  const classes = {
    diterbitkan: "bg-green-50 text-green-600 border border-green-100",
    siap_cetak: "bg-blue-50 text-blue-600 border border-blue-100",
    proses: "bg-orange-50 text-orange-600 border border-orange-100",
  };
  return classes[status] || "bg-gray-50 text-gray-600 border border-gray-100";
};

const getStatusIcon = (status) => {
  const icons = {
    diterbitkan: "verified",
    siap_cetak: "print",
    proses: "pending",
  };
  return icons[status] || "help";
};

const getStatusLabel = (status) => {
  const labels = {
    diterbitkan: "Diterbitkan",
    siap_cetak: "Siap Cetak",
    proses: "Proses",
  };
  return labels[status] || status || "-";
};

onMounted(() => {
  fetchNotaBimbingan();
});
</script>
