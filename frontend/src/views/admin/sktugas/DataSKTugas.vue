<template>
  <div class="max-w-7xl mx-auto flex flex-col gap-8 animate-fade-in-up">
    <div class="flex flex-col gap-1">
      <div class="flex items-center gap-2 text-sm text-text-secondary">
        <router-link
          to="/admin/dashboard"
          class="hover:text-primary transition-colors"
          >Dashboard</router-link
        >
        <span>/</span>
        <span class="text-text-main font-medium">SK Tugas</span>
      </div>
      <h1 class="text-text-main text-3xl font-bold leading-tight">
        Manajemen SK Tugas
      </h1>
      <p class="text-text-secondary text-sm font-normal">
        Daftar pengajuan SK Tugas Akhir mahasiswa dengan status Sudah Isi
        Dospem.
      </p>
    </div>

    <div v-if="loading" class="p-12 text-center">
      <div
        class="animate-spin rounded-full h-8 w-8 border-b-2 border-primary mx-auto"
      ></div>
      <p class="text-text-secondary text-sm mt-3">Memuat data...</p>
    </div>

    <div
      v-else
      class="flex flex-col bg-surface-light border border-border-light rounded-xl shadow-sm"
    >
      <div
        class="p-5 border-b border-border-light flex flex-col md:flex-row gap-4 items-center justify-between"
      >
        <div>
          <h3 class="text-text-main text-lg font-bold">Data SK Tugas</h3>
          <p class="text-text-secondary text-xs">
            Menampilkan semua mahasiswa yang sudah memiliki pembimbing.
          </p>
        </div>
        <div
          class="p-5 border-b border-border-light flex flex-col lg:flex-row lg:items-center justify-between gap-4"
        >
          <div class="flex p-1 bg-sidebar-light rounded-lg w-fit">
            <button
              @click="setFilter('')"
              :class="
                filterStatus === ''
                  ? 'bg-background-light text-primary shadow-sm border border-border-light/50 dark:bg-surface'
                  : 'text-text-secondary hover:text-text-main hover:bg-background-light/50'
              "
              class="px-4 py-1.5 text-xs font-bold rounded transition-all"
            >
              Semua
            </button>
            <button
              @click="setFilter('belum_ttd')"
              :class="
                filterStatus === 'belum_ttd'
                  ? 'bg-background-light text-primary shadow-sm border border-border-light/50 dark:bg-surface'
                  : 'text-text-secondary hover:text-text-main hover:bg-background-light/50'
              "
              class="px-4 py-1.5 text-xs font-medium rounded transition-all"
            >
              Belum TTD
            </button>
            <button
              @click="setFilter('sudah_ttd')"
              :class="
                filterStatus === 'sudah_ttd'
                  ? 'bg-background-light text-primary shadow-sm border border-border-light/50 dark:bg-surface'
                  : 'text-text-secondary hover:text-text-main hover:bg-background-light/50'
              "
              class="px-4 py-1.5 text-xs font-medium rounded transition-all"
            >
              Sudah TTD
            </button>
          </div>
          <div class="relative flex-1 md:flex-none">
            <input
              v-model="searchQuery"
              @input="debouncedSearch"
              class="block w-full pl-10 pr-3 py-2.5 border border-border-light rounded-lg leading-5 bg-background-light text-text-main placeholder-text-secondary focus:outline-none focus:ring-1 focus:ring-primary focus:border-primary sm:text-sm transition-shadow dark:bg-background"
              placeholder="Cari mahasiswa..."
            />
            <div
              class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none"
            >
              <span class="material-symbols-outlined text-text-secondary"
                >search</span
              >
            </div>
          </div>
        </div>
      </div>
      <DataTableScroll>
        <table class="w-full text-left text-sm whitespace-nowrap">
          <thead
            class="bg-sidebar-light/50 text-text-secondary font-medium border-b border-border-light"
          >
            <tr>
              <th class="px-6 py-4">Mahasiswa</th>
              <th class="px-6 py-4">Judul Skripsi</th>
              <th class="px-6 py-4">Pembimbing</th>
              <th class="px-6 py-4">Status</th>
              <th class="px-6 py-4 text-right">Aksi</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-border-light">
            <tr v-if="skTugasList.length === 0">
              <td
                colspan="5"
                class="px-6 py-12 text-center text-text-secondary"
              >
                Tidak ada data
              </td>
            </tr>
            <tr
              v-for="item in skTugasList"
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
                    <p class="text-xs text-text-secondary font-medium">
                      {{ item.mahasiswa?.nim || "-" }}
                    </p>
                  </div>
                </div>
              </td>
              <td class="px-6 py-4 max-w-xs truncate" :title="item.judul">
                {{ item.judul || "-" }}
              </td>
              <td class="px-6 py-4">
                <div class="flex flex-col gap-1">
                  <div
                    v-for="(pembimbing, idx) in item.pembimbing || []"
                    :key="idx"
                    class="flex items-center gap-2"
                  >
                    <span
                      class="size-5 rounded-full flex items-center justify-center text-[10px] font-bold"
                      :class="
                        idx === 0
                          ? 'bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400'
                          : 'bg-gray-100 text-gray-600 dark:bg-gray-800 dark:text-gray-400'
                      "
                      >{{ idx + 1 }}</span
                    >
                    <span class="text-xs text-text-main">{{
                      getDosenName(pembimbing.dosen)
                    }}</span>
                  </div>
                  <div
                    v-if="!item.pembimbing?.length"
                    class="text-xs text-text-secondary"
                  >
                    Belum ada pembimbing
                  </div>
                </div>
              </td>
              <td class="px-6 py-4">
                <span
                  class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-medium"
                  :class="getStatusClass(item.sk_status)"
                >
                  <span
                    class="w-1.5 h-1.5 rounded-full"
                    :class="getStatusDot(item.sk_status)"
                  ></span>
                  {{ getStatusLabel(item.sk_status) }}
                </span>
              </td>
              <td class="px-6 py-4 text-right">
                <div class="flex items-center justify-end gap-2">
                  <button
                    v-if="item.sk_status === 'approved'"
                    @click="viewFile(item)"
                    class="p-2 text-text-secondary hover:text-primary hover:bg-blue-50 dark:hover:bg-blue-900/20 rounded-lg transition-colors"
                    title="Lihat File"
                  >
                    <span class="material-symbols-outlined text-[20px]"
                      >visibility</span
                    >
                  </button>
                  <button
                    @click="generateSK(item)"
                    :disabled="generating === item.id"
                    class="p-2 text-text-secondary hover:text-primary hover:bg-blue-50 dark:hover:bg-blue-900/20 rounded-lg transition-colors disabled:opacity-30 disabled:cursor-not-allowed"
                    title="Generate SK"
                  >
                    <span class="material-symbols-outlined text-[20px]"
                      >print</span
                    >
                  </button>
                  <button
                    @click="uploadSK(item)"
                    class="p-2 text-text-secondary hover:text-primary hover:bg-blue-50 dark:hover:bg-blue-900/20 rounded-lg transition-colors"
                    title="Upload SK"
                  >
                    <span class="material-symbols-outlined text-[20px]"
                      >upload_file</span
                    >
                  </button>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </DataTableScroll>
      <TablePagination
        :pagination="pagination"
        :disabled="loading"
        @page-change="goToPage"
        @per-page-change="changePerPage"
      />
    </div>

    <!-- Upload Modal -->
    <Transition name="modal-fade">
      <div
        v-if="showUploadModal"
        class="fixed inset-0 bg-black/50 flex items-center justify-center z-50 p-4"
      >
        <div
          class="bg-white dark:bg-surface-light rounded-xl shadow-2xl w-full max-w-md"
        >
          <div class="p-6 border-b border-border-light">
            <h2 class="text-xl font-bold text-text-main">Upload SK Tugas</h2>
            <p class="text-sm text-text-secondary mt-1">
              {{ selectedItem?.skripsi?.mahasiswa?.nama }}
            </p>
          </div>
          <form @submit.prevent="submitUpload" class="p-6 space-y-4">
            <div>
              <label class="block text-sm font-medium text-text-main mb-1"
                >File SK (PDF)</label
              >
              <input
                type="file"
                @change="handleFileChange"
                accept=".pdf"
                class="w-full px-3 py-2 border border-border-light rounded-lg bg-background-light text-text-main dark:bg-background"
                required
              />
            </div>
            <div>
              <label class="block text-sm font-medium text-text-main mb-1"
                >Nomor SK</label
              >
              <input
                v-model="uploadForm.nomor_sk"
                type="text"
                class="w-full px-3 py-2 border border-border-light rounded-lg focus:ring-2 focus:ring-primary/20 focus:border-primary bg-background-light text-text-main dark:bg-background"
                required
              />
            </div>
            <div>
              <input
                v-model="uploadForm.tanggal_sk"
                type="date"
                class="w-full px-3 py-2 border border-border-light rounded-lg focus:ring-2 focus:ring-primary/20 focus:border-primary bg-background-light text-text-main dark:bg-background"
                required
              />
            </div>
            <div>
              <label class="block text-sm font-medium text-text-main mb-2"
                >Status SK</label
              >
              <div class="flex gap-4">
                <label class="flex items-center gap-2 cursor-pointer">
                  <input
                    type="radio"
                    v-model="uploadForm.status"
                    value="pending"
                    class="text-primary focus:ring-primary"
                  />
                  <span class="text-sm text-text-main">Belum TTD</span>
                </label>
                <label class="flex items-center gap-2 cursor-pointer">
                  <input
                    type="radio"
                    v-model="uploadForm.status"
                    value="approved"
                    class="text-primary focus:ring-primary"
                  />
                  <span class="text-sm text-text-main">Sudah TTD</span>
                </label>
              </div>
            </div>
            <div class="flex gap-3 pt-4">
              <button
                type="button"
                @click="showUploadModal = false"
                class="flex-1 px-4 py-2.5 border border-border-light rounded-lg text-text-secondary hover:bg-background-light transition-colors"
              >
                Batal
              </button>
              <button
                type="submit"
                :disabled="uploading"
                class="flex-1 px-4 py-2.5 bg-primary text-white rounded-lg hover:bg-blue-600 transition-colors disabled:opacity-50"
              >
                {{ uploading ? "Mengupload..." : "Upload" }}
              </button>
            </div>
          </form>
        </div>
      </div>
    </Transition>
  </div>
