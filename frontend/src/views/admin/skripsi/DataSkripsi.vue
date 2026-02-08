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
        <span class="text-text-main font-medium">Data Skripsi</span>
      </div>
      <h1 class="text-text-main text-3xl font-bold leading-tight">
        Data Skripsi
      </h1>
      <p class="text-text-secondary text-sm font-normal">
        Kelola data pendaftaran dan status skripsi mahasiswa.
      </p>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
      <div
        class="flex flex-col gap-2 rounded-xl p-6 bg-surface-light border border-border-light shadow-sm hover:shadow-md transition-all hover:-translate-y-1 duration-300"
      >
        <div class="flex items-center justify-between">
          <p
            class="text-text-secondary text-xs font-bold uppercase tracking-wider"
          >
            Total Judul
          </p>
          <div class="bg-primary/10 p-2 rounded-lg text-primary">
            <span class="material-symbols-outlined">library_books</span>
          </div>
        </div>
        <div class="mt-2">
          <p class="text-text-main text-3xl font-bold leading-tight">
            {{ pagination.total }}
          </p>
          <div class="flex items-center gap-1 mt-1">
            <span class="material-symbols-outlined text-green-600 text-[18px]"
              >trending_up</span
            >
            <p class="text-green-600 text-xs font-semibold">Total terdaftar</p>
          </div>
        </div>
      </div>

      <div
        class="flex flex-col gap-2 rounded-xl p-6 bg-surface-light border border-border-light shadow-sm hover:shadow-md transition-all hover:-translate-y-1 duration-300"
      >
        <div class="flex items-center justify-between">
          <p
            class="text-text-secondary text-xs font-bold uppercase tracking-wider"
          >
            Sedang Bimbingan
          </p>
          <div class="bg-orange-100 p-2 rounded-lg text-orange-600">
            <span class="material-symbols-outlined">pending_actions</span>
          </div>
        </div>
        <div class="mt-2">
          <p class="text-text-main text-3xl font-bold leading-tight">
            {{ statsCount.bimbingan }}
          </p>
          <div class="flex items-center gap-1 mt-1">
            <span class="material-symbols-outlined text-orange-600 text-[18px]"
              >groups</span
            >
            <p class="text-orange-600 text-xs font-semibold">Mahasiswa aktif</p>
          </div>
        </div>
      </div>

      <div
        class="flex flex-col gap-2 rounded-xl p-6 bg-surface-light border border-border-light shadow-sm hover:shadow-md transition-all hover:-translate-y-1 duration-300"
      >
        <div class="flex items-center justify-between">
          <p
            class="text-text-secondary text-xs font-bold uppercase tracking-wider"
          >
            Selesai
          </p>
          <div class="bg-green-100 p-2 rounded-lg text-green-600">
            <span class="material-symbols-outlined">check_circle</span>
          </div>
        </div>
        <div class="mt-2">
          <p class="text-text-main text-3xl font-bold leading-tight">
            {{ statsCount.lulus }}
          </p>
          <div class="flex items-center gap-1 mt-1">
            <span class="material-symbols-outlined text-green-600 text-[18px]"
              >celebration</span
            >
            <p class="text-green-600 text-xs font-semibold">Total lulus</p>
          </div>
        </div>
      </div>
    </div>

    <!-- Toolbar & Table -->
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
            @input="debouncedSearch"
            class="block w-full pl-10 pr-3 py-2.5 border border-border-light rounded-lg leading-5 bg-background-light text-text-main placeholder-text-secondary focus:outline-none focus:ring-1 focus:ring-primary focus:border-primary sm:text-sm transition-shadow dark:bg-background"
            placeholder="Cari Mahasiswa, NIM, atau Judul..."
            type="text"
          />
        </div>
        <!-- Actions -->
        <div class="flex gap-3 w-full md:w-auto">
          <select
            v-model="filterStatus"
            @change="fetchSkripsi"
            class="px-4 py-2.5 bg-surface-light border border-border-light rounded-lg text-text-secondary text-sm focus:ring-1 focus:ring-primary"
          >
            <option value="">Semua Status</option>
            <option value="proposal">Proposal</option>
            <option value="bimbingan">Bimbingan</option>
            <option value="sempro">Seminar Proposal</option>
            <option value="semhas">Seminar Hasil</option>
            <option value="ujian">Ujian</option>
            <option value="revisi">Revisi</option>
            <option value="lulus">Lulus</option>
          </select>
          <button
            @click="openAddModal"
            class="flex items-center justify-center gap-2 px-4 py-2.5 bg-primary hover:bg-blue-600 text-white rounded-lg font-medium text-sm shadow-sm shadow-blue-500/20 transition-all w-full md:w-auto whitespace-nowrap"
          >
            <span class="material-symbols-outlined text-[20px]">add</span>
            Tambah Data
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
              <th class="px-6 py-4 w-12">
                <input
                  type="checkbox"
                  class="rounded border-border-light text-primary focus:ring-primary h-4 w-4"
                />
              </th>
              <th class="px-6 py-4">Mahasiswa</th>
              <th class="px-6 py-4">Judul Skripsi</th>
              <th class="px-6 py-4">Pembimbing</th>
              <th class="px-6 py-4">Status</th>
              <th class="px-6 py-4 text-right">Aksi</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-border-light">
            <tr v-if="skripsiList.length === 0">
              <td
                colspan="6"
                class="px-6 py-12 text-center text-text-secondary"
              >
                Tidak ada data skripsi
              </td>
            </tr>
            <tr
              v-for="item in skripsiList"
              :key="item.id"
              class="group hover:bg-background-light/50 transition-colors"
            >
              <td class="px-6 py-4">
                <input
                  type="checkbox"
                  class="rounded border-border-light text-primary focus:ring-primary h-4 w-4"
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
                    <p class="font-bold text-text-main text-sm">
                      {{ item.mahasiswa?.nama || "-" }}
                    </p>
                    <p class="text-xs text-text-secondary font-medium">
                      {{ item.mahasiswa?.nim || "-" }}
                    </p>
                  </div>
                </div>
              </td>
              <td
                class="px-6 py-4 text-text-main max-w-xs truncate"
                :title="item.judul"
              >
                {{ item.judul || "-" }}
              </td>
              <td class="px-6 py-4 text-text-secondary">
                {{ getPembimbing(item.pembimbing) }}
              </td>
              <td class="px-6 py-4">
                <span
                  class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-medium"
                  :class="getStatusClass(item.status)"
                >
                  <span
                    class="w-1.5 h-1.5 rounded-full"
                    :class="getStatusDot(item.status)"
                  ></span>
                  {{ getStatusLabel(item.status) }}
                </span>
              </td>
              <td class="px-6 py-4 text-right">
                <div
                  class="flex items-center justify-end gap-2 opacity-0 group-hover:opacity-100 transition-opacity"
                >
                  <button
                    @click="viewDetail(item.id)"
                    class="p-2 text-text-secondary hover:text-primary hover:bg-blue-50 dark:hover:bg-blue-900/20 rounded-lg transition-colors"
                    title="Lihat Detail"
                  >
                    <span class="material-symbols-outlined text-[20px]"
                      >visibility</span
                    >
                  </button>
                  <button
                    @click="openEditModal(item)"
                    class="p-2 text-text-secondary hover:text-primary hover:bg-blue-50 dark:hover:bg-blue-900/20 rounded-lg transition-colors"
                    title="Edit"
                  >
                    <span class="material-symbols-outlined text-[20px]"
                      >edit</span
                    >
                  </button>
                  <button
                    @click="confirmDelete(item)"
                    class="p-2 text-text-secondary hover:text-red-500 hover:bg-red-50 dark:hover:bg-red-900/20 rounded-lg transition-colors"
                    title="Delete"
                  >
                    <span class="material-symbols-outlined text-[20px]"
                      >delete</span
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

    <!-- Add/Edit Modal -->
    <div
      v-if="showModal"
      class="fixed inset-0 bg-black/50 flex items-center justify-center z-50 p-4"
    >
      <div
        class="bg-white dark:bg-surface-light rounded-xl shadow-2xl w-full max-w-lg max-h-[90vh] overflow-y-auto"
      >
        <div class="p-6 border-b border-border-light">
          <h2 class="text-xl font-bold text-text-main">
            {{ isEditing ? "Edit Skripsi" : "Tambah Skripsi" }}
          </h2>
        </div>
        <form @submit.prevent="saveSkripsi" class="p-6 space-y-4">
          <div>
            <label class="block text-sm font-medium text-text-main mb-1"
              >Judul Skripsi</label
            >
            <textarea
              v-model="form.judul"
              rows="3"
              class="w-full px-3 py-2 border border-border-light rounded-lg focus:ring-2 focus:ring-primary/20 focus:border-primary"
              required
            ></textarea>
          </div>
          <div>
            <label class="block text-sm font-medium text-text-main mb-1"
              >Status</label
            >
            <select
              v-model="form.status"
              class="w-full px-3 py-2 border border-border-light rounded-lg focus:ring-2 focus:ring-primary/20 focus:border-primary"
            >
              <option value="proposal">Proposal</option>
              <option value="bimbingan">Bimbingan</option>
              <option value="sempro">Seminar Proposal</option>
              <option value="semhas">Seminar Hasil</option>
              <option value="ujian">Ujian</option>
              <option value="revisi">Revisi</option>
              <option value="lulus">Lulus</option>
            </select>
          </div>
          <div class="flex gap-3 pt-4">
            <button
              type="button"
              @click="closeModal"
              class="flex-1 px-4 py-2.5 border border-border-light rounded-lg text-text-secondary hover:bg-background-light transition-colors"
            >
              Batal
            </button>
            <button
              type="submit"
              :disabled="saving"
              class="flex-1 px-4 py-2.5 bg-primary text-white rounded-lg hover:bg-blue-600 transition-colors disabled:opacity-50"
            >
              {{ saving ? "Menyimpan..." : "Simpan" }}
            </button>
          </div>
        </form>
      </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div
      v-if="showDeleteModal"
      class="fixed inset-0 bg-black/50 flex items-center justify-center z-50 p-4"
    >
      <div
        class="bg-white dark:bg-surface-light rounded-xl shadow-2xl w-full max-w-md"
      >
        <div class="p-6">
          <div class="flex items-center gap-4 mb-4">
            <div class="p-3 bg-red-100 text-red-600 rounded-full">
              <span class="material-symbols-outlined">warning</span>
            </div>
            <div>
              <h3 class="text-lg font-bold text-text-main">Hapus Skripsi?</h3>
              <p class="text-sm text-text-secondary">
                Tindakan ini tidak dapat dibatalkan.
              </p>
            </div>
          </div>
          <p class="text-text-main mb-6">
            Apakah Anda yakin ingin menghapus skripsi
            <strong>"{{ deleteItem?.judul }}"</strong>?
          </p>
          <div class="flex gap-3">
            <button
              @click="showDeleteModal = false"
              class="flex-1 px-4 py-2.5 border border-border-light rounded-lg text-text-secondary hover:bg-background-light transition-colors"
            >
              Batal
            </button>
            <button
              @click="deleteSkripsi"
              :disabled="deleting"
              class="flex-1 px-4 py-2.5 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors disabled:opacity-50"
            >
              {{ deleting ? "Menghapus..." : "Hapus" }}
            </button>
          </div>
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
const saving = ref(false);
const deleting = ref(false);
const skripsiList = ref([]);
const searchQuery = ref("");
const filterStatus = ref("");
const showModal = ref(false);
const showDeleteModal = ref(false);
const isEditing = ref(false);
const deleteItem = ref(null);

