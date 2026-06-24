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
              <th class="px-6 py-4 text-center">Status Ujian</th>
              <th class="px-6 py-4 text-right">Aksi</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-border-light">
            <tr v-if="bimbinganList.length === 0">
              <td colspan="5" class="p-12 text-center text-text-secondary">
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
                        <!-- Bimbingan count badge per pembimbing -->
                        <span
                          v-if="item.eligibility"
                          class="inline-flex items-center px-1.5 py-0.5 rounded text-[10px] font-bold"
                          :class="getEligibilityBadge(item.eligibility, pembimbing.jenis)"
                        >
                          {{ getEligibilityCount(item.eligibility, pembimbing.jenis) }}/{{ getEligibilityRequired(item.eligibility, pembimbing.jenis) }}
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
              <!-- Status Ujian Column -->
              <td class="px-6 py-4 text-center">
                <span
                  class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[11px] font-bold"
                  :class="getSkripsiStatusClass(item.status)"
                >
                  <span class="material-symbols-outlined text-[13px]">{{ getSkripsiStatusIcon(item.status) }}</span>
                  {{ getSkripsiStatusLabel(item.status) }}
                </span>
              </td>
              <td class="px-6 py-4 text-right">
                <div class="flex justify-end gap-1">
                  <!-- Pengajuan Ujian Button (only admin/superadmin, status bimbingan, eligibility met) -->
                  <button
                    v-if="isAdminOrSuperAdmin && canSubmitPengajuan(item)"
                    @click="confirmPengajuan(item)"
                    class="inline-flex items-center justify-center gap-1 px-2.5 py-1.5 text-xs font-semibold text-amber-700 bg-amber-50 hover:bg-amber-100 border border-amber-200 rounded-lg transition-all dark:bg-amber-900/20 dark:text-amber-300 dark:border-amber-700 dark:hover:bg-amber-900/40"
                    title="Ajukan Ujian"
                  >
                    <span class="material-symbols-outlined text-[16px]">school</span>
                    Ajukan
                  </button>
                  <!-- Approve/Reject (only admin/superadmin, status pengajuan_sidang) -->
                  <template v-if="isAdminOrSuperAdmin && item.status === 'pengajuan_sidang'">
                    <button
                      @click="confirmReview(item, 'approve')"
                      class="inline-flex items-center justify-center gap-1 px-2.5 py-1.5 text-xs font-semibold text-green-700 bg-green-50 hover:bg-green-100 border border-green-200 rounded-lg transition-all dark:bg-green-900/20 dark:text-green-300 dark:border-green-700 dark:hover:bg-green-900/40"
                      title="Setujui Ujian"
                    >
                      <span class="material-symbols-outlined text-[16px]">check_circle</span>
                      ACC
                    </button>
                    <button
                      @click="openRejectModal(item)"
                      class="inline-flex items-center justify-center gap-1 px-2.5 py-1.5 text-xs font-semibold text-red-700 bg-red-50 hover:bg-red-100 border border-red-200 rounded-lg transition-all dark:bg-red-900/20 dark:text-red-300 dark:border-red-700 dark:hover:bg-red-900/40"
                      title="Tolak Ujian"
                    >
                      <span class="material-symbols-outlined text-[16px]">cancel</span>
                      Tolak
                    </button>
                  </template>
                  <!-- Detail Button -->
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

    <!-- Pengajuan Ujian Confirmation Modal -->
    <Teleport to="body">
      <div
        v-if="showPengajuanModal"
        class="fixed inset-0 bg-black/50 backdrop-blur-sm z-50 flex items-center justify-center p-4"
        @click.self="closePengajuanModal"
      >
        <div class="bg-surface-light rounded-xl shadow-xl border border-border-light w-full max-w-md max-h-[90vh] overflow-y-auto animate-fade-in-up">
          <div class="p-6 text-center">
            <div class="mx-auto w-12 h-12 bg-amber-100 rounded-full flex items-center justify-center mb-4 dark:bg-amber-900/30">
              <span class="material-symbols-outlined text-amber-600 text-2xl dark:text-amber-400">school</span>
            </div>
            <h3 class="text-lg font-bold text-text-main mb-2">Ajukan Ujian?</h3>
            <p class="text-sm text-text-secondary mb-1">
              Mahasiswa: <strong>{{ pengajuanTarget?.mahasiswa?.nama }}</strong>
            </p>
            <div v-if="pengajuanTarget?.eligibility" class="my-4 text-left bg-sidebar-light/50 rounded-lg p-3 text-xs space-y-1.5 border border-border-light">
              <div class="flex items-center justify-between">
                <span class="text-text-secondary">Bimbingan P1</span>
                <span class="font-bold" :class="pengajuanTarget.eligibility.pembimbing_1.met ? 'text-green-600' : 'text-red-500'">
                  {{ pengajuanTarget.eligibility.pembimbing_1.count }}/{{ pengajuanTarget.eligibility.pembimbing_1.required }}
                  <span class="material-symbols-outlined text-[14px] align-middle">{{ pengajuanTarget.eligibility.pembimbing_1.met ? 'check_circle' : 'cancel' }}</span>
                </span>
              </div>
              <div v-if="pengajuanTarget.eligibility.pembimbing_2.exists" class="flex items-center justify-between">
                <span class="text-text-secondary">Bimbingan P2</span>
                <span class="font-bold" :class="pengajuanTarget.eligibility.pembimbing_2.met ? 'text-green-600' : 'text-red-500'">
                  {{ pengajuanTarget.eligibility.pembimbing_2.count }}/{{ pengajuanTarget.eligibility.pembimbing_2.required }}
                  <span class="material-symbols-outlined text-[14px] align-middle">{{ pengajuanTarget.eligibility.pembimbing_2.met ? 'check_circle' : 'cancel' }}</span>
                </span>
              </div>
            </div>
            <p class="text-sm text-text-secondary mb-6">
              Status skripsi akan berubah menjadi <strong>"Pengajuan Sidang"</strong>.
            </p>
            <div
              class="flex items-start gap-2.5 p-3 mb-4 rounded-lg bg-amber-50 dark:bg-amber-900/10 border border-amber-200 dark:border-amber-800 text-left"
            >
              <span class="material-symbols-outlined text-amber-600 text-[18px] mt-0.5">info</span>
              <p class="text-xs text-amber-800 dark:text-amber-300">
                Jika mahasiswa belum mempunyai SK 6, silakan mengurusnya ke
                staff prodi masing-masing sebelum pengajuan sidang.
              </p>
            </div>
            <div class="mb-5 text-left">
              <label class="block text-sm font-semibold text-text-main mb-1.5">
                Lampiran SK 6 <span class="text-red-500">*</span>
              </label>
              <label
                class="flex items-center gap-3 px-3 py-3 rounded-lg border border-dashed border-border-light hover:border-amber-500 hover:bg-amber-50/50 cursor-pointer transition-colors"
              >
                <span class="material-symbols-outlined text-amber-600">upload_file</span>
                <span class="min-w-0 flex-1">
                  <span class="block text-sm font-medium text-text-main truncate">
                    {{ pengajuanSk6File?.name || "Pilih file SK 6" }}
                  </span>
                  <span class="block text-xs text-text-secondary">
                    PDF, DOC, atau DOCX, maksimal 20 MB
                  </span>
                </span>
                <input
                  type="file"
                  class="sr-only"
                  accept=".pdf,.doc,.docx,application/pdf,application/msword,application/vnd.openxmlformats-officedocument.wordprocessingml.document"
                  @change="handlePengajuanSk6File"
                />
              </label>
              <p v-if="pengajuanSk6Error" class="text-xs text-red-600 mt-1.5">
                {{ pengajuanSk6Error }}
              </p>
            </div>
            <div class="flex items-center justify-center gap-3">
              <button
                @click="closePengajuanModal"
                class="px-4 py-2.5 text-sm font-medium text-text-secondary hover:text-text-main border border-border-light rounded-lg hover:bg-sidebar-light transition-all"
              >
                Batal
              </button>
              <button
                @click="doSubmitPengajuan"
                :disabled="submitting"
                class="px-5 py-2.5 text-sm font-semibold bg-amber-500 hover:bg-amber-600 text-white rounded-lg shadow-sm transition-all disabled:opacity-50 disabled:cursor-not-allowed flex items-center gap-2"
              >
                <span v-if="submitting" class="animate-spin rounded-full h-4 w-4 border-2 border-white border-t-transparent"></span>
                Ajukan Ujian
              </button>
            </div>
          </div>
        </div>
      </div>
    </Teleport>

    <!-- Reject Modal -->
    <Teleport to="body">
      <div
        v-if="showRejectModal"
        class="fixed inset-0 bg-black/50 backdrop-blur-sm z-50 flex items-center justify-center p-4"
        @click.self="closeRejectModal"
      >
        <div class="bg-surface-light rounded-xl shadow-xl border border-border-light w-full max-w-sm animate-fade-in-up">
          <div class="p-6">
            <div class="text-center mb-4">
              <div class="mx-auto w-12 h-12 bg-red-100 rounded-full flex items-center justify-center mb-4 dark:bg-red-900/30">
                <span class="material-symbols-outlined text-red-600 text-2xl dark:text-red-400">cancel</span>
              </div>
              <h3 class="text-lg font-bold text-text-main mb-1">Tolak Pengajuan Ujian?</h3>
              <p class="text-sm text-text-secondary">
                Mahasiswa: <strong>{{ rejectTarget?.mahasiswa?.nama }}</strong>
              </p>
            </div>
            <div class="mb-4">
              <label class="block text-sm font-semibold text-text-main mb-1.5">Alasan Penolakan <span class="text-red-500">*</span></label>
              <textarea
                v-model="rejectReason"
                rows="3"
                placeholder="Tuliskan alasan penolakan..."
                class="w-full px-3 py-2.5 border border-border-light rounded-lg text-sm bg-white dark:bg-sidebar-light focus:ring-2 focus:ring-primary/20 focus:border-primary outline-none transition-all resize-none"
                required
              ></textarea>
            </div>
            <div class="flex items-center justify-center gap-3">
              <button
                @click="closeRejectModal"
                class="px-4 py-2.5 text-sm font-medium text-text-secondary hover:text-text-main border border-border-light rounded-lg hover:bg-sidebar-light transition-all"
              >
                Batal
              </button>
              <button
                @click="doReject"
                :disabled="submitting || !rejectReason.trim()"
                class="px-5 py-2.5 text-sm font-semibold bg-red-600 hover:bg-red-700 text-white rounded-lg shadow-sm transition-all disabled:opacity-50 disabled:cursor-not-allowed flex items-center gap-2"
              >
                <span v-if="submitting" class="animate-spin rounded-full h-4 w-4 border-2 border-white border-t-transparent"></span>
                Tolak
              </button>
            </div>
          </div>
        </div>
      </div>
    </Teleport>

    <!-- Toast Notification -->
    <Teleport to="body">
      <Transition name="toast">
        <div
          v-if="toast.show"
          class="fixed top-6 right-6 z-60 px-4 py-3 rounded-lg shadow-lg text-sm font-medium flex items-center gap-2 max-w-sm"
          :class="{
            'bg-green-600 text-white': toast.type === 'success',
            'bg-red-600 text-white': toast.type === 'error',
          }"
        >
          <span class="material-symbols-outlined text-[18px]">
            {{ toast.type === "success" ? "check_circle" : "error" }}
          </span>
          {{ toast.message }}
        </div>
      </Transition>
    </Teleport>
  </div>
