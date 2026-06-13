<template>
  <div class="flex flex-col gap-6">
    <!-- Top Controls -->
    <div class="flex flex-col md:flex-row gap-3 items-start md:items-center justify-between">
      <div class="flex flex-col sm:flex-row gap-3 flex-1 w-full">
        <div class="relative flex-1">
          <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-text-secondary text-[20px]">search</span>
          <input
            v-model="searchQuery"
            @input="debouncedSearch"
            type="text"
            placeholder="Cari Mahasiswa, NIM, atau Judul..."
            class="w-full pl-10 pr-4 py-2.5 border border-border-light rounded-lg bg-white dark:bg-white/5 text-text-main text-sm focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all"
          />
        </div>
        <select
          v-model="filterStatus"
          @change="handleFilterChange"
          class="px-3 py-2.5 border border-border-light rounded-lg bg-white dark:bg-white/5 text-text-main text-sm focus:ring-2 focus:ring-primary/20 focus:border-primary"
        >
          <option value="">Semua Status</option>
          <option value="pengajuan">Pengajuan</option>
          <option value="disetujui">Disetujui</option>
          <option value="proposal">Proposal</option>
          <option value="sempro">Seminar Proposal</option>
          <option value="bimbingan">Bimbingan</option>
          <option value="lulus">Lulus</option>
        </select>
        <label class="flex items-center gap-2 px-3 py-2.5 border border-border-light rounded-lg bg-white dark:bg-white/5 text-sm cursor-pointer select-none whitespace-nowrap">
          <input type="checkbox" v-model="onlySimilar" @change="handleFilterChange" class="accent-primary size-4" />
          <span class="text-text-main font-medium">≥ 70% saja</span>
        </label>
      </div>
      <div class="flex items-center gap-2">
        <Transition name="fade">
          <div v-if="selectedItems.length > 0" class="flex items-center gap-2">
            <span class="text-sm text-text-secondary font-medium whitespace-nowrap mr-2">
              {{ selectedItems.length }} dipilih
            </span>
            <button
              @click="bulkApprove"
              class="inline-flex items-center gap-1.5 px-3 py-2.5 text-sm font-medium text-white bg-green-600 hover:bg-green-700 rounded-lg transition-colors whitespace-nowrap shadow-sm shadow-green-500/20"
            >
              <span class="material-symbols-outlined text-[18px]">check_circle</span>
              Setujui Terpilih
            </button>
            <button
              @click="bulkReject"
              class="inline-flex items-center gap-1.5 px-3 py-2.5 text-sm font-medium text-white bg-red-600 hover:bg-red-700 rounded-lg transition-colors whitespace-nowrap shadow-sm shadow-red-500/20"
            >
              <span class="material-symbols-outlined text-[18px]">cancel</span>
              Tolak Terpilih
            </button>
          </div>
        </Transition>
        <button
          @click="recalculateAll"
          :disabled="recalculating"
          class="inline-flex items-center gap-1.5 px-3 py-2.5 text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 rounded-lg transition-colors whitespace-nowrap shadow-sm disabled:opacity-50"
        >
          <span class="material-symbols-outlined text-[18px]" :class="{ 'animate-spin': recalculating }">autorenew</span>
          {{ recalculating ? 'Menghitung...' : 'Hitung Ulang Semua' }}
        </button>
      </div>
    </div>

    <!-- Loading -->
    <div v-if="loading" class="p-12 text-center">
      <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-primary mx-auto"></div>
      <p class="text-text-secondary text-sm mt-3">Memuat data similarity...</p>
    </div>

    <!-- Table -->
    <DataTableScroll v-else>
      <table class="w-full text-left text-sm whitespace-nowrap">
        <thead class="bg-sidebar-light/50 text-text-secondary font-medium border-b border-border-light">
          <tr>
            <th class="px-4 py-4 w-10">
              <input
                v-if="hasPengajuanItems"
                type="checkbox"
                :checked="isAllSelected"
                :indeterminate="isIndeterminate"
                @change="toggleSelectAll"
                class="size-4 rounded border-border-light text-primary focus:ring-primary cursor-pointer accent-primary"
              />
            </th>
            <th class="px-6 py-4 cursor-pointer hover:text-primary transition-colors select-none group" @click="handleSort('mahasiswa_nama')">
              <div class="flex items-center gap-1">
                Mahasiswa
                <span class="material-symbols-outlined text-[16px] text-text-secondary/50 group-hover:text-primary transition-colors">{{ getSortIcon('mahasiswa_nama') }}</span>
              </div>
            </th>
            <th class="px-6 py-4 cursor-pointer hover:text-primary transition-colors select-none group" @click="handleSort('judul')">
              <div class="flex items-center gap-1">
                Judul yang Diajukan
                <span class="material-symbols-outlined text-[16px] text-text-secondary/50 group-hover:text-primary transition-colors">{{ getSortIcon('judul') }}</span>
              </div>
            </th>
            <th class="px-6 py-4 cursor-pointer hover:text-primary transition-colors select-none group" @click="handleSort('max_similarity')">
              <div class="flex items-center gap-1">
                Similarity Tertinggi
                <span class="material-symbols-outlined text-[16px] text-text-secondary/50 group-hover:text-primary transition-colors">{{ getSortIcon('max_similarity') }}</span>
              </div>
            </th>
            <th class="px-6 py-4">Jumlah Mirip</th>
            <th class="px-6 py-4">Status</th>
            <th class="px-6 py-4 text-right">Aksi</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-border-light">
          <tr v-if="items.length === 0">
            <td colspan="7" class="px-6 py-12 text-center text-text-secondary">
              Tidak ada data similarity
            </td>
          </tr>
          <tr
            v-for="item in items"
            :key="item.id"
            class="group hover:bg-sidebar-light/30 transition-colors"
            :class="{ 'bg-primary/5': selectedItems.includes(item.id) }"
          >
            <td class="px-4 py-4">
              <input
                v-if="item.status === 'pengajuan'"
                type="checkbox"
                :checked="selectedItems.includes(item.id)"
                @change="toggleSelect(item.id)"
                class="size-4 rounded border-border-light text-primary focus:ring-primary cursor-pointer accent-primary"
              />
            </td>
            <td class="px-6 py-4">
              <div class="flex items-center gap-3">
                <div
                  class="size-10 rounded-full flex items-center justify-center text-xs font-bold shrink-0"
                  :class="getAvatarColor(item.mahasiswa?.nama)"
                >
                  {{ getInitials(item.mahasiswa?.nama) }}
                </div>
                <div>
                  <p class="font-bold text-text-main text-sm">{{ item.mahasiswa?.nama || '-' }}</p>
                  <p class="text-xs text-text-secondary font-medium">{{ item.mahasiswa?.nim || '-' }}</p>
                </div>
              </div>
            </td>
            <td class="px-6 py-4 text-text-main whitespace-normal min-w-[250px]" :title="item.judul">
              {{ item.judul || '-' }}
            </td>
            <td class="px-6 py-4">
              <span
                v-if="item.max_similarity > 0"
                class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-bold"
                :class="getSimilarityBadgeClass(item.max_similarity)"
              >
                {{ item.max_similarity.toFixed(1) }}%
              </span>
              <span v-else class="text-text-secondary text-xs">-</span>
            </td>
            <td class="px-6 py-4">
              <span v-if="item.similar_count > 0" class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-bold bg-amber-50 text-amber-700 border border-amber-200">
                {{ item.similar_count }} judul
              </span>
              <span v-else class="text-text-secondary text-xs">0</span>
            </td>
            <td class="px-6 py-4">
              <span
                class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-medium"
                :class="getStatusClass(item.status)"
              >
                <span class="w-1.5 h-1.5 rounded-full" :class="getStatusDot(item.status)"></span>
                {{ getStatusLabel(item.status) }}
              </span>
            </td>
            <td class="px-6 py-4 text-right">
              <div class="flex items-center justify-end gap-1">
                <button
                  v-if="item.status === 'pengajuan'"
                  @click="approveItem(item)"
                  class="p-2 text-green-600 hover:bg-green-50 dark:hover:bg-green-900/20 rounded-lg transition-colors"
                  title="Setujui"
                >
                  <span class="material-symbols-outlined text-[20px]">check_circle</span>
                </button>
                <button
                  v-if="item.status === 'pengajuan'"
                  @click="rejectItem(item)"
                  class="p-2 text-red-600 hover:bg-red-50 dark:hover:bg-red-900/20 rounded-lg transition-colors"
                  title="Tolak"
                >
                  <span class="material-symbols-outlined text-[20px]">cancel</span>
                </button>
                <button
                  v-if="item.max_similarity > 0"
                  @click="openDetailModal(item)"
                  class="p-2 text-primary hover:bg-blue-50 dark:hover:bg-blue-900/20 rounded-lg transition-colors"
                  title="Lihat Judul Mirip"
                >
                  <span class="material-symbols-outlined text-[20px]">compare_arrows</span>
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

    <!-- Detail Modal: Lihat Judul Mirip -->
    <Teleport to="body">
    <Transition name="modal-fade">
      <div
        v-if="showDetailModal"
        class="fixed inset-0 bg-black/50 flex items-center justify-center z-50 p-4"
        @click.self="showDetailModal = false"
      >
        <div class="bg-white dark:bg-surface-light rounded-xl shadow-2xl w-full max-w-4xl max-h-[90vh] overflow-hidden flex flex-col">
          <div class="p-6 border-b border-border-light flex items-center justify-between shrink-0">
            <div>
              <h2 class="text-xl font-bold text-text-main">Judul Mirip</h2>
              <p class="text-sm text-text-secondary mt-1 max-w-lg whitespace-normal" :title="detailSkripsi?.judul">
                {{ detailSkripsi?.judul }}
              </p>
              <p class="text-xs text-text-secondary mt-0.5">
                {{ detailSkripsi?.mahasiswa?.nama }} ({{ detailSkripsi?.mahasiswa?.nim }})
              </p>
            </div>
            <button @click="showDetailModal = false" class="p-2 text-text-secondary hover:text-text-main hover:bg-gray-100 dark:hover:bg-white/10 rounded-lg transition-colors">
              <span class="material-symbols-outlined">close</span>
            </button>
          </div>
          <div class="p-6 overflow-y-auto flex-1">
            <div v-if="loadingDetail" class="text-center py-8">
              <div class="animate-spin rounded-full h-6 w-6 border-b-2 border-primary mx-auto"></div>
              <p class="text-text-secondary text-sm mt-2">Memuat data...</p>
            </div>
            <div v-else-if="detailSimilarities.length === 0" class="text-center py-8 text-text-secondary">
              Tidak ada judul mirip ditemukan.
            </div>
            <div v-else class="space-y-3">
              <div
                v-for="sim in detailSimilarities"
                :key="sim.id"
                class="p-4 rounded-xl border transition-all hover:shadow-sm"
                :class="getDetailCardBorder(sim.similarity_score)"
              >
                <div class="flex items-start justify-between gap-4">
                  <div class="flex-1 min-w-0">
                    <p class="font-semibold text-text-main text-sm leading-relaxed">{{ sim.compared_skripsi.judul }}</p>
                    <div class="flex flex-wrap items-center gap-x-4 gap-y-1 mt-2 text-xs text-text-secondary">
                      <span class="flex items-center gap-1">
                        <span class="material-symbols-outlined text-[14px]">person</span>
                        {{ sim.compared_skripsi.mahasiswa.nama }}
                      </span>
                      <span class="flex items-center gap-1">
                        <span class="material-symbols-outlined text-[14px]">badge</span>
                        {{ sim.compared_skripsi.mahasiswa.nim }}
                      </span>
                      <span class="flex items-center gap-1">
                        <span class="material-symbols-outlined text-[14px]">school</span>
                        {{ sim.compared_skripsi.mahasiswa.prodi }}
                      </span>
                      <span class="flex items-center gap-1">
                        <span class="material-symbols-outlined text-[14px]">calendar_month</span>
                        {{ sim.compared_skripsi.tahun_akademik }}
                      </span>
                      <span
                        class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-medium"
                        :class="getStatusClass(sim.compared_skripsi.status)"
                      >
                        <span class="w-1.5 h-1.5 rounded-full" :class="getStatusDot(sim.compared_skripsi.status)"></span>
                        {{ getStatusLabel(sim.compared_skripsi.status) }}
                      </span>
                    </div>
                  </div>
                  <div class="shrink-0 text-right">
                    <span
                      class="inline-flex items-center gap-1 px-3 py-1.5 rounded-full text-sm font-bold"
                      :class="getSimilarityBadgeClass(sim.similarity_score)"
                    >
                      {{ sim.similarity_score.toFixed(1) }}%
                    </span>
                    <p class="text-xs text-text-secondary mt-1">{{ getCategoryLabel(sim.category) }}</p>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </Transition>
    </Teleport>
  </div>
