<template>
  <div class="flex flex-col gap-6">
    <div class="flex flex-col md:flex-row md:items-end justify-between gap-4">
      <div class="flex flex-col gap-1">
        <div class="flex items-center gap-3">
          <span class="bg-primary/10 p-2 rounded-lg text-primary">
            <span class="material-symbols-outlined">cast_for_education</span>
          </span>
          <h1 class="text-text-main text-3xl font-bold leading-tight">
            Log Bimbingan
          </h1>
        </div>
        <p class="text-text-secondary text-sm font-normal pl-[52px]">
          Kelola data dan riwayat bimbingan skripsi mahasiswa aktif.
        </p>
      </div>
    </div>

    <div
      class="flex flex-col rounded-xl border border-border-light bg-surface-light overflow-hidden shadow-sm flex-1 mb-8 dark:bg-surface-light"
    >
      <div
        class="p-4 md:p-6 border-b border-border-light flex justify-between items-center bg-gray-50/50 dark:bg-sidebar-light/50"
      >
        <div class="flex items-center gap-2">
          <span class="text-sm font-bold text-text-main"
            >Daftar Mahasiswa Bimbingan</span
          >
          <span
            class="bg-primary/10 text-primary text-xs font-bold px-2 py-0.5 rounded-full"
            >{{ pagination.total }} Data</span
          >
        </div>
        <div class="flex gap-2">
          <div class="relative">
            <input
              v-model="searchQuery"
              @input="debouncedSearch"
              class="pl-9 pr-4 py-1.5 rounded-md border border-border-light bg-white text-xs w-48 focus:ring-1 focus:ring-primary"
              placeholder="Cari mahasiswa..."
            />
            <span
              class="material-symbols-outlined absolute left-2 top-1.5 text-[16px] text-text-secondary"
              >search</span
            >
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

      <div v-else class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
          <thead>
            <tr
              class="bg-sidebar-light/50 border-b border-border-light dark:bg-sidebar-light/50"
            >
              <th
                class="p-4 md:pl-6 text-[11px] font-bold tracking-widest text-text-secondary uppercase w-1/3"
              >
                Mahasiswa
              </th>
              <th
                class="p-4 text-[11px] font-bold tracking-widest text-text-secondary uppercase w-1/3"
              >
                Pembimbing
              </th>
              <th
                class="p-4 text-[11px] font-bold tracking-widest text-text-secondary uppercase w-1/6 text-center"
              >
                Total Bimbingan
              </th>
              <th
                class="p-4 md:pr-6 text-[11px] font-bold tracking-widest text-text-secondary uppercase w-1/6 text-right"
              >
                Aksi
              </th>
            </tr>
          </thead>
          <tbody class="divide-y divide-border-light">
            <tr v-if="bimbinganList.length === 0">
              <td colspan="4" class="p-12 text-center text-text-secondary">
                Tidak ada data bimbingan
              </td>
            </tr>
            <tr
              v-for="item in bimbinganList"
              :key="item.id"
              class="group hover:bg-sidebar-light/30 transition-colors"
            >
              <td class="p-4 md:pl-6">
                <div class="flex items-center gap-4">
                  <div
                    class="size-10 rounded-full flex items-center justify-center text-xs font-bold shrink-0"
                    :class="getAvatarColor(item.mahasiswa?.nama)"
                  >
                    {{ getInitials(item.mahasiswa?.nama) }}
                  </div>
                  <div>
                    <p
                      class="font-bold text-text-main text-sm group-hover:text-primary transition-colors"
                    >
                      {{ item.mahasiswa?.nama || "-" }}
                    </p>
                    <p
                      class="text-xs text-text-secondary font-medium font-mono mt-0.5"
                    >
                      {{ item.mahasiswa?.nim || "-" }}
                    </p>
                    <p
                      class="text-[10px] text-text-secondary mt-1 line-clamp-1"
                    >
                      {{ item.judul || "-" }}
                    </p>
                  </div>
                </div>
              </td>
              <td class="p-4">
                <div class="flex flex-col gap-2">
                  <div
                    v-for="pembimbing in item.pembimbing"
                    :key="pembimbing.id"
                    class="flex items-center gap-3"
                  >
                    <div
                      class="bg-blue-100 flex items-center justify-center size-8 rounded-full text-primary border border-blue-200 shrink-0"
                    >
                      <span class="material-symbols-outlined text-[16px]"
                        >person</span
                      >
                    </div>
                    <div>
                      <p class="font-semibold text-text-main text-xs">
                        {{ pembimbing.dosen?.full_name || "-" }}
                      </p>
                      <div class="flex items-center gap-2 mt-0.5">
                        <span
                          class="inline-flex items-center px-1.5 py-0.5 rounded text-[9px] font-medium bg-gray-100 text-gray-600 border border-gray-200"
                        >
                          {{
                            pembimbing.jenis === "pembimbing_1"
                              ? "Pembimbing 1"
                              : "Pembimbing 2"
                          }}
                        </span>
                      </div>
                    </div>
                  </div>
                </div>
              </td>
              <td class="p-4 text-center">
                <div class="inline-flex flex-col items-center justify-center">
                  <span class="text-xl font-bold text-text-main">{{
                    item.total_bimbingan || 0
                  }}</span>
                  <span
                    class="text-[10px] text-text-secondary uppercase tracking-wider font-semibold"
                    >Kali</span
                  >
                </div>
              </td>
              <td class="p-4 md:pr-6 text-right">
                <div class="flex justify-end gap-1">
                  <button
                    @click="viewDetail(item)"
                    class="inline-flex items-center justify-center p-2 text-text-secondary hover:text-primary hover:bg-primary/5 rounded-lg transition-all"
                    title="Lihat Detail"
                  >
                    <span class="material-symbols-outlined text-[20px]"
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
        class="p-4 border-t border-border-light flex items-center justify-between bg-gray-50/50 dark:bg-sidebar-light/50"
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
            class="size-8 flex items-center justify-center rounded-lg bg-primary text-white font-bold text-xs shadow-sm shadow-primary/20"
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
const bimbinganList = ref([]);
const searchQuery = ref("");

const pagination = reactive({
  current_page: 1,
  last_page: 1,
  total: 0,
  from: 0,
  to: 0,
});

let searchTimeout = null;

const fetchBimbingan = async () => {
  try {
    loading.value = true;
    const params = {
      page: pagination.current_page,
      search: searchQuery.value,
    };
    const response = await adminService.getBimbingan(params);
    if (response.success) {
      bimbinganList.value = response.data.data || response.data;
      if (response.data.current_page) {
        Object.assign(pagination, {
          current_page: response.data.current_page,
          last_page: response.data.last_page,
          total: response.data.total,
          from: response.data.from,
          to: response.data.to,
        });
      }
    }
  } catch (error) {
    console.error("Failed to fetch bimbingan:", error);
  } finally {
    loading.value = false;
  }
};

const debouncedSearch = () => {
  clearTimeout(searchTimeout);
  searchTimeout = setTimeout(() => {
    pagination.current_page = 1;
    fetchBimbingan();
  }, 300);
};

const goToPage = (page) => {
  if (page >= 1 && page <= pagination.last_page) {
    pagination.current_page = page;
    fetchBimbingan();
  }
};

const viewDetail = (item) => {
  router.push(`/admin/bimbingan/${item.id}`);
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
  fetchBimbingan();
});
</script>
