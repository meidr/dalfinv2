<template>
  <div class="max-w-7xl mx-auto flex flex-col gap-6 animate-fade-in-up">
    <!-- Breadcrumb -->
    <div class="flex flex-wrap items-center gap-2 text-sm">
      <router-link
        to="/admin/bimbingan"
        class="text-text-secondary hover:text-primary font-medium transition-colors"
        >Log Bimbingan</router-link
      >
      <span class="material-symbols-outlined text-text-secondary text-sm"
        >chevron_right</span
      >
      <span class="text-text-main font-bold">Detail</span>
    </div>

    <!-- Loading -->
    <div v-if="loading" class="p-12 text-center">
      <div
        class="animate-spin rounded-full h-8 w-8 border-b-2 border-primary mx-auto"
      ></div>
      <p class="text-text-secondary text-sm mt-3">Memuat data...</p>
    </div>

    <template v-else>
      <!-- Mahasiswa Info Header -->
      <div
        class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 bg-surface-light border border-border-light rounded-xl p-6 shadow-sm"
      >
        <div class="flex items-center gap-4">
          <div
            class="size-14 rounded-full flex items-center justify-center text-lg font-bold shrink-0"
            :class="getAvatarColor(skripsi?.mahasiswa?.nama)"
          >
            {{ getInitials(skripsi?.mahasiswa?.nama) }}
          </div>
          <div>
            <h1 class="text-xl font-bold text-text-main">
              {{ skripsi?.mahasiswa?.nama || "-" }}
            </h1>
            <p class="text-sm text-text-secondary font-mono font-medium mt-0.5">
              {{ skripsi?.mahasiswa?.nim || "-" }}
            </p>
            <p class="text-sm text-text-secondary mt-1 max-w-xl line-clamp-2">
              {{ skripsi?.judul || "-" }}
            </p>
          </div>
        </div>
        <div class="flex flex-col items-end gap-2">
          <div class="flex items-center gap-3">
            <div class="text-center">
              <span class="text-2xl font-bold text-primary">{{
                bimbinganList.length
              }}</span>
              <p
                class="text-[10px] text-text-secondary uppercase tracking-wider font-semibold"
              >
                Total Bimbingan
              </p>
            </div>
          </div>
        </div>
      </div>

      <!-- Pembimbing Info -->
      <div v-if="skripsi?.pembimbing?.length" class="flex flex-wrap gap-3">
        <div
          v-for="p in skripsi.pembimbing"
          :key="p.id"
          class="flex items-center gap-3 bg-surface-light border border-border-light rounded-lg px-4 py-3 shadow-sm"
        >
          <div
            class="bg-blue-100 flex items-center justify-center size-9 rounded-full text-primary border border-blue-200 shrink-0"
          >
            <span class="material-symbols-outlined text-[18px]">person</span>
          </div>
          <div>
            <p class="font-semibold text-text-main text-sm">
              {{ p.dosen?.full_name || "-" }}
            </p>
            <span
              class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-medium bg-background-light text-text-secondary border border-border-light mt-0.5"
            >
              {{ p.jenis === "pembimbing_1" ? "Pembimbing 1" : "Pembimbing 2" }}
            </span>
          </div>
        </div>
      </div>

      <!-- Bimbingan Log Table -->
      <div
        class="flex flex-col rounded-xl border border-border-light bg-surface-light overflow-hidden shadow-sm"
      >
        <div class="px-6 py-4 border-b border-border-light">
          <h2 class="font-bold text-lg text-text-main">Riwayat Bimbingan</h2>
        </div>
        <div class="overflow-x-auto">
          <table class="w-full text-left text-sm whitespace-nowrap">
            <thead
              class="bg-sidebar-light/50 text-text-secondary font-medium border-b border-border-light"
            >
              <tr>
                <th class="px-6 py-3">No</th>
                <th class="px-6 py-3">Tanggal</th>
                <th class="px-6 py-3">Topik</th>
                <th class="px-6 py-3">Deskripsi</th>
                <th class="px-6 py-3">Pembimbing</th>
                <th class="px-6 py-3">Catatan Dosen</th>
                <th class="px-6 py-3 text-center">Status</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-border-light">
              <tr v-if="bimbinganList.length === 0">
                <td colspan="7" class="p-12 text-center text-text-secondary">
                  <span
                    class="material-symbols-outlined text-4xl text-gray-300 block mb-2"
                    >history_edu</span
                  >
                  Belum ada riwayat bimbingan
                </td>
              </tr>
              <tr
                v-for="(log, index) in bimbinganList"
                :key="log.id"
                class="hover:bg-sidebar-light/30 transition-colors"
              >
                <td class="px-6 py-4 text-text-secondary font-medium">
                  {{ index + 1 }}
                </td>
                <td class="px-6 py-4 font-medium">
                  {{ formatDate(log.tanggal) }}
                </td>
                <td class="px-6 py-4">
                  <p class="font-bold text-text-main">
                    {{ log.topik || "-" }}
                  </p>
                </td>
                <td class="px-6 py-4">
                  <p
                    class="text-text-secondary max-w-xs whitespace-normal line-clamp-2"
                  >
                    {{ log.deskripsi || "-" }}
                  </p>
                </td>
                <td class="px-6 py-4">
                  <span
                    class="bg-blue-50 text-blue-700 px-2.5 py-1 rounded text-xs font-bold dark:bg-blue-900/30 dark:text-blue-300"
                  >
                    {{ log.dosen?.full_name || log.dosen?.nama || "-" }}
                  </span>
                </td>
                <td class="px-6 py-4">
                  <p
                    class="text-text-secondary max-w-xs whitespace-normal line-clamp-2"
                  >
                    {{ log.catatan_dosen || "-" }}
                  </p>
                </td>
                <td class="px-6 py-4 text-center">
                  <span
                    class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-bold"
                    :class="getStatusClass(log.status)"
                  >
                    <span class="material-symbols-outlined text-[14px]">{{
                      getStatusIcon(log.status)
                    }}</span>
                    {{ getStatusLabel(log.status) }}
                  </span>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>

      <!-- Back Button -->
      <div>
        <router-link
          to="/admin/bimbingan"
          class="inline-flex items-center gap-2 text-text-secondary hover:text-primary font-medium text-sm transition-colors"
        >
          <span class="material-symbols-outlined text-[18px]">arrow_back</span>
          Kembali ke daftar
        </router-link>
      </div>
    </template>
  </div>
