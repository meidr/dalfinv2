<template>
  <div class="flex flex-col gap-6 animate-fade-in">
    <div
      class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4"
    >
      <div class="flex flex-col gap-1">
        <h1 class="text-3xl font-bold tracking-tight text-text-main">
          Mahasiswa Bimbingan
        </h1>
        <p class="text-text-secondary text-base">
          Kelola progres dan status skripsi mahasiswa bimbingan Anda.
        </p>
      </div>
      <div class="flex gap-2">
        <button
          @click="showFilter = !showFilter"
          class="flex items-center gap-2 bg-surface-light border border-border-light px-4 py-2 rounded-lg text-sm font-medium hover:bg-sidebar-light transition-colors text-text-main"
          :class="{
            'border-primary text-primary': showFilter || hasActiveFilter,
          }"
        >
          <span class="material-symbols-outlined text-[20px]">filter_list</span>
          Filter
          <span
            v-if="hasActiveFilter"
            class="size-5 rounded-full bg-primary text-white text-[10px] font-bold flex items-center justify-center"
            >{{ activeFilterCount }}</span
          >
        </button>
      </div>
    </div>

    <!-- Filter Panel -->
    <div
      v-if="showFilter"
      class="bg-surface-light border border-border-light rounded-xl p-5 flex flex-col gap-4 animate-slide-down"
    >
      <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <!-- Search by name/NIM -->
        <div class="flex flex-col gap-1.5">
          <label class="text-xs font-bold text-text-secondary"
            >Cari Nama / NIM</label
          >
          <div class="relative">
            <span
              class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-text-secondary text-[18px]"
              >search</span
            >
            <input
              v-model="filters.search"
              type="text"
              placeholder="Ketik nama atau NIM..."
              class="w-full pl-10 pr-4 py-2.5 rounded-lg border border-border-light bg-background-light focus:ring-2 focus:ring-primary/20 focus:border-primary outline-none transition-all text-sm"
            />
          </div>
        </div>

        <!-- Prodi filter -->
        <div class="flex flex-col gap-1.5">
          <label class="text-xs font-bold text-text-secondary"
            >Program Studi</label
          >
          <select
            v-model="filters.prodi"
            class="w-full px-4 py-2.5 rounded-lg border border-border-light bg-background-light focus:ring-2 focus:ring-primary/20 focus:border-primary outline-none transition-all text-sm"
          >
            <option value="">Semua Prodi</option>
            <option v-for="p in prodiList" :key="p" :value="p">{{ p }}</option>
          </select>
        </div>

        <!-- Status filter -->
        <div class="flex flex-col gap-1.5">
          <label class="text-xs font-bold text-text-secondary"
            >Status Skripsi</label
          >
          <select
            v-model="filters.status"
            class="w-full px-4 py-2.5 rounded-lg border border-border-light bg-background-light focus:ring-2 focus:ring-primary/20 focus:border-primary outline-none transition-all text-sm"
          >
            <option value="">Semua Status</option>
            <option value="pengajuan">Pengajuan</option>
            <option value="berjalan">Berjalan</option>
            <option value="seminar">Seminar</option>
            <option value="revisi">Revisi</option>
            <option value="selesai">Selesai</option>
          </select>
        </div>
      </div>

      <div class="flex justify-end gap-2 border-t border-border-light pt-3">
        <button
          @click="resetFilters"
          class="px-4 py-2 rounded-lg text-sm font-medium text-text-secondary hover:bg-sidebar-light transition-colors"
        >
          Reset
        </button>
        <button
          @click="applyFilters"
          class="px-4 py-2 rounded-lg bg-primary text-white text-sm font-medium hover:bg-blue-600 transition-colors"
        >
          Terapkan
        </button>
      </div>
    </div>

    <!-- Loading -->
    <div
      v-if="loading"
      class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6"
    >
      <div
        v-for="i in 6"
        :key="i"
        class="bg-surface-light border border-border-light rounded-xl p-5 animate-pulse"
      >
        <div class="flex items-start justify-between mb-4">
          <div class="flex items-center gap-3">
            <div
              class="size-10 rounded-full bg-gray-200 dark:bg-gray-700"
            ></div>
            <div>
              <div
                class="h-4 w-28 bg-gray-200 dark:bg-gray-700 rounded mb-1"
              ></div>
              <div class="h-3 w-20 bg-gray-200 dark:bg-gray-700 rounded"></div>
            </div>
          </div>
          <div class="h-5 w-12 bg-gray-200 dark:bg-gray-700 rounded"></div>
        </div>
        <div class="h-10 bg-gray-200 dark:bg-gray-700 rounded mb-4"></div>
        <div class="border-t border-border-light pt-3 flex justify-between">
          <div class="h-3 w-24 bg-gray-200 dark:bg-gray-700 rounded"></div>
          <div class="h-3 w-16 bg-gray-200 dark:bg-gray-700 rounded"></div>
        </div>
      </div>
    </div>

    <!-- Student Cards Grid -->
    <div v-else-if="filteredList.length">
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <div
          v-for="s in paginatedList"
          :key="s.id"
          class="bg-surface-light border border-border-light rounded-xl p-5 hover:border-primary/50 transition-colors shadow-sm cursor-pointer group"
          @click="
            $router.push({
              name: 'DosenBimbinganProgress',
              params: { id: s.id },
            })
          "
        >
          <div class="flex items-start justify-between mb-4">
            <div class="flex items-center gap-3">
              <div
                class="size-10 rounded-full flex items-center justify-center text-white text-sm font-bold shrink-0"
                :style="{ backgroundColor: getAvatarColor(s.mahasiswa?.nama) }"
              >
                {{ getInitials(s.mahasiswa?.nama) }}
              </div>
              <div>
                <h3 class="font-bold text-text-main">
                  {{ s.mahasiswa?.nama || "-" }}
                </h3>
                <p class="text-xs text-text-secondary">
                  NIM: {{ s.mahasiswa?.nim || "-" }}
                </p>
              </div>
            </div>
            <span
              class="px-2 py-0.5 rounded text-[10px] font-bold"
              :class="getStatusClass(s.status)"
              >{{ getStatusLabel(s.status) }}</span
            >
          </div>
          <h4
            class="text-sm font-medium text-text-main line-clamp-2 mb-4 h-10 leading-snug"
          >
            {{ s.judul || "Belum ada judul" }}
          </h4>
          <div
            class="flex items-center justify-between text-xs text-text-secondary border-t border-border-light pt-3"
          >
            <span class="flex items-center gap-1">
              <span class="material-symbols-outlined text-[14px]"
                >schedule</span
              >
              {{ getLastUpdate(s) }}
            </span>
            <span class="group-hover:text-primary font-medium transition-colors"
              >Lihat Detail -></span
            >
          </div>
        </div>
      </div>

      <!-- Pagination -->
      <div
        v-if="totalPages > 1"
        class="flex flex-col sm:flex-row items-center justify-between gap-4 mt-6 pt-4 border-t border-border-light"
      >
        <div class="flex items-center gap-3 text-sm text-text-secondary">
          <span>
            Menampilkan
            <span class="font-bold text-text-main">{{ startIndex + 1 }}</span>
            –
            <span class="font-bold text-text-main">{{ endIndex }}</span>
            dari
            <span class="font-bold text-text-main">{{
              filteredList.length
            }}</span>
            mahasiswa
          </span>
          <select
            v-model.number="perPage"
            @change="currentPage = 1"
            class="px-2 py-1 rounded-lg border border-border-light bg-background-light text-xs font-medium focus:ring-2 focus:ring-primary/20 focus:border-primary outline-none"
          >
            <option :value="6">6 / hal</option>
            <option :value="9">9 / hal</option>
            <option :value="12">12 / hal</option>
            <option :value="24">24 / hal</option>
          </select>
        </div>
        <div class="flex items-center gap-1">
          <button
            @click="currentPage = 1"
            :disabled="currentPage === 1"
            class="size-9 rounded-lg flex items-center justify-center text-sm font-medium transition-colors border border-border-light hover:bg-sidebar-light disabled:opacity-30 disabled:cursor-not-allowed"
          >
            <span class="material-symbols-outlined text-[18px]"
              >first_page</span
            >
          </button>
          <button
            @click="currentPage--"
            :disabled="currentPage === 1"
            class="size-9 rounded-lg flex items-center justify-center text-sm font-medium transition-colors border border-border-light hover:bg-sidebar-light disabled:opacity-30 disabled:cursor-not-allowed"
          >
            <span class="material-symbols-outlined text-[18px]"
              >chevron_left</span
            >
          </button>
          <button
            v-for="page in visiblePages"
            :key="page"
            @click="currentPage = page"
            class="size-9 rounded-lg flex items-center justify-center text-sm font-bold transition-colors"
            :class="
              page === currentPage
                ? 'bg-primary text-white shadow-sm'
                : 'border border-border-light hover:bg-sidebar-light text-text-main'
            "
          >
            {{ page }}
          </button>
          <button
            @click="currentPage++"
            :disabled="currentPage === totalPages"
            class="size-9 rounded-lg flex items-center justify-center text-sm font-medium transition-colors border border-border-light hover:bg-sidebar-light disabled:opacity-30 disabled:cursor-not-allowed"
          >
            <span class="material-symbols-outlined text-[18px]"
              >chevron_right</span
            >
          </button>
          <button
            @click="currentPage = totalPages"
            :disabled="currentPage === totalPages"
            class="size-9 rounded-lg flex items-center justify-center text-sm font-medium transition-colors border border-border-light hover:bg-sidebar-light disabled:opacity-30 disabled:cursor-not-allowed"
          >
            <span class="material-symbols-outlined text-[18px]">last_page</span>
          </button>
        </div>
      </div>
    </div>

    <!-- Empty State -->
    <div
      v-else
      class="bg-surface-light border border-border-light rounded-xl p-12 flex flex-col items-center justify-center gap-3 text-center"
    >
      <span
        class="material-symbols-outlined text-5xl text-text-secondary opacity-40"
        >school</span
      >
      <h3 class="text-lg font-bold text-text-main">
        {{
          hasActiveFilter ? "Tidak Ada Hasil" : "Belum Ada Mahasiswa Bimbingan"
        }}
      </h3>
      <p class="text-text-secondary text-sm max-w-md">
        {{
          hasActiveFilter
            ? "Coba ubah filter pencarian Anda."
            : "Anda belum memiliki mahasiswa bimbingan skripsi."
        }}
      </p>
      <button
        v-if="hasActiveFilter"
        @click="resetFilters"
        class="mt-2 px-4 py-2 rounded-lg border border-border-light text-sm font-medium hover:bg-sidebar-light transition-colors"
      >
        Reset Filter
      </button>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from "vue";
