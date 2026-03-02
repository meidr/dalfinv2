<template>
  <div class="max-w-7xl mx-auto flex flex-col gap-8 animate-fade-in-up">
    <!-- Header -->
    <div class="flex flex-col gap-1">
      <div class="flex items-center gap-2 text-sm text-text-secondary">
        <router-link
          to="/admin/dashboard"
          class="hover:text-primary transition-colors"
          >Dashboard</router-link
        >
        <span>/</span>
        <span class="text-text-main font-medium">Verifikasi Skripsi</span>
      </div>
      <h1 class="text-text-main text-3xl font-bold leading-tight">
        Verifikasi Perubahan Skripsi
      </h1>
      <p class="text-text-secondary text-sm font-normal">
        Setujui atau tolak perubahan status dan judul skripsi yang diajukan.
      </p>
    </div>

    <!-- Content -->
    <div
      class="flex flex-col bg-surface-light border border-border-light rounded-xl shadow-sm"
    >
      <!-- Toolbar -->
      <div
        class="p-5 border-b border-border-light flex flex-col md:flex-row gap-4 items-center justify-between"
      >
        <!-- Search -->
        <div class="relative w-full md:max-w-md">
          <div
            class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none"
          >
            <span class="material-symbols-outlined text-text-secondary"
              >search</span
            >
          </div>
          <input
            v-model="searchQuery"
            class="block w-full pl-10 pr-3 py-2.5 border border-border-light rounded-lg leading-5 bg-background-light text-text-main placeholder-text-secondary focus:outline-none focus:ring-1 focus:ring-primary focus:border-primary sm:text-sm transition-shadow dark:bg-background"
            placeholder="Cari Mahasiswa..."
            type="text"
          />
        </div>

        <div class="flex items-center gap-2">
          <!-- Bulk action buttons -->
          <Transition name="fade">
            <div v-if="selectedIds.length > 0" class="flex items-center gap-2">
              <span
                class="text-sm text-text-secondary font-medium whitespace-nowrap"
              >
                {{ selectedIds.length }} dipilih
              </span>
              <button
                @click="confirmBulkAction('approve')"
                class="inline-flex items-center gap-1.5 px-3 py-2 text-sm font-medium text-white bg-green-600 hover:bg-green-700 rounded-lg transition-colors"
              >
                <span class="material-symbols-outlined text-[18px]"
                  >check_circle</span
                >
                Setujui Semua
              </button>
              <button
                @click="confirmBulkAction('reject')"
                class="inline-flex items-center gap-1.5 px-3 py-2 text-sm font-medium text-white bg-red-600 hover:bg-red-700 rounded-lg transition-colors"
              >
                <span class="material-symbols-outlined text-[18px]"
                  >cancel</span
                >
                Tolak Semua
              </button>
            </div>
          </Transition>

          <button
            @click="fetchPending"
            class="p-2 text-text-secondary hover:text-primary hover:bg-blue-50 dark:hover:bg-blue-900/20 rounded-lg transition-colors"
            title="Refresh"
          >
            <span class="material-symbols-outlined text-[24px]">refresh</span>
          </button>
        </div>
      </div>

      <!-- Loading -->
      <div v-if="loading" class="p-12 text-center">
        <div
          class="animate-spin rounded-full h-8 w-8 border-b-2 border-primary mx-auto"
        ></div>
        <p class="text-text-secondary text-sm mt-3">Memuat data...</p>
      </div>

      <!-- Table -->
      <div v-else class="overflow-x-auto">
        <table class="w-full text-left text-sm whitespace-nowrap">
          <thead
            class="bg-sidebar-light/50 text-text-secondary font-medium border-b border-border-light"
          >
            <tr>
              <th class="px-4 py-4 w-10">
                <input
                  type="checkbox"
                  :checked="isAllSelected"
                  :indeterminate="isIndeterminate"
                  @change="toggleSelectAll"
                  class="size-4 rounded border-border-light text-primary focus:ring-primary cursor-pointer accent-primary"
                />
              </th>
              <th class="px-6 py-4">Mahasiswa</th>
              <th class="px-6 py-4">Perubahan Judul</th>
              <th class="px-6 py-4">Perubahan Status</th>
              <th class="px-6 py-4">Alasan</th>
              <th class="px-6 py-4">Oleh</th>
              <th class="px-6 py-4">Tanggal</th>
              <th class="px-6 py-4 text-right">Aksi</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-border-light">
            <tr v-if="filteredList.length === 0">
              <td
                colspan="8"
                class="px-6 py-12 text-center text-text-secondary"
              >
                Tidak ada data verifikasi pending
              </td>
            </tr>
            <tr
              v-for="item in filteredList"
              :key="item.id"
              class="group hover:bg-sidebar-light/30 transition-colors"
              :class="{ 'bg-primary/5': selectedIds.includes(item.id) }"
            >
              <td class="px-4 py-4">
                <input
                  type="checkbox"
                  :checked="selectedIds.includes(item.id)"
                  @change="toggleSelect(item.id)"
                  class="size-4 rounded border-border-light text-primary focus:ring-primary cursor-pointer accent-primary"
                />
              </td>
              <td class="px-6 py-4">
                <div class="flex items-center gap-3">
                  <div
                    class="size-10 rounded-full flex items-center justify-center text-xs font-bold shrink-0 bg-primary/10 text-primary"
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
              <td class="px-6 py-4 max-w-xs">
                <div v-if="item.judul_lama !== item.judul_baru">
                  <p
                    class="text-xs text-red-500 line-through truncate"
                    :title="item.judul_lama"
                  >
                    {{ item.judul_lama }}
                  </p>
                  <p
                    class="text-sm text-green-600 font-medium truncate"
                    :title="item.judul_baru"
                  >
                    {{ item.judul_baru }}
                  </p>
                </div>
                <span v-else class="text-text-secondary">-</span>
              </td>
              <td class="px-6 py-4">
                <div
                  v-if="item.status_lama !== item.status_baru"
                  class="flex flex-col items-start gap-1"
                >
                  <span
                    class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-gray-100 text-gray-800 line-through"
                  >
                    {{ getStatusLabel(item.status_lama) }}
                  </span>
                  <span
                    class="material-symbols-outlined text-[16px] text-text-secondary ml-2"
                    >arrow_downward</span
                  >
                  <span
                    class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium"
                    :class="getStatusClass(item.status_baru)"
                  >
                    {{ getStatusLabel(item.status_baru) }}
                  </span>
                </div>
                <span v-else class="text-text-secondary">-</span>
              </td>
              <td
                class="px-6 py-4 text-text-secondary max-w-xs truncate"
                :title="item.alasan"
              >
                {{ item.alasan || "-" }}
              </td>
              <td class="px-6 py-4 text-text-secondary">
                {{ item.updated_by?.name || "System" }}
              </td>
              <td class="px-6 py-4 text-text-secondary text-xs">
                {{ formatDate(item.created_at) }}
              </td>
              <td class="px-6 py-4 text-right">
                <div class="flex items-center justify-end gap-2">
                  <button
                    @click="confirmAction(item, 'approve')"
                    class="p-2 text-green-600 hover:bg-green-50 rounded-lg transition-colors"
                    title="Setujui"
                  >
                    <span class="material-symbols-outlined text-[20px]"
                      >check</span
                    >
                  </button>
                  <button
                    @click="confirmAction(item, 'reject')"
                    class="p-2 text-red-600 hover:bg-red-50 rounded-lg transition-colors"
                    title="Tolak"
                  >
                    <span class="material-symbols-outlined text-[20px]"
                      >close</span
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
        <div class="flex gap-2">
          <button
            @click="goToPage(pagination.current_page - 1)"
            :disabled="pagination.current_page <= 1"
            class="px-3 py-1.5 rounded-md border border-border-light text-text-secondary text-sm font-medium hover:bg-background-light disabled:opacity-50"
          >
            Sebelumnya
          </button>
          <button
            @click="goToPage(pagination.current_page + 1)"
            :disabled="pagination.current_page >= pagination.last_page"
            class="px-3 py-1.5 rounded-md border border-border-light text-text-secondary text-sm font-medium hover:bg-background-light disabled:opacity-50"
          >
            Selanjutnya
          </button>
        </div>
      </div>
    </div>

    <!-- Confirmation Modal -->
    <Transition name="modal-fade">
      <div
        v-if="showConfirmModal"
        class="fixed inset-0 bg-black/50 flex items-center justify-center z-50 p-4"
      >
        <div
          class="bg-white dark:bg-surface-light rounded-xl shadow-2xl w-full max-w-sm"
        >
          <div class="p-6">
            <h3 class="text-lg font-bold text-text-main mb-2">
              {{
                actionType === "approve"
                  ? "Setujui Perubahan?"
                  : "Tolak Perubahan?"
              }}
            </h3>
            <p class="text-text-secondary text-sm mb-6">
              <template v-if="isBulkAction">
                Apakah Anda yakin ingin
                {{ actionType === "approve" ? "menyetujui" : "menolak" }}
                <span class="font-semibold text-text-main">{{
                  selectedIds.length
                }}</span>
                perubahan yang dipilih?
              </template>
              <template v-else>
                Apakah Anda yakin ingin
                {{ actionType === "approve" ? "menyetujui" : "menolak" }}
                perubahan ini?
              </template>
            </p>
            <div class="flex gap-3">
              <button
                @click="showConfirmModal = false"
                class="flex-1 px-4 py-2.5 border border-border-light rounded-lg text-text-secondary hover:bg-background-light transition-colors"
              >
                Batal
              </button>
              <button
                @click="processAction"
                :disabled="processing"
                class="flex-1 px-4 py-2.5 text-white rounded-lg transition-colors disabled:opacity-50"
                :class="
                  actionType === 'approve'
                    ? 'bg-green-600 hover:bg-green-700'
                    : 'bg-red-600 hover:bg-red-700'
                "
              >
                {{
                  processing
                    ? "Memproses..."
                    : actionType === "approve"
                      ? "Ya, Setujui"
                      : "Ya, Tolak"
                }}
              </button>
            </div>
          </div>
        </div>
      </div>
    </Transition>
  </div>