</template>

<script setup>
import { ref, computed, onMounted, reactive } from "vue";
import adminService from "../../../services/adminService";
import DataTableScroll from "../../../components/ui/DataTableScroll.vue";
import TablePagination from "../../../components/ui/TablePagination.vue";

const loading = ref(true);
const recalculating = ref(false);
const items = ref([]);
const searchQuery = ref("");
const filterStatus = ref("");
const onlySimilar = ref(true);
const showDetailModal = ref(false);
const loadingDetail = ref(false);
const detailSkripsi = ref(null);
const detailSimilarities = ref([]);
const selectedItems = ref([]);

const sorting = reactive({
  by: "max_similarity",
  order: "desc",
});

const pagination = reactive({
  current_page: 1,
  last_page: 1,
  per_page: 15,
  total: 0,
  from: 0,
  to: 0,
});

let searchTimeout = null;

const hasPengajuanItems = computed(() => {
  return items.value.some(item => item.status === 'pengajuan');
});

const isAllSelected = computed(() => {
  const pengajuanItems = items.value.filter(item => item.status === 'pengajuan');
  return pengajuanItems.length > 0 && pengajuanItems.every(item => selectedItems.value.includes(item.id));
});

const isIndeterminate = computed(() => {
  return selectedItems.value.length > 0 && !isAllSelected.value;
});