const pagination = reactive({
  current_page: 1,
  last_page: 1,
  per_page: 15,
  total: 0,
  from: 0,
  to: 0,
});

const statsCount = reactive({
  bimbingan: 0,
  lulus: 0,
});

const form = reactive({
  id: null,
  judul: "",
  status: "proposal",
});

let searchTimeout = null;

const fetchSkripsi = async () => {
  try {
    loading.value = true;
    const params = {
      page: pagination.current_page,
      per_page: pagination.per_page,
      search: searchQuery.value,
      status: filterStatus.value,
    };
    const response = await adminService.getSkripsi(params);
    if (response.success) {
      skripsiList.value = response.data.data;
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
    console.error("Failed to fetch skripsi:", error);
  } finally {
    loading.value = false;
  }
};

const fetchStats = async () => {
  try {
    const response = await adminService.getDashboard();
    if (response.success) {
      const data = response.data;
      statsCount.bimbingan = data.skripsi_bimbingan || 0;
      statsCount.lulus = data.skripsi_lulus || 0;
    }
  } catch (error) {
    console.error("Failed to fetch stats:", error);
  }
};

const debouncedSearch = () => {
  clearTimeout(searchTimeout);
  searchTimeout = setTimeout(() => {
    pagination.current_page = 1;
    fetchSkripsi();
  }, 300);
};

const goToPage = (page) => {
  if (page >= 1 && page <= pagination.last_page) {
    pagination.current_page = page;
    fetchSkripsi();
  }
};

const openAddModal = () => {
  isEditing.value = false;
  form.id = null;
  form.judul = "";
  form.status = "proposal";
  showModal.value = true;
};

const openEditModal = (item) => {
  isEditing.value = true;
  form.id = item.id;
  form.judul = item.judul;
  form.status = item.status;
  showModal.value = true;
};

const closeModal = () => {
  showModal.value = false;
};

const saveSkripsi = async () => {
  try {
    saving.value = true;
    if (isEditing.value) {
      await adminService.updateSkripsi(form.id, {
        judul: form.judul,
        status: form.status,
      });
    } else {
      await adminService.createSkripsi({
        judul: form.judul,
        status: form.status,
      });
    }
    closeModal();
    fetchSkripsi();
    fetchStats();
  } catch (error) {
    console.error("Failed to save skripsi:", error);
    alert(
      "Gagal menyimpan data: " +
        (error.response?.data?.message || error.message),
    );
  } finally {
    saving.value = false;
  }
};

const confirmDelete = (item) => {
  deleteItem.value = item;
  showDeleteModal.value = true;
};

const deleteSkripsi = async () => {
  try {
    deleting.value = true;
    await adminService.deleteSkripsi(deleteItem.value.id);
    showDeleteModal.value = false;
    fetchSkripsi();
    fetchStats();
  } catch (error) {
    console.error("Failed to delete skripsi:", error);
    alert(
      "Gagal menghapus data: " +
        (error.response?.data?.message || error.message),
    );
  } finally {
    deleting.value = false;
  }
};

const viewDetail = (id) => {
  router.push(`/admin/skripsi/${id}`);
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

const getPembimbing = (pembimbingList) => {
  if (!pembimbingList || pembimbingList.length === 0) return "-";
  const p1 = pembimbingList.find((p) => p.jenis === "pembimbing_1");
  return (
    p1?.dosen?.nama_lengkap || pembimbingList[0]?.dosen?.nama_lengkap || "-"
  );
};

const getStatusClass = (status) => {
  const classes = {
    proposal:
      "bg-yellow-100 text-yellow-700 dark:bg-yellow-900/30 dark:text-yellow-400",
    bimbingan:
      "bg-purple-100 text-purple-700 dark:bg-purple-900/30 dark:text-purple-400",
    sempro: "bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400",
    semhas: "bg-cyan-100 text-cyan-700 dark:bg-cyan-900/30 dark:text-cyan-400",
    ujian:
      "bg-orange-100 text-orange-700 dark:bg-orange-900/30 dark:text-orange-400",
    revisi:
      "bg-amber-100 text-amber-700 dark:bg-amber-900/30 dark:text-amber-400",
    lulus:
      "bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400",
  };
  return classes[status] || "bg-gray-100 text-gray-700";
};

const getStatusDot = (status) => {
  const dots = {
    proposal: "bg-yellow-500",
    bimbingan: "bg-purple-500",
    sempro: "bg-blue-500",
    semhas: "bg-cyan-500",
    ujian: "bg-orange-500",
    revisi: "bg-amber-500",
    lulus: "bg-green-500",
  };
  return dots[status] || "bg-gray-500";
};

const getStatusLabel = (status) => {
  const labels = {
    proposal: "Proposal",
    bimbingan: "Bimbingan",
    sempro: "Sem. Proposal",
    semhas: "Sem. Hasil",
    ujian: "Ujian",
    revisi: "Revisi",
    lulus: "Lulus",
  };
  return labels[status] || status;
};

onMounted(() => {
  fetchSkripsi();
  fetchStats();
});
</script>