</template>

<script setup>
import { ref, computed, onMounted, reactive } from "vue";
import { useRouter } from "vue-router";
import adminService from "../../../services/adminService";
import { useAuthStore } from "../../../stores/auth";

const authStore = useAuthStore();
const isAdminOrSuperAdmin = computed(() => authStore.isAdmin);

const router = useRouter();
const loading = ref(true);
const bimbinganList = ref([]);
const searchQuery = ref("");
const submitting = ref(false);

// Pengajuan modal
const showPengajuanModal = ref(false);
const pengajuanTarget = ref(null);
const pengajuanSk6File = ref(null);
const pengajuanSk6Error = ref("");

// Reject modal
const showRejectModal = ref(false);
const rejectTarget = ref(null);
const rejectReason = ref("");

// Toast
const toast = ref({ show: false, message: "", type: "success" });
const showToast = (message, type = "success") => {
  toast.value = { show: true, message, type };
  setTimeout(() => { toast.value.show = false; }, 3000);
};

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

// --- Pengajuan Ujian ---
const canSubmitPengajuan = (item) => {
  return (
    ['bimbingan', 'dospem', 'pengajuan_sidang_tolak'].includes(item.status) &&
    item.eligibility?.all_met
  );
};

const confirmPengajuan = (item) => {
  pengajuanTarget.value = item;
  pengajuanSk6File.value = null;
  pengajuanSk6Error.value = "";
  showPengajuanModal.value = true;
};