</template>

<script setup>
import { ref, onMounted, reactive } from "vue";
import adminService from "../../../services/adminService";

const loading = ref(true);
const generating = ref(null);
const uploading = ref(false);
const showUploadModal = ref(false);
const selectedItem = ref(null);
const skTugasList = ref([]);
const searchQuery = ref("");
const filterStatus = ref("");

const pagination = reactive({
  current_page: 1,
  last_page: 1,
  per_page: 10,
  total: 0,
  from: 0,
  to: 0,
});

const uploadForm = reactive({
  file: null,
  nomor_sk: "",
  tanggal_sk: "",
  status: "pending",
});

const setFilter = (status) => {
  filterStatus.value = status;
  pagination.current_page = 1;
  fetchSKTugas();
};

let searchTimeout = null;

const fetchSKTugas = async () => {
  try {
    loading.value = true;
    const params = {
      page: pagination.current_page,
      per_page: pagination.per_page,
      search: searchQuery.value,
      status: filterStatus.value,
    };
    const response = await adminService.getSKTugas(params);

    // Normalize response (handle both axios object and direct data)
    const body = response.data?.success ? response.data : response;

    if (body.success) {
      skTugasList.value = body.data.data || body.data;
      if (body.data.current_page) {
        Object.assign(pagination, {
          current_page: body.data.current_page,
          last_page: body.data.last_page,
          per_page: body.data.per_page || pagination.per_page,
          total: body.data.total,
          from: body.data.from,
          to: body.data.to,
        });
      }
    }
  } catch (error) {
    console.error("Failed to fetch SK Tugas:", error);
  } finally {
    loading.value = false;
  }
};