const toggleSelectAll = () => {
  if (isAllSelected.value) {
    selectedItems.value = [];
  } else {
    selectedItems.value = items.value.filter(item => item.status === 'pengajuan').map(item => item.id);
  }
};

const toggleSelect = (id) => {
  const idx = selectedItems.value.indexOf(id);
  if (idx >= 0) {
    selectedItems.value.splice(idx, 1);
  } else {
    selectedItems.value.push(id);
  }
};

const fetchData = async () => {
  try {
    loading.value = true;
    const params = {
      page: pagination.current_page,
      per_page: pagination.per_page,
      sort_by: sorting.by,
      sort_order: sorting.order,
      has_similarity: onlySimilar.value,
    };
    if (searchQuery.value) params.search = searchQuery.value;
    if (filterStatus.value) params.status = filterStatus.value;

    const res = await adminService.getSimilarity(params);
    if (res.success) {
      items.value = res.data.data;
      pagination.current_page = res.data.current_page;
      pagination.last_page = res.data.last_page;
      pagination.per_page = res.data.per_page;
      pagination.total = res.data.total;
      pagination.from = res.data.from || 0;
      pagination.to = res.data.to || 0;
    }
  } catch (error) {
    console.error("Failed to fetch similarity:", error);
  } finally {
    loading.value = false;
  }
};