</template>

<script setup>
import { ref, onMounted } from "vue";
import { useRoute } from "vue-router";
import adminService from "../../../services/adminService";

const route = useRoute();
const loading = ref(true);
const skripsi = ref(null);
const bimbinganList = ref([]);

const fetchData = async () => {
  try {
    loading.value = true;
    const skripsiId = route.params.id;

    // Fetch skripsi info (mahasiswa, pembimbing)
    const skripsiRes = await adminService.getSkripsiDetail(skripsiId);
    if (skripsiRes.success) {
      skripsi.value = skripsiRes.data;
    }

    // Fetch bimbingan logs
    const bimbinganRes = await adminService.getBimbinganDetail(skripsiId);
    if (bimbinganRes.success) {
      bimbinganList.value = bimbinganRes.data;
    }
  } catch (error) {
    console.error("Failed to fetch data:", error);
  } finally {
    loading.value = false;
  }
};

const formatDate = (dateStr) => {
  if (!dateStr) return "-";
  const date = new Date(dateStr);
  return date.toLocaleDateString("id-ID", {
    day: "2-digit",
    month: "short",
    year: "numeric",
  });
};

const getStatusClass = (status) => {
  const classes = {
    approved:
      "bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400",
    pending:
      "bg-yellow-100 text-yellow-700 dark:bg-yellow-900/30 dark:text-yellow-400",
    revision:
      "bg-orange-100 text-orange-700 dark:bg-orange-900/30 dark:text-orange-400",
    rejected: "bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400",
  };
  return classes[status] || "bg-gray-100 text-gray-600";
};

const getStatusIcon = (status) => {
  const icons = {
    approved: "check_circle",
    pending: "schedule",
    revision: "edit_note",
    rejected: "cancel",
  };
  return icons[status] || "help";
};

const getStatusLabel = (status) => {
  const labels = {
    approved: "Disetujui",
    pending: "Menunggu",
    revision: "Revisi",
    rejected: "Ditolak",
  };
  return labels[status] || status || "-";
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

onMounted(() => {
  fetchData();
});
</script>
