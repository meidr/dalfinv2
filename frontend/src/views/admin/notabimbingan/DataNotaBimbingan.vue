<template>
  <div class="flex flex-col gap-6">
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
            <p class="text-primary text-xs font-semibold">Ready for issuance</p>
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
              Waiting for student action
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
            <p class="text-green-600 text-xs font-semibold">This semester</p>
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
      class="flex flex-col rounded-xl border border-border-light bg-surface-light overflow-hidden shadow-sm"
    >
      <div
        class="p-6 border-b border-border-light flex flex-wrap gap-4 justify-between items-center bg-gray-50/50"
      >
        <div>
          <h3 class="text-text-main text-lg font-bold">
            Mahasiswa Selesai Bimbingan
          </h3>
          <p class="text-text-secondary text-xs">
            Daftar mahasiswa yang telah mendapat persetujuan pembimbing untuk
            maju ujian.
          </p>
        </div>
        <div class="flex gap-2">
          <div class="relative">
            <input
              v-model="searchQuery"
              @input="debouncedSearch"
              class="pl-9 pr-4 py-2 rounded-lg border border-border-light bg-white text-sm w-64 focus:ring-1 focus:ring-primary"
              placeholder="Cari mahasiswa..."
            />
            <span
              class="material-symbols-outlined absolute left-2 top-2 text-[18px] text-text-secondary"
              >search</span
            >
          </div>
          <button
            @click="exportData"
            class="flex items-center gap-2 text-white text-sm font-bold bg-primary hover:bg-primary/90 px-4 py-2 rounded-lg transition-colors shadow-sm shadow-primary/20"
          >
            <span class="material-symbols-outlined text-[18px]">download</span>
            Export Data
          </button>
        </div>
      </div>
      <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
          <thead>
            <tr class="bg-sidebar-light/50 border-b border-border-light">
              <th
                class="p-4 text-[10px] font-bold tracking-widest text-text-secondary uppercase w-1/4"
              >
                Mahasiswa
              </th>
              <th
                class="p-4 text-[10px] font-bold tracking-widest text-text-secondary uppercase w-1/4"
              >
                Pembimbing
              </th>
              <th
                class="p-4 text-[10px] font-bold tracking-widest text-text-secondary uppercase"
              >
                Status Bimbingan
              </th>
              <th
                class="p-4 text-[10px] font-bold tracking-widest text-text-secondary uppercase"
              >
                Selesai Pada
              </th>
              <th
                class="p-4 text-[10px] font-bold tracking-widest text-text-secondary uppercase text-right"
              >
                Aksi
              </th>
            </tr>
          </thead>
          <tbody class="divide-y divide-border-light">
            <tr v-if="notaBimbinganList.length === 0">
              <td colspan="5" class="p-12 text-center text-text-secondary">
                Tidak ada data
              </td>
            </tr>
            <tr
              v-for="item in notaBimbinganList"
              :key="item.id"
              class="group hover:bg-sidebar-light/30 transition-colors"
            >
              <td class="p-4">
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
                    <p
                      class="text-[10px] text-text-secondary truncate max-w-[150px] mt-0.5"
                    >
                      {{ item.skripsi?.judul || "-" }}
                    </p>
                  </div>
                </div>
              </td>
              <td class="p-4">
                <div class="flex flex-col">
                  <span class="text-sm font-medium text-text-main">{{
                    item.dosen?.nama_lengkap || "-"
                  }}</span>
                  <span class="text-[10px] text-text-secondary"
                    >NIP. {{ item.dosen?.nip || "-" }}</span
                  >
                </div>
              </td>
              <td class="p-4">
                <span
                  class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[11px] font-bold"
                  :class="getStatusClass(item.status)"
                >
                  <span class="material-symbols-outlined text-[14px]"
                    >check_circle</span
                  >
                  {{ getStatusLabel(item.status) }}
                </span>
              </td>
              <td class="p-4 text-xs font-medium text-text-secondary">
                {{ formatDate(item.tanggal_selesai) }}
              </td>
              <td class="p-4 text-right">
                <div class="flex items-center justify-end gap-2">
                  <button
                    @click="cetakNota(item)"
                    :disabled="generating === item.id"
                    class="inline-flex items-center justify-center gap-2 px-4 py-2 text-xs font-bold text-white bg-primary rounded-lg hover:bg-primary/90 transition-all shadow-sm shadow-primary/20 disabled:opacity-50"
                  >
                    <span class="material-symbols-outlined text-[16px]"
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
        class="p-4 border-t border-border-light flex items-center justify-between bg-gray-50/50"
      >
        <span class="text-xs text-text-secondary">
          Menampilkan {{ pagination.from || 0 }}-{{ pagination.to || 0 }} dari
          {{ pagination.total }} data
        </span>
        <div class="flex gap-1">
          <button
            @click="goToPage(pagination.current_page - 1)"
            :disabled="pagination.current_page <= 1"
            class="size-8 flex items-center justify-center rounded-lg border border-border-light text-text-secondary hover:bg-sidebar-light transition-colors disabled:opacity-50"
          >
            <span class="material-symbols-outlined text-[18px]"
              >chevron_left</span
            >
          </button>
          <button
            class="size-8 flex items-center justify-center rounded-lg bg-primary text-white font-bold text-xs"
          >
            {{ pagination.current_page }}
          </button>
          <button
            @click="goToPage(pagination.current_page + 1)"
            :disabled="pagination.current_page >= pagination.last_page"
            class="size-8 flex items-center justify-center rounded-lg border border-border-light text-text-secondary hover:bg-sidebar-light transition-colors"
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
    const response = await adminService.getNotaBimbinganPdf(item.skripsi_id);
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
  router.push(`/admin/skripsi/${item.skripsi_id}`);
};

const exportData = () => {
  alert("Fitur export akan segera tersedia");
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
  if (status === "selesai")
    return "bg-green-50 text-green-600 border border-green-100";
  return "bg-gray-50 text-gray-600 border border-gray-100";
};

const getStatusLabel = (status) => {
  const labels = { selesai: "Selesai", proses: "Proses" };
  return labels[status] || status;
};

onMounted(() => {
  fetchNotaBimbingan();
});
</script>
