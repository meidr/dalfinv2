<template>
  <div class="max-w-7xl mx-auto flex flex-col gap-8 animate-fade-in-up">
    <div class="flex flex-col md:flex-row md:items-end justify-between gap-4">
      <div class="flex flex-col gap-1">
        <h1 class="text-3xl font-bold tracking-tight text-text-main">
          Master Data Dosen
        </h1>
        <p class="text-text-secondary text-sm font-normal">
          Kelola data dosen pembimbing dan kuota bimbingan skripsi.
        </p>
      </div>
    </div>

    <!-- Filters & Actions -->
    <div
      class="bg-surface-light dark:bg-surface-light p-4 rounded-xl border border-border-light shadow-sm flex flex-col md:flex-row gap-4 justify-between items-center"
    >
      <div class="relative w-full md:w-96 group">
        <div
          class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none"
        >
          <span
            class="material-symbols-outlined text-text-secondary group-focus-within:text-primary transition-colors"
            >search</span
          >
        </div>
        <input
          v-model="searchQuery"
          @input="debouncedSearch"
          class="block w-full pl-10 pr-3 py-2 border border-border-light rounded-lg bg-background-light dark:bg-sidebar-light dark:border-gray-600 text-text-main placeholder-text-secondary focus:outline-none focus:ring-2 focus:ring-primary/50 transition-all text-sm outline-none"
          placeholder="Cari NIDN, Nama, atau Jabatan..."
          type="text"
        />
      </div>
      <div class="flex items-center gap-3 w-full md:w-auto">
        <select
          v-model="filterStatus"
          @change="fetchDosen"
          class="appearance-none bg-background-light dark:bg-sidebar-light border border-border-light dark:border-gray-600 text-text-main text-sm rounded-lg focus:ring-primary focus:border-primary block w-full md:w-auto pl-4 pr-10 py-2 cursor-pointer hover:border-gray-300 transition-colors outline-none"
        >
          <option value="">Semua Status</option>
          <option value="aktif">Aktif</option>
          <option value="cuti">Cuti</option>
        </select>
        <button
          @click="showImportModal = true"
          class="flex items-center justify-center gap-2 bg-green-600 text-white px-4 py-2 rounded-lg text-sm font-bold shadow-sm shadow-green-600/20 hover:bg-green-700 transition-all w-full md:w-auto"
        >
          <span class="material-symbols-outlined text-[20px]">upload_file</span>
          <span>Import</span>
        </button>
        <button
          @click="openAddModal"
          class="flex items-center justify-center gap-2 bg-primary hover:bg-primary/90 text-white font-bold py-2 px-4 rounded-lg transition-all shadow-sm shadow-primary/20 hover:shadow-lg active:scale-95 whitespace-nowrap w-full md:w-auto text-sm"
        >
          <span class="material-symbols-outlined text-[20px]">add</span>
          <span>Tambah Dosen</span>
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

    <!-- Data Table -->
    <div
      v-else
      class="bg-surface-light dark:bg-surface-light rounded-xl border border-border-light shadow-sm overflow-hidden"
    >
      <div class="overflow-x-auto">
        <table class="w-full text-left text-sm whitespace-nowrap">
          <thead
            class="bg-sidebar-light/50 text-text-secondary font-medium border-b border-border-light"
          >
            <tr>
              <th class="px-6 py-4">NIDN/NIP</th>
              <th class="px-6 py-4">Nama Dosen</th>
              <th class="px-6 py-4">Jabatan</th>
              <th class="px-6 py-4">Kuota Bimbingan</th>
              <th class="px-6 py-4">Status</th>
              <th class="px-6 py-4 text-right">Aksi</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-border-light">
            <tr v-if="dosenList.length === 0">
              <td colspan="6" class="p-12 text-center text-text-secondary">
                Tidak ada data dosen
              </td>
            </tr>
            <tr
              v-for="item in dosenList"
              :key="item.id"
              class="group hover:bg-sidebar-light/30 transition-colors"
            >
              <td class="px-6 py-4 font-mono text-text-secondary">
                {{ item.nidn || item.nip || "-" }}
              </td>
              <td class="px-6 py-4">
                <div class="flex items-center gap-3">
                  <div
                    class="size-9 rounded-full flex items-center justify-center font-bold text-xs border shrink-0"
                    :class="getAvatarColor(getNamaLengkap(item))"
                  >
                    {{ getInitials(getNamaLengkap(item)) }}
                  </div>
                  <div class="font-bold text-text-main">
                    {{ getNamaLengkap(item) }}
                  </div>
                </div>
              </td>
              <td class="px-6 py-4 text-text-secondary">
                {{ item.jabatan_fungsional || "-" }}
              </td>
              <td class="px-6 py-4">
                <div class="flex flex-col gap-1.5 w-full max-w-[160px]">
                  <div class="flex justify-between text-xs font-medium">
                    <span class="text-text-main"
                      >{{ item.jumlah_bimbingan || 0 }} Mahasiswa</span
                    >
                    <span class="text-text-secondary"
                      >{{ item.jumlah_bimbingan || 0 }}/{{
                        item.kuota_bimbingan || 10
                      }}</span
                    >
                  </div>
                  <div
                    class="w-full bg-slate-100 dark:bg-gray-700 rounded-full h-1.5 overflow-hidden"
                  >
                    <div
                      class="h-1.5 rounded-full transition-all"
                      :class="
                        getQuotaColor(
                          item.jumlah_bimbingan,
                          item.kuota_bimbingan,
                        )
                      "
                      :style="{
                        width:
                          getQuotaPercent(
                            item.jumlah_bimbingan,
                            item.kuota_bimbingan,
                          ) + '%',
                      }"
                    ></div>
                  </div>
                </div>
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
                  {{ item.user?.is_active ? "Aktif" : "Cuti" }}
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
          <span class="font-medium text-text-main">{{ pagination.total }}</span>
          data
        </p>
        <div class="flex gap-2">
          <button
            @click="goToPage(pagination.current_page - 1)"
            :disabled="pagination.current_page <= 1"
            class="px-3 py-1.5 rounded-md border border-border-light text-text-secondary text-sm font-medium hover:bg-background-light disabled:opacity-50"
          >
            <span class="material-symbols-outlined text-sm">chevron_left</span>
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
            <span class="material-symbols-outlined text-sm">chevron_right</span>
          </button>
        </div>
      </div>
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
              {{ isEditing ? "Edit Dosen" : "Tambah Dosen" }}
            </h2>
          </div>
          <form @submit.prevent="saveDosen" class="p-6 space-y-4">
            <div class="grid grid-cols-2 gap-4">
              <div>
                <label class="block text-sm font-medium text-text-main mb-1"
                  >NIDN</label
                >
                <input
                  v-model="form.nidn"
                  type="text"
                  class="w-full px-3 py-2 border border-border-light rounded-lg focus:ring-2 focus:ring-primary/20 focus:border-primary"
                  :disabled="isEditing"
                />
              </div>
              <div>
                <label class="block text-sm font-medium text-text-main mb-1"
                  >NIP</label
                >
                <input
                  v-model="form.nip"
                  type="text"
                  class="w-full px-3 py-2 border border-border-light rounded-lg focus:ring-2 focus:ring-primary/20 focus:border-primary"
                />
              </div>
            </div>
            <div>
              <label class="block text-sm font-medium text-text-main mb-1"
                >Nama Lengkap</label
              >
              <input
                v-model="form.nama_lengkap"
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
            <div class="grid grid-cols-2 gap-4">
              <div>
                <label class="block text-sm font-medium text-text-main mb-1"
                  >Jabatan Fungsional</label
                >
                <input
                  v-model="form.jabatan_fungsional"
                  type="text"
                  placeholder="Contoh: Lektor Kepala"
                  class="w-full px-3 py-2 border border-border-light rounded-lg focus:ring-2 focus:ring-primary/20 focus:border-primary bg-white dark:bg-surface-light"
                />
              </div>
              <div>
                <label class="block text-sm font-medium text-text-main mb-1"
                  >Kuota Bimbingan</label
                >
                <input
                  v-model.number="form.kuota_bimbingan"
                  type="number"
                  min="1"
                  max="20"
                  class="w-full px-3 py-2 border border-border-light rounded-lg focus:ring-2 focus:ring-primary/20 focus:border-primary"
                />
              </div>
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
                <h3 class="text-lg font-bold text-text-main">Hapus Dosen?</h3>
                <p class="text-sm text-text-secondary">
                  Tindakan ini tidak dapat dibatalkan.
                </p>
              </div>
            </div>
            <p class="text-text-main mb-6">
              Apakah Anda yakin ingin menghapus dosen
              <strong>"{{ getNamaLengkap(deleteItem) }}"</strong>?
            </p>
            <div class="flex gap-3">
              <button
                @click="showDeleteModal = false"
                class="flex-1 px-4 py-2.5 border border-border-light rounded-lg text-text-secondary hover:bg-background-light transition-colors"
              >
                Batal
              </button>
              <button
                @click="deleteDosen"
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
            <h2 class="text-xl font-bold text-text-main">Import Data Dosen</h2>
            <p class="text-sm text-text-secondary mt-1">
              Upload file CSV sesuai template yang disediakan.
            </p>
          </div>
          <div class="p-6 space-y-4">
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
                  Gunakan template ini untuk mengisi data dosen
                </p>
              </div>
              <button
                @click="downloadTemplate"
                class="px-3 py-1.5 bg-blue-600 text-white rounded-lg text-xs font-bold hover:bg-blue-700 transition-colors"
              >
                Download
              </button>
            </div>

            <div>
              <label class="block text-sm font-medium text-text-main mb-2"
                >File CSV</label
              >
              <div
                class="border-2 border-dashed border-border-light rounded-lg p-6 text-center hover:border-primary/50 transition-colors cursor-pointer"
                @click="$refs.importFileInput.click()"
                @dragover.prevent
                @drop.prevent="handleDrop"
              >
                <input
                  ref="importFileInput"
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
const dosenList = ref([]);