const debouncedSearch = () => {
  clearTimeout(searchTimeout);
  searchTimeout = setTimeout(() => {
    pagination.current_page = 1;
    fetchSKTugas();
  }, 300);
};

const goToPage = (page) => {
  if (page >= 1 && page <= pagination.last_page) {
    pagination.current_page = page;
    fetchSKTugas();
  }
};

const changePerPage = (perPage) => {
  pagination.per_page = perPage;
  pagination.current_page = 1;
  fetchSKTugas();
};

const generateSK = async (item) => {
  try {
    generating.value = item.id;
    const response = await adminService.getSkTugasPdf(item.id);
    const blob = new Blob([response.data], { type: "application/pdf" });
    const url = window.URL.createObjectURL(blob);
    window.open(url, "_blank");
    // Refresh list to update status to "Sudah TTD"
    await fetchSKTugas();
  } catch (error) {
    console.error("Failed to generate SK:", error);
    alert(
      "Gagal generate SK Tugas. Pastikan KAPRODI sudah dikonfigurasi di Otoritas Jabatan.",
    );
  } finally {
    generating.value = null;
  }
};

const viewFile = async (item) => {
  try {
    const response = await adminService.getSkTugasPdf(item.id);
    const blob = new Blob([response.data], { type: "application/pdf" });
    const url = window.URL.createObjectURL(blob);
    window.open(url, "_blank");
  } catch (error) {
    console.error("Failed to view file:", error);
    alert("Gagal membuka file SK");
  }
};