const debouncedSearch = () => {
  clearTimeout(searchTimeout);
  searchTimeout = setTimeout(() => {
    pagination.current_page = 1;
    fetchData();
  }, 300);
};

const handleFilterChange = () => {
  pagination.current_page = 1;
  fetchData();
};

const goToPage = (page) => {
  if (page >= 1 && page <= pagination.last_page) {
    pagination.current_page = page;
    fetchData();
  }
};

const changePerPage = (perPage) => {
  pagination.per_page = perPage;
  pagination.current_page = 1;
  fetchData();
};

const handleSort = (column) => {
  if (sorting.by === column) {
    sorting.order = sorting.order === "asc" ? "desc" : "asc";
  } else {
    sorting.by = column;
    sorting.order = "desc";
  }
  fetchData();
};

const getSortIcon = (column) => {
  if (sorting.by !== column) return "unfold_more";
  return sorting.order === "asc" ? "expand_less" : "expand_more";
};

const openDetailModal = async (item) => {
  showDetailModal.value = true;
  loadingDetail.value = true;
  detailSkripsi.value = item;
  detailSimilarities.value = [];
  try {
    const res = await adminService.getSimilarityDetail(item.id);
    if (res.success) {
      detailSkripsi.value = res.data.skripsi;
      detailSimilarities.value = res.data.similarities;
    }
  } catch (error) {
    console.error("Failed to fetch similarity detail:", error);
  } finally {
    loadingDetail.value = false;
  }
};

