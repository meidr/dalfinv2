<template>
  <div class="max-w-7xl mx-auto flex flex-col gap-8 animate-fade-in-up">
    <div
      class="flex flex-col md:flex-row md:items-center justify-between gap-4"
    >
      <div class="flex flex-col gap-1">
        <h1 class="text-text-main text-3xl font-bold leading-tight">
          Master Data Mahasiswa
        </h1>
        <p class="text-text-secondary text-sm font-normal">
          Kelola data mahasiswa yang terdaftar untuk skripsi.
        </p>
      </div>
      <div class="flex flex-col sm:flex-row items-center gap-3">
        <div class="relative w-full sm:w-64 group">
          <span
            class="material-symbols-outlined absolute left-3 top-2.5 text-text-secondary text-[20px] group-focus-within:text-primary transition-colors"
            >search</span
          >
          <input
            v-model="searchQuery"
            @input="debouncedSearch"
            class="w-full pl-10 pr-4 py-2 bg-surface-light border border-border-light rounded-lg text-sm focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all outline-none"
            placeholder="Cari Nama, NIM..."
            type="text"
          />
        </div>
        <button
          @click="showImportModal = true"
          class="flex items-center justify-center gap-2 bg-green-600 text-white px-4 py-2 rounded-lg text-sm font-bold shadow-sm shadow-green-600/20 hover:bg-green-700 transition-all w-full sm:w-auto"
        >
          <span class="material-symbols-outlined text-[20px]">upload_file</span>
          <span>Import</span>
        </button>
        <button
          @click="openAddModal"
          class="flex items-center justify-center gap-2 bg-primary text-white px-4 py-2 rounded-lg text-sm font-bold shadow-sm shadow-primary/20 hover:bg-primary/90 transition-all w-full sm:w-auto"
        >
          <span class="material-symbols-outlined text-[20px]">add</span>
          <span>Tambah Mahasiswa</span>
        </button>
      </div>
    </div>

    <!-- Stats -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
      <div
        class="flex items-center gap-3 bg-white dark:bg-surface-light px-4 py-3 rounded-xl border border-border-light shadow-sm"
      >
        <div
          class="p-2 bg-green-50 text-green-600 rounded-full dark:bg-green-900/30 dark:text-green-400"
        >
          <span class="material-symbols-outlined text-xl">check_circle</span>
        </div>
        <div>
          <p
            class="text-xs text-text-secondary uppercase font-bold tracking-wider"
          >
            Mahasiswa Aktif
          </p>
          <p class="text-lg font-bold text-text-main">
            {{ statsCount.active }}
          </p>
        </div>
      </div>
      <div
        class="flex items-center gap-3 bg-white dark:bg-surface-light px-4 py-3 rounded-xl border border-border-light shadow-sm"
      >
        <div
          class="p-2 bg-orange-50 text-orange-600 rounded-full dark:bg-orange-900/30 dark:text-orange-400"
        >
          <span class="material-symbols-outlined text-xl">school</span>
        </div>
        <div>
          <p
            class="text-xs text-text-secondary uppercase font-bold tracking-wider"
          >
            Total Mahasiswa
          </p>
          <p class="text-lg font-bold text-text-main">{{ pagination.total }}</p>
        </div>
      </div>
    </div>

    <!-- Table Container -->
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

      <template v-else>
        <div class="overflow-x-auto">
          <table class="w-full text-left text-sm whitespace-nowrap">
            <thead
              class="bg-sidebar-light/50 text-text-secondary font-medium border-b border-border-light"
            >
              <tr>
                <th class="px-6 py-4 w-12">
                  <input
                    type="checkbox"
                    class="rounded border-border-light text-primary focus:ring-primary size-4"
                  />
                </th>
                <th class="px-6 py-4">NIM</th>
                <th class="px-6 py-4">Mahasiswa</th>
                <th class="px-6 py-4">Tahun</th>
                <th class="px-6 py-4">Program Studi</th>
                <th class="px-6 py-4">Status</th>
                <th class="px-6 py-4 text-right">Aksi</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-border-light">
              <tr v-if="mahasiswaList.length === 0">
                <td colspan="7" class="p-12 text-center text-text-secondary">
                  Tidak ada data mahasiswa
                </td>
              </tr>
              <tr
                v-for="item in mahasiswaList"
                :key="item.id"
                class="group hover:bg-sidebar-light/30 transition-colors"
              >
                <td class="px-6 py-4">
                  <div class="flex items-center justify-center">
                    <input
                      type="checkbox"
                      class="rounded border-border-light text-primary focus:ring-primary size-4"
                    />
                  </div>
                </td>
                <td class="px-6 py-4 font-mono text-text-main">
                  {{ item.nim }}
                </td>
                <td class="px-6 py-4">
                  <div class="flex items-center gap-3">
                    <div
                      class="size-8 rounded-full flex items-center justify-center font-bold text-xs shrink-0"
                      :class="getAvatarColor(item.nama)"
                    >
                      {{ getInitials(item.nama) }}
                    </div>
                    <span class="font-bold text-text-main">{{
                      item.nama
                    }}</span>
                  </div>
                </td>
                <td class="px-6 py-4 text-text-secondary">
                  {{ item.tahun?.name || "-" }}
                </td>
                <td class="px-6 py-4 text-text-secondary">
                  {{ item.prodi?.nama || "-" }}
                </td>
                <td class="px-6 py-4">
                  <span
                    class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-medium"
                    :class="
                      item.user?.is_active
                        ? 'bg-green-50 text-green-700 border border-green-200'
                        : 'bg-gray-100 text-gray-700 border border-gray-200'
                    "
                  >
                    <span
                      class="w-1.5 h-1.5 rounded-full"
                      :class="
                        item.user?.is_active ? 'bg-green-600' : 'bg-gray-500'
                      "
                    ></span>
                    {{ item.user?.is_active ? "Aktif" : "Non-Aktif" }}
                  </span>
                </td>
                <td class="px-6 py-4 text-right">
                  <div
                    class="flex items-center justify-end gap-2 opacity-0 group-hover:opacity-100 transition-opacity"
                  >
                    <button
                      @click="openEditModal(item)"
                      class="p-1.5 text-text-secondary hover:text-primary hover:bg-background-light rounded-md transition-colors"
                      title="Edit"
                    >
                      <span class="material-symbols-outlined text-[18px]"
                        >edit</span
                      >
                    </button>
                    <button
                      @click="confirmDelete(item)"
                      class="p-1.5 text-text-secondary hover:text-red-500 hover:bg-background-light rounded-md transition-colors"
                      title="Delete"
                    >
                      <span class="material-symbols-outlined text-[18px]"
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
            <span class="font-medium text-text-main">{{
              pagination.total
            }}</span>
            data
          </p>
          <div class="flex gap-2">
            <button
              @click="goToPage(pagination.current_page - 1)"
              :disabled="pagination.current_page <= 1"
              class="px-3 py-1.5 rounded-md border border-border-light text-text-secondary text-sm font-medium hover:bg-background-light disabled:opacity-50"
            >
              <span class="material-symbols-outlined text-sm"
                >chevron_left</span
              >
            </button>
            <button
              class="px-3 py-1.5 rounded-md bg-primary text-white text-sm font-medium"
            >
              {{ pagination.current_page }}
            </button>
            <button
              @click="goToPage(pagination.current_page + 1)"
              :disabled="pagination.current_page >= pagination.last_page"
              class="px-3 py-1.5 rounded-md border border-border-light text-text-secondary text-sm font-medium hover:bg-background-light disabled:opacity-50"
            >
              <span class="material-symbols-outlined text-sm"
                >chevron_right</span
              >
            </button>
          </div>
        </div>
      </template>
    </div>

    <!-- Add/Edit Modal -->
    <Transition name="modal-fade">
      <div
        v-if="showModal"
        class="fixed inset-0 bg-black/50 flex items-center justify-center z-50 p-4"
      >
        <div
          class="bg-white dark:bg-surface-light rounded-xl shadow-2xl w-full max-w-lg max-h-[90vh] overflow-y-auto"
        >
          <div class="p-6 border-b border-border-light">
            <h2 class="text-xl font-bold text-text-main">
              {{ isEditing ? "Edit Mahasiswa" : "Tambah Mahasiswa" }}
            </h2>
          </div>
          <form @submit.prevent="saveMahasiswa" class="p-6 space-y-4">
            <div class="grid grid-cols-2 gap-4">
              <div>
                <label class="block text-sm font-medium text-text-main mb-1"
                  >NIM</label
                >
                <input
                  v-model="form.nim"
                  type="text"
                  class="w-full px-3 py-2 border border-border-light rounded-lg focus:ring-2 focus:ring-primary/20 focus:border-primary"
                  required
                  :disabled="isEditing"
                />
              </div>
              <div>
                <label class="block text-sm font-medium text-text-main mb-1"
                  >Tahun</label
                >
                <select
                  v-model="form.tahun_id"
                  class="w-full px-3 py-2 border border-border-light rounded-lg focus:ring-2 focus:ring-primary/20 focus:border-primary bg-white dark:bg-surface-light"
                  required
                >
                  <option value="" disabled>Pilih Tahun</option>
                  <option v-for="t in tahunList" :key="t.id" :value="t.id">
                    {{ t.name }}
                  </option>
                </select>
              </div>
            </div>
            <div>
              <label class="block text-sm font-medium text-text-main mb-1"
                >Nama Lengkap</label
              >
              <input
                v-model="form.nama"
                type="text"
                class="w-full px-3 py-2 border border-border-light rounded-lg focus:ring-2 focus:ring-primary/20 focus:border-primary"
                required
              />
            </div>
            <div>
              <label class="block text-sm font-medium text-text-main mb-1"
                >Email</label
              >
              <input
                v-model="form.email"
                type="email"
                class="w-full px-3 py-2 border border-border-light rounded-lg focus:ring-2 focus:ring-primary/20 focus:border-primary"
                required
              />
            </div>
            <div>
              <label class="block text-sm font-medium text-text-main mb-1"
                >Program Studi</label
              >
              <select
                v-model="form.prodi_id"
                class="w-full px-3 py-2 border border-border-light rounded-lg focus:ring-2 focus:ring-primary/20 focus:border-primary bg-white dark:bg-surface-light"
                required
              >
                <option value="" disabled>Pilih Program Studi</option>
                <option v-for="p in prodiList" :key="p.id" :value="p.id">
                  {{ p.nama }}
                </option>
              </select>
            </div>
            <div v-if="!isEditing">
              <label class="block text-sm font-medium text-text-main mb-1"
                >Password</label
              >
              <input
                v-model="form.password"
                type="password"
                class="w-full px-3 py-2 border border-border-light rounded-lg focus:ring-2 focus:ring-primary/20 focus:border-primary"
                :required="!isEditing"
              />
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
    </Transition>

    <!-- Delete Confirmation Modal -->
    <Transition name="modal-fade">
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
                <h3 class="text-lg font-bold text-text-main">
                  Hapus Mahasiswa?
                </h3>
                <p class="text-sm text-text-secondary">
                  Tindakan ini tidak dapat dibatalkan.
                </p>
              </div>
            </div>
            <p class="text-text-main mb-6">
              Apakah Anda yakin ingin menghapus mahasiswa
              <strong>"{{ deleteItem?.nama }}"</strong>?
            </p>
            <div class="flex gap-3">
              <button
                @click="showDeleteModal = false"
                class="flex-1 px-4 py-2.5 border border-border-light rounded-lg text-text-secondary hover:bg-background-light transition-colors"
              >
                Batal
              </button>
              <button
                @click="deleteMahasiswa"
                :disabled="deleting"
                class="flex-1 px-4 py-2.5 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors disabled:opacity-50"
              >
                {{ deleting ? "Menghapus..." : "Hapus" }}
              </button>
            </div>
          </div>
        </div>
      </div>
    </Transition>

    <!-- Import Modal -->
    <Transition name="modal-fade">
      <div
        v-if="showImportModal"
        class="fixed inset-0 bg-black/50 flex items-center justify-center z-50 p-4"
      >
        <div
          class="bg-white dark:bg-surface-light rounded-xl shadow-2xl w-full max-w-lg"
        >
          <div class="p-6 border-b border-border-light">
            <h2 class="text-xl font-bold text-text-main">
              Import Data Mahasiswa
            </h2>
            <p class="text-sm text-text-secondary mt-1">
              Upload file CSV sesuai template yang disediakan.
            </p>
          </div>
          <div class="p-6 space-y-4">
            <!-- Template Download -->
            <div
              class="flex items-center gap-3 p-3 bg-blue-50 dark:bg-blue-900/20 rounded-lg border border-blue-200 dark:border-blue-800"
            >
              <span
                class="material-symbols-outlined text-blue-600 dark:text-blue-400"
                >description</span
              >
              <div class="flex-1">
                <p class="text-sm font-medium text-blue-800 dark:text-blue-300">
                  Download Template
                </p>
                <p class="text-xs text-blue-600 dark:text-blue-400">
                  Gunakan template ini untuk mengisi data mahasiswa
                </p>
              </div>
              <button
                @click="downloadTemplate"
                class="px-3 py-1.5 bg-blue-600 text-white rounded-lg text-xs font-bold hover:bg-blue-700 transition-colors"
              >
                Download
              </button>
            </div>

            <!-- File Input -->
            <div>
              <label class="block text-sm font-medium text-text-main mb-2"
                >File CSV</label
              >
              <div
                class="border-2 border-dashed border-border-light rounded-lg p-6 text-center hover:border-primary/50 transition-colors cursor-pointer"
                @click="$refs.importFile.click()"
                @dragover.prevent
                @drop.prevent="handleDrop"
              >
                <input
                  ref="importFile"
                  type="file"
                  accept=".csv"
                  class="hidden"
                  @change="handleFileSelect"
                />
                <span
                  class="material-symbols-outlined text-3xl text-text-secondary mb-2 block"
                  >cloud_upload</span
                >
                <p v-if="!importFile" class="text-sm text-text-secondary">
                  Klik atau drag & drop file CSV di sini
                </p>
                <p v-else class="text-sm text-primary font-medium">
                  {{ importFile.name }}
                </p>
              </div>
            </div>

            <!-- Import Result -->
            <div
              v-if="importResult"
              class="p-3 rounded-lg border"
              :class="
                importResult.success
                  ? 'bg-green-50 border-green-200 dark:bg-green-900/20 dark:border-green-800'
                  : 'bg-red-50 border-red-200 dark:bg-red-900/20 dark:border-red-800'
              "
            >
              <p
                class="text-sm font-medium"
                :class="
                  importResult.success
                    ? 'text-green-800 dark:text-green-300'
                    : 'text-red-800 dark:text-red-300'
                "
              >
                {{ importResult.message }}
              </p>
              <div
                v-if="importResult.data?.errors?.length"
                class="mt-2 space-y-1"
              >
                <p
                  v-for="(err, i) in importResult.data.errors"
                  :key="i"
                  class="text-xs text-red-600 dark:text-red-400"
                >
                  • {{ err }}
                </p>
              </div>
            </div>

            <div class="flex gap-3 pt-2">
              <button
                type="button"
                @click="closeImportModal"
                class="flex-1 px-4 py-2.5 border border-border-light rounded-lg text-text-secondary hover:bg-background-light transition-colors"
              >
                Tutup
              </button>
              <button
                @click="doImport"
                :disabled="!importFile || importing"
                class="flex-1 px-4 py-2.5 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors disabled:opacity-50"
              >
                {{ importing ? "Mengimport..." : "Import" }}
              </button>
            </div>
          </div>
        </div>
      </div>
    </Transition>
  </div>
