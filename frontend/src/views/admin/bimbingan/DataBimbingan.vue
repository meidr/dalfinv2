<template>
  <div class="max-w-7xl mx-auto flex flex-col gap-8 animate-fade-in-up">
    <div
      class="flex flex-col md:flex-row md:items-center justify-between gap-4"
    >
      <div class="flex flex-col gap-1">
        <h1 class="text-text-main text-3xl font-bold leading-tight">
          Log Bimbingan
        </h1>
        <p class="text-text-secondary text-sm font-normal">
          Kelola data dan riwayat bimbingan skripsi mahasiswa aktif.
        </p>
      </div>
      <div class="flex flex-col sm:flex-row items-center gap-3">
        <div class="relative w-full sm:w-64 group">
          <span
            class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-text-secondary text-[20px] group-focus-within:text-primary transition-colors"
            >search</span
          >
          <input
            v-model="searchQuery"
            @input="debouncedSearch"
            class="w-full pl-10 pr-4 py-2.5 bg-white border border-border-light rounded-lg text-sm focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all outline-none shadow-sm placeholder:text-gray-400 dark:bg-sidebar-light dark:border-gray-600 dark:text-gray-200"
            placeholder="Cari mahasiswa..."
            type="text"
          />
        </div>
      </div>
    </div>

    <div
      class="flex flex-col rounded-xl border border-border-light bg-surface-light overflow-hidden shadow-sm"
    >
      <!-- Loading -->
      <div v-if="loading" class="p-12 text-center">
        <div
          class="animate-spin rounded-full h-8 w-8 border-b-2 border-primary mx-auto"
        ></div>
        <p class="text-text-secondary text-sm mt-3">Memuat data...</p>
      </div>

      <DataTableScroll v-else>
        <table class="w-full text-left text-sm whitespace-nowrap">
          <thead
            class="bg-sidebar-light/50 text-text-secondary font-medium border-b border-border-light"
          >
            <tr>
              <th class="px-6 py-4">Mahasiswa</th>
              <th class="px-6 py-4">Pembimbing</th>
              <th class="px-6 py-4 text-center">Total Bimbingan</th>
              <th class="px-6 py-4 text-right">Aksi</th>
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
              <td class="px-6 py-4">
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
                    <p class="text-xs text-text-secondary mt-1 line-clamp-1">
                      {{ item.judul || "-" }}
                    </p>
                  </div>
                </div>
              </td>
              <td class="px-6 py-4">
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
                          class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-medium bg-background-light text-text-secondary border border-border-light"
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
              <td class="px-6 py-4 text-center">
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
              <td class="px-6 py-4 text-right">
                <div class="flex justify-end gap-1">
                  <button
                    @click="viewDetail(item)"
                    class="inline-flex items-center justify-center p-2 text-text-secondary hover:text-primary hover:bg-background-light rounded-lg transition-all"
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
      </DataTableScroll>

      <!-- Pagination -->
      <TablePagination
        :pagination="pagination"
        :disabled="loading"
        @page-change="goToPage"
        @per-page-change="changePerPage"
      />
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
  per_page: 10,
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
      per_page: pagination.per_page,
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
          per_page: response.data.per_page || pagination.per_page,
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

const changePerPage = (perPage) => {
  pagination.per_page = perPage;
  pagination.current_page = 1;
  fetchBimbingan();
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