const recalculateAll = async () => {
  if (!confirm("Hitung ulang similarity untuk semua judul skripsi? Proses ini mungkin memakan waktu.")) return;
  try {
    recalculating.value = true;
    const res = await adminService.recalculateAllSimilarity();
    if (res.message) {
      alert(res.message);
    }
    fetchData();
  } catch (error) {
    console.error("Failed to recalculate all:", error);
    alert("Gagal menghitung ulang similarity.");
  } finally {
    recalculating.value = false;
  }
};

const approveItem = async (item) => {
  if (!confirm("Apakah Anda yakin ingin menyetujui pengajuan ini?")) return;
  try {
    loading.value = true;
    const formData = new FormData();
    formData.append("_method", "PUT");
    formData.append("status", "disetujui");
    formData.append("is_active", true);
    formData.append("alasan", "Pengajuan disetujui");
    await adminService.updateSkripsi(item.id, formData);
    fetchData();
  } catch (error) {
    console.error("Failed to approve:", error);
    alert("Gagal menyetujui: " + (error.response?.data?.message || error.message));
    loading.value = false;
  }
};

const rejectItem = async (item) => {
  if (!confirm("Apakah Anda yakin ingin menolak pengajuan ini?")) return;
  try {
    loading.value = true;
    const formData = new FormData();
    formData.append("_method", "PUT");
    formData.append("status", "ditolak");
    formData.append("alasan", "Pengajuan ditolak");
    await adminService.updateSkripsi(item.id, formData);
    fetchData();
  } catch (error) {
    console.error("Failed to reject:", error);
    alert("Gagal menolak: " + (error.response?.data?.message || error.message));
    loading.value = false;
  }
};

const bulkApprove = async () => {
  if (!confirm(`Setujui ${selectedItems.value.length} pengajuan terpilih?`)) return;
  try {
    loading.value = true;
    for (const id of selectedItems.value) {
      const formData = new FormData();
      formData.append("_method", "PUT");
      formData.append("status", "disetujui");
      formData.append("is_active", true);
      formData.append("alasan", "Pengajuan disetujui secara massal");
      await adminService.updateSkripsi(id, formData);
    }
    selectedItems.value = [];
    fetchData();
  } catch (error) {
    console.error("Failed to bulk approve:", error);
    alert("Gagal menyetujui secara massal.");
    loading.value = false;
  }
};