</template>

<script setup>
import { ref, onMounted, reactive } from "vue";
import adminService from "../../../services/adminService";

const loading = ref(true);
const saving = ref(false);
const deleting = ref(false);
const mahasiswaList = ref([]);
const prodiList = ref([]);
const tahunList = ref([]);
const searchQuery = ref("");
const showModal = ref(false);
const showDeleteModal = ref(false);
const showImportModal = ref(false);
const isEditing = ref(false);
const deleteItem = ref(null);
const importFile = ref(null);
const importing = ref(false);
const importResult = ref(null);

const pagination = reactive({
  current_page: 1,
  last_page: 1,
  per_page: 15,
  total: 0,
  from: 0,
  to: 0,
});

const statsCount = reactive({
  active: 0,
});

const form = reactive({
  id: null,
  nim: "",
  nama: "",
  email: "",
  password: "",
  tahun_id: "",
  prodi_id: "",
});

let searchTimeout = null;

const fetchMahasiswa = async () => {
  try {
    loading.value = true;
    const params = {
      page: pagination.current_page,
      per_page: pagination.per_page,
      search: searchQuery.value,
    };
    const response = await adminService.getMahasiswa(params);
    if (response.success) {
      mahasiswaList.value = response.data.data;
      Object.assign(pagination, {
        current_page: response.data.current_page,
        last_page: response.data.last_page,
        per_page: response.data.per_page,
        total: response.data.total,
        from: response.data.from,
        to: response.data.to,
      });
      // Count active
      statsCount.active = mahasiswaList.value.filter(
        (m) => m.user?.is_active,
      ).length;
    }
  } catch (error) {
    console.error("Failed to fetch mahasiswa:", error);
  } finally {
    loading.value = false;
  }
};