const closePengajuanModal = () => {
  showPengajuanModal.value = false;
  pengajuanTarget.value = null;
  pengajuanSk6File.value = null;
  pengajuanSk6Error.value = "";
};

const handlePengajuanSk6File = (event) => {
  const file = event.target.files?.[0] || null;
  pengajuanSk6Error.value = "";

  if (!file) {
    pengajuanSk6File.value = null;
    return;
  }

  const extension = file.name.split(".").pop()?.toLowerCase();
  if (!["pdf", "doc", "docx"].includes(extension)) {
    pengajuanSk6File.value = null;
    pengajuanSk6Error.value = "Format SK 6 harus PDF, DOC, atau DOCX.";
    event.target.value = "";
    return;
  }

  if (file.size > 20 * 1024 * 1024) {
    pengajuanSk6File.value = null;
    pengajuanSk6Error.value = "Ukuran file SK 6 maksimal 20 MB.";
    event.target.value = "";
    return;
  }

  pengajuanSk6File.value = file;
};

const doSubmitPengajuan = async () => {
  if (!pengajuanSk6File.value) {
    pengajuanSk6Error.value = "File SK 6 wajib dilampirkan.";
    return;
  }

  try {
    submitting.value = true;
    const formData = new FormData();
    formData.append("skripsi_id", pengajuanTarget.value.id);
    formData.append("file_sk6", pengajuanSk6File.value);
    await adminService.submitPengajuanUjian(formData);
    showToast("Pengajuan ujian berhasil disubmit");
    closePengajuanModal();
    await fetchBimbingan();
  } catch (error) {
    showToast(error.response?.data?.message || "Gagal mengajukan ujian", "error");
  } finally {
    submitting.value = false;
  }
};