const bulkReject = async () => {
  if (!confirm(`Tolak ${selectedItems.value.length} pengajuan terpilih?`)) return;
  try {
    loading.value = true;
    for (const id of selectedItems.value) {
      const formData = new FormData();
      formData.append("_method", "PUT");
      formData.append("status", "ditolak");
      formData.append("alasan", "Pengajuan ditolak secara massal");
      await adminService.updateSkripsi(id, formData);
    }
    selectedItems.value = [];
    fetchData();
  } catch (error) {
    console.error("Failed to bulk reject:", error);
    alert("Gagal menolak secara massal.");
    loading.value = false;
  }
};

// Helpers
const getInitials = (name) => {
  if (!name) return "?";
  return name.split(" ").map((n) => n[0]).join("").substring(0, 2).toUpperCase();
};

const getAvatarColor = (name) => {
  const colors = [
    "bg-blue-100 text-blue-600",
    "bg-purple-100 text-purple-600",
    "bg-orange-100 text-orange-600",
    "bg-green-100 text-green-600",
    "bg-pink-100 text-pink-600",
    "bg-cyan-100 text-cyan-600",
  ];
  if (!name) return colors[0];
  const index = name.charCodeAt(0) % colors.length;
  return colors[index];
};

const getSimilarityBadgeClass = (score) => {
  if (score >= 90) return "bg-red-100 text-red-700 border border-red-200";
  if (score >= 80) return "bg-orange-100 text-orange-700 border border-orange-200";
  if (score >= 70) return "bg-yellow-100 text-yellow-700 border border-yellow-200";
  return "bg-green-100 text-green-700 border border-green-200";
};

const getDetailCardBorder = (score) => {
  if (score >= 90) return "border-red-200 bg-red-50/50 dark:bg-red-900/10";
  if (score >= 80) return "border-orange-200 bg-orange-50/50 dark:bg-orange-900/10";
  if (score >= 70) return "border-yellow-200 bg-yellow-50/50 dark:bg-yellow-900/10";
  return "border-border-light bg-white dark:bg-white/5";
};

const getCategoryLabel = (category) => {
  const labels = {
    sangat_mirip: "Sangat Mirip",
    mirip: "Mirip",
    perlu_ditinjau: "Perlu Ditinjau",
    tidak_mirip: "Tidak Mirip",
  };
  return labels[category] || category;
};

const getStatusClass = (status) => {
  const classes = {
    pengajuan: "bg-gray-50 text-gray-600 border border-gray-100",
    disetujui: "bg-green-100 text-green-700 border border-green-200",
    ditolak: "bg-red-100 text-red-700 border border-red-200",
    proposal: "bg-yellow-50 text-yellow-600 border border-yellow-100",
    bimbingan: "bg-purple-50 text-purple-600 border border-purple-100",
    sempro: "bg-blue-50 text-blue-600 border border-blue-100",
    lulus: "bg-green-50 text-green-600 border border-green-100",
  };
  return classes[status] || "bg-gray-50 text-gray-600 border border-gray-100";
};

const getStatusDot = (status) => {
  const dots = {
    pengajuan: "bg-gray-500",
    disetujui: "bg-green-600",
    ditolak: "bg-red-600",
    proposal: "bg-yellow-600",
    bimbingan: "bg-purple-600",
    sempro: "bg-blue-600",
    lulus: "bg-green-600",
  };
  return dots[status] || "bg-gray-600";
};

const getStatusLabel = (status) => {
  const labels = {
    pengajuan: "Pengajuan",
    disetujui: "Disetujui",
    ditolak: "Ditolak",
    proposal: "Proposal",
    bimbingan: "Bimbingan",
    sempro: "Sem. Proposal",
    lulus: "Lulus",
  };
  return labels[status] || status;
};

onMounted(() => {
  fetchData();
});
</script>

<style scoped>
.fade-enter-active,
.fade-leave-active {
  transition: opacity 0.2s ease;
}
.fade-enter-from,
.fade-leave-to {
  opacity: 0;
}
</style>