const debouncedSearch = () => {
  clearTimeout(searchTimeout);
  searchTimeout = setTimeout(() => {
    pagination.current_page = 1;
    fetchMahasiswa();
  }, 300);
};

const goToPage = (page) => {
  if (page >= 1 && page <= pagination.last_page) {
    pagination.current_page = page;
    fetchMahasiswa();
  }
};

const openAddModal = () => {
  isEditing.value = false;
  form.id = null;
  form.nim = "";
  form.nama = "";
  form.email = "";
  form.password = "";
  form.tahun_id = "";
  form.prodi_id = "";
  showModal.value = true;
};

const openEditModal = (item) => {
  isEditing.value = true;
  form.id = item.id;
  form.nim = item.nim;
  form.nama = item.nama;
  form.email = item.user?.email || "";
  form.password = "";
  form.tahun_id = item.tahun_id || "";
  form.prodi_id = item.prodi_id || "";
  showModal.value = true;
};

const closeModal = () => {
  showModal.value = false;
};

const saveMahasiswa = async () => {
  try {
    saving.value = true;
    const data = {
      nim: form.nim,
      nama: form.nama,
      email: form.email,
      tahun_id: form.tahun_id,
      prodi_id: form.prodi_id,
    };
    if (!isEditing.value && form.password) {
      data.password = form.password;
    }
    if (isEditing.value) {
      await adminService.updateMahasiswa(form.id, data);
    } else {
      await adminService.createMahasiswa(data);
    }
    closeModal();
    fetchMahasiswa();
  } catch (error) {
    console.error("Failed to save mahasiswa:", error);
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

const deleteMahasiswa = async () => {
  try {
    deleting.value = true;
    await adminService.deleteMahasiswa(deleteItem.value.id);
    showDeleteModal.value = false;
    fetchMahasiswa();
  } catch (error) {
    console.error("Failed to delete mahasiswa:", error);
    alert(
      "Gagal menghapus data: " +
        (error.response?.data?.message || error.message),
    );
  } finally {
    deleting.value = false;
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
    "bg-blue-100 text-blue-600",
    "bg-purple-100 text-purple-600",
    "bg-orange-100 text-orange-600",
    "bg-green-100 text-green-600",
  ];
  if (!name) return colors[0];
  const index = name.charCodeAt(0) % colors.length;
  return colors[index];
};

// --- Import ---
const handleFileSelect = (e) => {
  importFile.value = e.target.files[0] || null;
  importResult.value = null;
};

const handleDrop = (e) => {
  const file = e.dataTransfer.files[0];
  if (file && file.name.endsWith(".csv")) {
    importFile.value = file;
    importResult.value = null;
  }
};

const downloadTemplate = async () => {
  try {
    const response = await adminService.downloadMahasiswaTemplate();
    const url = window.URL.createObjectURL(new Blob([response.data]));
    const a = document.createElement("a");
    a.href = url;
    a.download = "template_mahasiswa.csv";
    a.click();
    window.URL.revokeObjectURL(url);
  } catch (error) {
    console.error("Failed to download template:", error);
  }
};

const doImport = async () => {
  if (!importFile.value) return;
  importing.value = true;
  importResult.value = null;
  try {
    const result = await adminService.importMahasiswa(importFile.value);
    importResult.value = result;
    if (result.success) {
      fetchMahasiswa();
    }
  } catch (error) {
    importResult.value = {
      success: false,
      message:
        error.response?.data?.message ||
        "Import gagal. Pastikan format file benar.",
    };
  } finally {
    importing.value = false;
  }
};

const closeImportModal = () => {
  showImportModal.value = false;
  importFile.value = null;
  importResult.value = null;
};

const fetchProdi = async () => {
  try {
    const response = await adminService.getProdi({ active_only: true });
    if (response.success) {
      prodiList.value = response.data;
    }
  } catch (error) {
    console.error("Failed to fetch prodi:", error);
  }
};

const fetchTahun = async () => {
  try {
    const response = await adminService.getTahun({ active_only: 1 });
    if (response.success) {
      tahunList.value = response.data;
    }
  } catch (error) {
    console.error("Failed to fetch tahun:", error);
  }
};

onMounted(() => {
  fetchMahasiswa();
  fetchProdi();
  fetchTahun();
});
</script>