</template>

<script setup>
import { ref, reactive, computed, onMounted } from "vue";
import adminService from "../../../services/adminService";
import { format } from "date-fns";
import { id as idLocale } from "date-fns/locale";

const loading = ref(true);
const processing = ref(false);
const pendingList = ref([]);
const searchQuery = ref("");
const showConfirmModal = ref(false);
const selectedItem = ref(null);
const actionType = ref(""); // 'approve' | 'reject'
const isBulkAction = ref(false);
const selectedIds = ref([]);

const pagination = reactive({
  current_page: 1,
  last_page: 1,
  per_page: 15,
  total: 0,
  from: 0,
  to: 0,
});

// Selection logic
const isAllSelected = computed(() => {
  return (
    filteredList.value.length > 0 &&
    filteredList.value.every((item) => selectedIds.value.includes(item.id))
  );
});

const isIndeterminate = computed(() => {
  return selectedIds.value.length > 0 && !isAllSelected.value;
});

const toggleSelectAll = () => {
  if (isAllSelected.value) {
    selectedIds.value = [];
  } else {
    selectedIds.value = filteredList.value.map((item) => item.id);
  }
};

const toggleSelect = (id) => {
  const idx = selectedIds.value.indexOf(id);
  if (idx >= 0) {
    selectedIds.value.splice(idx, 1);
  } else {
    selectedIds.value.push(id);
  }
};