import dosenService from "../../services/dosenService";

const loading = ref(true);
const skripsiList = ref([]);
const showFilter = ref(false);

const filters = ref({
  search: "",
  prodi: "",
  status: "",
});

// Fetch data
const fetchData = async () => {
  loading.value = true;
  try {
    const params = {};
    if (filters.value.search) params.search = filters.value.search;
    if (filters.value.status) params.status = filters.value.status;

    const res = await dosenService.getBimbinganList(params);
    if (res.success) {
      skripsiList.value = res.data || [];
    }
  } catch (err) {
    console.error("Failed to fetch bimbingan list:", err);
  } finally {
    loading.value = false;
  }
};

onMounted(fetchData);

// Extract unique prodi names from data
const prodiList = computed(() => {
  const names = skripsiList.value
    .map((s) => s.mahasiswa?.prodi?.nama)
    .filter(Boolean);
  return [...new Set(names)].sort();
});

// Client-side prodi filter (backend doesn't support prodi param)
const filteredList = computed(() => {
  if (!filters.value.prodi) return skripsiList.value;
  return skripsiList.value.filter(
    (s) => s.mahasiswa?.prodi?.nama === filters.value.prodi,
  );
});

// ===== Pagination =====
const currentPage = ref(1);
const perPage = ref(6);