const uploadSK = (item) => {
  selectedItem.value = item;
  uploadForm.file = null;
  uploadForm.nomor_sk = "";
  uploadForm.tanggal_sk = new Date().toISOString().split("T")[0];
  uploadForm.status = "pending";
  showUploadModal.value = true;
};

const handleFileChange = (e) => {
  uploadForm.file = e.target.files[0];
};

const submitUpload = async () => {
  try {
    uploading.value = true;
    const formData = new FormData();
    formData.append("file", uploadForm.file);
    formData.append("nomor_sk", uploadForm.nomor_sk);
    formData.append("tanggal_sk", uploadForm.tanggal_sk);
    formData.append("skripsi_id", selectedItem.value.id);
    formData.append("jenis", "sk_tugas");
    formData.append("status", uploadForm.status);

    await adminService.uploadDokumen(formData);
    showUploadModal.value = false;
    // Auto switch tab based on upload status
    filterStatus.value =
      uploadForm.status === "approved" ? "sudah_ttd" : "belum_ttd";
    pagination.current_page = 1;
    fetchSKTugas();
    alert("SK berhasil diupload!");
  } catch (error) {
    console.error("Failed to upload SK:", error);
    alert(
      "Gagal upload SK: " + (error.response?.data?.message || error.message),
    );
  } finally {
    uploading.value = false;
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
    "bg-blue-100 text-blue-600 dark:bg-blue-900/30 dark:text-blue-400",
    "bg-purple-100 text-purple-600 dark:bg-purple-900/30 dark:text-purple-400",
    "bg-orange-100 text-orange-600 dark:bg-orange-900/30 dark:text-orange-400",
    "bg-green-100 text-green-600 dark:bg-green-900/30 dark:text-green-400",
  ];
  if (!name) return colors[0];
  const index = name.charCodeAt(0) % colors.length;
  return colors[index];
};

// Helper to get dosen full name
const getDosenName = (dosen) => {
  if (!dosen) return "-";
  const parts = [];
  if (dosen.gelar_depan) parts.push(dosen.gelar_depan);
  if (dosen.nama) parts.push(dosen.nama);
  if (dosen.gelar_belakang) parts.push(dosen.gelar_belakang);
  return parts.join(" ") || dosen.nama || "-";
};

const getStatusClass = (status) => {
  const classes = {
    belum_ada:
      "bg-blue-50 text-blue-600 border border-blue-100 dark:bg-blue-900/20 dark:text-blue-400 dark:border-blue-800",
    pending:
      "bg-yellow-50 text-yellow-600 border border-yellow-100 dark:bg-yellow-900/20 dark:text-yellow-400 dark:border-yellow-800",
    approved:
      "bg-green-50 text-green-600 border border-green-100 dark:bg-green-900/20 dark:text-green-400 dark:border-green-800",
  };
  return (
    classes[status] ||
    "bg-gray-50 text-gray-600 border border-gray-100 dark:bg-gray-900/20 dark:text-gray-400 dark:border-gray-800"
  );
};

const getStatusDot = (status) => {
  const dots = {
    belum_ada: "bg-blue-600",
    pending: "bg-yellow-600",
    approved: "bg-green-600",
  };
  return dots[status] || "bg-gray-600";
};

const getStatusLabel = (status) => {
  const labels = {
    belum_ada: "Siap Proses",
    pending: "Belum TTD",
    approved: "Sudah TTD",
  };
  return labels[status] || status;
};

onMounted(() => {
  fetchSKTugas();
});
</script>