const searchQuery = ref("");
const filterStatus = ref("");
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

const form = reactive({
  id: null,
  nidn: "",
  nip: "",
  nama_lengkap: "",
  email: "",
  password: "",
  jabatan_fungsional: "",
  kuota_bimbingan: 10,
});

let searchTimeout = null;

const fetchDosen = async () => {
  try {
    loading.value = true;
    const params = {
      page: pagination.current_page,
      per_page: pagination.per_page,
      search: searchQuery.value,
      status: filterStatus.value,
    };
    const response = await adminService.getDosen(params);
    if (response.success) {
      dosenList.value = response.data.data;
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
    console.error("Failed to fetch dosen:", error);
  } finally {
    loading.value = false;
  }
};

const debouncedSearch = () => {
  clearTimeout(searchTimeout);
  searchTimeout = setTimeout(() => {
    pagination.current_page = 1;
    fetchDosen();
  }, 300);
};

const goToPage = (page) => {
  if (page >= 1 && page <= pagination.last_page) {
    pagination.current_page = page;
    fetchDosen();
  }
};

const openAddModal = () => {
  isEditing.value = false;
  form.id = null;
  form.nidn = "";
  form.nip = "";
  form.nama_lengkap = "";
  form.email = "";
  form.password = "";
  form.jabatan_fungsional = "";
  form.kuota_bimbingan = 10;
  showModal.value = true;
};

const openEditModal = (item) => {
  isEditing.value = true;
  form.id = item.id;
  form.nidn = item.nidn || "";
  form.nip = item.nip || "";
  form.nama_lengkap = item.nama || "";
  form.email = item.user?.email || item.email || "";
  form.password = "";
  form.jabatan_fungsional = item.jabatan_fungsional || "";
  form.kuota_bimbingan = item.kuota_bimbingan || 10;
  showModal.value = true;
};

const closeModal = () => {
  showModal.value = false;
};

const saveDosen = async () => {
  try {
    saving.value = true;
    const data = {
      nidn: form.nidn,
      nip: form.nip,
      nama: form.nama_lengkap,
      email: form.email,
      jabatan_fungsional: form.jabatan_fungsional,
      kuota_bimbingan: form.kuota_bimbingan,
    };
    if (!isEditing.value && form.password) {
      data.password = form.password;
    }
    if (isEditing.value) {
      await adminService.updateDosen(form.id, data);
    } else {
      await adminService.createDosen(data);
    }
    closeModal();
    fetchDosen();
  } catch (error) {
    console.error("Failed to save dosen:", error);
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

const deleteDosen = async () => {
  try {
    deleting.value = true;
    await adminService.deleteDosen(deleteItem.value.id);
    showDeleteModal.value = false;
    fetchDosen();
  } catch (error) {
    console.error("Failed to delete dosen:", error);
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

const getNamaLengkap = (item) => {
  if (!item) return "";
  const parts = [];
  if (item.gelar_depan) parts.push(item.gelar_depan);
  if (item.nama) parts.push(item.nama);
  if (item.gelar_belakang) parts.push(item.gelar_belakang);
  return parts.join(" ") || item.nama || "";
};

const getAvatarColor = (name) => {
  const colors = [
    "bg-slate-100 text-primary border-slate-200",
    "bg-indigo-50 text-indigo-600 border-indigo-100",
    "bg-orange-50 text-orange-600 border-orange-100",
    "bg-green-50 text-green-600 border-green-100",
  ];
  if (!name) return colors[0];
  const index = name.charCodeAt(0) % colors.length;
  return colors[index];
};

const getQuotaPercent = (current, max) => {
  if (!max) return 0;
  return Math.min((current / max) * 100, 100);
};

const getQuotaColor = (current, max) => {
  const percent = getQuotaPercent(current, max);
  if (percent >= 100) return "bg-red-500";
  if (percent >= 80) return "bg-yellow-500";
  return "bg-primary";
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
    const response = await adminService.downloadDosenTemplate();
    const url = window.URL.createObjectURL(new Blob([response.data]));
    const a = document.createElement("a");
    a.href = url;
    a.download = "template_dosen.csv";
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
    const result = await adminService.importDosen(importFile.value);
    importResult.value = result;
    if (result.success) {
      fetchDosen();
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

onMounted(() => {
  fetchDosen();
});
</script>