const totalPages = computed(() =>
  Math.ceil(filteredList.value.length / perPage.value),
);

const startIndex = computed(() => (currentPage.value - 1) * perPage.value);
const endIndex = computed(() =>
  Math.min(startIndex.value + perPage.value, filteredList.value.length),
);

const paginatedList = computed(() =>
  filteredList.value.slice(startIndex.value, endIndex.value),
);

const visiblePages = computed(() => {
  const pages = [];
  const total = totalPages.value;
  const current = currentPage.value;
  let start = Math.max(1, current - 2);
  let end = Math.min(total, start + 4);
  if (end - start < 4) start = Math.max(1, end - 4);
  for (let i = start; i <= end; i++) pages.push(i);
  return pages;
});

const hasActiveFilter = computed(
  () => !!(filters.value.search || filters.value.prodi || filters.value.status),
);

const activeFilterCount = computed(() => {
  let count = 0;
  if (filters.value.search) count++;
  if (filters.value.prodi) count++;
  if (filters.value.status) count++;
  return count;
});

const applyFilters = () => {
  currentPage.value = 1;
  fetchData();
};

const resetFilters = () => {
  filters.value = { search: "", prodi: "", status: "" };
  currentPage.value = 1;
  fetchData();
};

// Helpers
const getInitials = (name) => {
  if (!name) return "?";
  return name
    .split(" ")
    .map((n) => n[0])
    .slice(0, 2)
    .join("")
    .toUpperCase();
};