// --- Review (Approve/Reject) ---
const confirmReview = async (item, action) => {
  if (action === "approve") {
    try {
      submitting.value = true;
      await adminService.reviewPengajuanUjian({ skripsi_id: item.id, action: "approve" });
      showToast("Pengajuan ujian disetujui");
      await fetchBimbingan();
    } catch (error) {
      showToast(error.response?.data?.message || "Gagal menyetujui", "error");
    } finally {
      submitting.value = false;
    }
  }
};

const openRejectModal = (item) => {
  rejectTarget.value = item;
  rejectReason.value = "";
  showRejectModal.value = true;
};

const closeRejectModal = () => {
  showRejectModal.value = false;
  rejectTarget.value = null;
  rejectReason.value = "";
};

const doReject = async () => {
  try {
    submitting.value = true;
    await adminService.reviewPengajuanUjian({
      skripsi_id: rejectTarget.value.id,
      action: "reject",
      alasan: rejectReason.value,
    });
    showToast("Pengajuan ujian ditolak");
    closeRejectModal();
    await fetchBimbingan();
  } catch (error) {
    showToast(error.response?.data?.message || "Gagal menolak", "error");
  } finally {
    submitting.value = false;
  }
};

// --- Helpers ---
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

const getEligibilityBadge = (eligibility, jenis) => {
  const key = jenis === 'pembimbing_1' ? 'pembimbing_1' : 'pembimbing_2';
  const data = eligibility[key];
  if (!data) return 'bg-sidebar-light text-text-secondary';
  return data.met
    ? 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400'
    : 'bg-red-100 text-red-600 dark:bg-red-900/30 dark:text-red-400';
};

const getEligibilityCount = (eligibility, jenis) => {
  const key = jenis === 'pembimbing_1' ? 'pembimbing_1' : 'pembimbing_2';
  return eligibility[key]?.count ?? 0;
};

const getEligibilityRequired = (eligibility, jenis) => {
  const key = jenis === 'pembimbing_1' ? 'pembimbing_1' : 'pembimbing_2';
  return eligibility[key]?.required ?? 0;
};

const getSkripsiStatusClass = (status) => {
  const map = {
    bimbingan: 'bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400',
    pengajuan_sidang: 'bg-amber-100 text-amber-700 dark:bg-amber-900/30 dark:text-amber-400',
    pengajuan_sidang_tolak: 'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400',
    sidang: 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400',
    lulus: 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-400',
  };
  return map[status] || 'bg-sidebar-light text-text-secondary';
};

const getSkripsiStatusIcon = (status) => {
  const map = {
    bimbingan: 'menu_book',
    pengajuan_sidang: 'pending',
    pengajuan_sidang_tolak: 'cancel',
    sidang: 'check_circle',
    lulus: 'verified',
  };
  return map[status] || 'info';
};

const getSkripsiStatusLabel = (status) => {
  const map = {
    draft: 'Draft',
    pengajuan: 'Pengajuan',
    disetujui: 'Disetujui',
    ditolak: 'Ditolak',
    proposal: 'Proposal',
    sempro: 'Sempro',
    penentuan_dospem: 'Penentuan Dospem',
    bimbingan: 'Bimbingan',
    pengajuan_sidang: 'Pengajuan Sidang',
    pengajuan_sidang_tolak: 'Ditolak',
    sidang: 'Sidang',
    revisi: 'Revisi',
    lulus: 'Lulus',
  };
  return map[status] || status || '-';
};

onMounted(() => {
  fetchBimbingan();
});
</script>

<style scoped>
.toast-enter-active {
  animation: slideInRight 0.3s ease-out;
}
.toast-leave-active {
  animation: slideOutRight 0.3s ease-in;
}
@keyframes slideInRight {
  from { transform: translateX(100%); opacity: 0; }
  to { transform: translateX(0); opacity: 1; }
}
@keyframes slideOutRight {
  from { transform: translateX(0); opacity: 1; }
  to { transform: translateX(100%); opacity: 0; }
}
</style>