const fetchPending = async () => {
  try {
    loading.value = true;
    selectedIds.value = [];
    const params = {
      page: pagination.current_page,
      per_page: pagination.per_page,
    };
    const response = await adminService.getSkripsiVerification(params);
    if (response.success) {
      pendingList.value = response.data.data;
      Object.assign(pagination, {
        current_page: response.data.current_page,
        last_page: response.data.last_page,
        per_page: response.data.per_page,
        total: response.data.total,
        from: response.data.from,
        to: response.data.to,
      });
    }
  } catch (error) {
    console.error("Failed to fetch pending verification", error);
  } finally {
    loading.value = false;
  }
};

const filteredList = computed(() => {
  if (!searchQuery.value) return pendingList.value;
  const lower = searchQuery.value.toLowerCase();
  return pendingList.value.filter(
    (item) =>
      item.skripsi?.mahasiswa?.nama?.toLowerCase().includes(lower) ||
      item.skripsi?.mahasiswa?.nim?.toLowerCase().includes(lower),
  );
});

const confirmAction = (item, type) => {
  selectedItem.value = item;
  actionType.value = type;
  isBulkAction.value = false;
  showConfirmModal.value = true;
};

const confirmBulkAction = (type) => {
  actionType.value = type;
  isBulkAction.value = true;
  showConfirmModal.value = true;
};

const processAction = async () => {
  try {
    processing.value = true;
    let response;

    if (isBulkAction.value) {
      // Bulk action
      if (actionType.value === "approve") {
        response = await adminService.bulkApproveSkripsiVerification(
          selectedIds.value,
        );
      } else {
        response = await adminService.bulkRejectSkripsiVerification(
          selectedIds.value,
        );
      }
    } else {
      // Single action
      if (actionType.value === "approve") {
        response = await adminService.approveSkripsiVerification(
          selectedItem.value.id,
        );
      } else {
        response = await adminService.rejectSkripsiVerification(
          selectedItem.value.id,
        );
      }
    }

    if (response.success) {
      showConfirmModal.value = false;
      selectedIds.value = [];
      fetchPending();
    }
  } catch (error) {
    console.error("Failed to process verification", error);
    alert("Gagal memproses verifikasi");
  } finally {
    processing.value = false;
  }
};

const goToPage = (page) => {
  if (page >= 1 && page <= pagination.last_page) {
    pagination.current_page = page;
    fetchPending();
  }
};

// Utils
const getInitials = (name) => {
  if (!name) return "?";
  return name
    .split(" ")
    .map((n) => n[0])
    .join("")
    .substring(0, 2)
    .toUpperCase();
};

const formatDate = (dateString) => {
  if (!dateString) return "-";
  return format(new Date(dateString), "dd MMM yyyy HH:mm", {
    locale: idLocale,
  });
};

const getStatusLabel = (status) => {
  const map = {
    draft: "Draft",
    pengajuan: "Pengajuan",
    disetujui: "Disetujui",
    ditolak: "Ditolak",
    proposal: "Proposal",
    sempro: "Seminar Proposal",
    bimbingan: "Bimbingan",
    semhas: "Seminar Hasil",
    sidang: "Sidang",
    revisi: "Revisi",
    lulus: "Lulus",
  };
  return map[status] || status;
};

const getStatusClass = (status) => {
  switch (status) {
    case "lulus":
    case "disetujui":
      return "bg-green-100 text-green-700";
    case "ditolak":
      return "bg-red-100 text-red-700";
    case "pengajuan":
    case "draft":
      return "bg-gray-100 text-gray-700";
    default:
      return "bg-blue-100 text-blue-700";
  }
};

onMounted(() => {
  fetchPending();
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