const getAvatarColor = (name) => {
  if (!name) return "#6366f1";
  const colors = [
    "#3b82f6",
    "#8b5cf6",
    "#ec4899",
    "#f59e0b",
    "#10b981",
    "#6366f1",
    "#ef4444",
    "#14b8a6",
  ];
  let hash = 0;
  for (let i = 0; i < name.length; i++)
    hash = name.charCodeAt(i) + ((hash << 5) - hash);
  return colors[Math.abs(hash) % colors.length];
};

const getStatusLabel = (status) => {
  const map = {
    draft: "Draft",
    pengajuan: "Pengajuan",
    disetujui: "Disetujui",
    ditolak: "Ditolak",
    proposal: "Proposal",
    sempro: "Sempro",
    bimbingan: "Bimbingan",
    semhas: "Semhas",
    sidang: "Sidang",
    revisi: "Revisi",
    lulus: "Lulus",
    berjalan: "Berjalan",
    seminar: "Seminar",
    selesai: "Selesai",
  };
  return map[status] || status || "-";
};

const getStatusClass = (status) => {
  const map = {
    draft: "bg-gray-100 text-gray-700 border border-gray-300",
    pengajuan: "bg-amber-100 text-amber-800 border border-amber-300",
    disetujui: "bg-emerald-100 text-emerald-800 border border-emerald-300",
    ditolak: "bg-red-100 text-red-700 border border-red-300",
    proposal: "bg-sky-100 text-sky-800 border border-sky-300",
    sempro: "bg-blue-100 text-blue-800 border border-blue-300",
    bimbingan: "bg-violet-100 text-violet-800 border border-violet-300",
    semhas: "bg-cyan-100 text-cyan-800 border border-cyan-300",
    sidang: "bg-orange-100 text-orange-800 border border-orange-300",
    revisi: "bg-rose-100 text-rose-700 border border-rose-300",
    lulus: "bg-green-100 text-green-800 border border-green-300",
    berjalan: "bg-blue-100 text-blue-800 border border-blue-300",
    seminar: "bg-purple-100 text-purple-800 border border-purple-300",
    selesai: "bg-green-100 text-green-800 border border-green-300",
  };
  return map[status] || "bg-gray-100 text-gray-700 border border-gray-300";
};

const getLastUpdate = (s) => {
  const lastBimbingan = s.bimbingan?.[0]?.tanggal;
  const date = lastBimbingan || s.updated_at;
  if (!date) return "-";

  const diff = Date.now() - new Date(date).getTime();
  const mins = Math.floor(diff / 60000);
  if (mins < 60) return `${mins}m yang lalu`;
  const hours = Math.floor(mins / 60);
  if (hours < 24) return `${hours}h yang lalu`;
  const days = Math.floor(hours / 24);
  if (days < 30) return `${days}d yang lalu`;
  return new Date(date).toLocaleDateString("id-ID", {
    day: "numeric",
    month: "short",
    year: "numeric",
  });
};
</script>

<style scoped>
.animate-fade-in {
  animation: fadeIn 0.3s ease-in-out;
}
.animate-slide-down {
  animation: slideDown 0.2s ease-out;
}
@keyframes fadeIn {
  from {
    opacity: 0;
    transform: translateY(5px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}
@keyframes slideDown {
  from {
    opacity: 0;
    transform: translateY(-10px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}
</style>
