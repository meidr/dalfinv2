<template>
  <div class="flex flex-col gap-6">
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
            class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-text-secondary text-[20px] group-focus-within:text-primary transition-colors"
            >search</span
          >
          <input
            v-model="searchQuery"
            @input="debouncedSearch"
            class="w-full pl-10 pr-4 py-2.5 bg-white border border-border-light rounded-lg text-sm focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all outline-none shadow-sm placeholder:text-gray-400 dark:bg-sidebar-light dark:border-gray-600 dark:text-gray-200"
            placeholder="Cari Nama, NIM..."
            type="text"
          />
        </div>
        <button
          @click="openAddModal"
          class="flex items-center justify-center gap-2 bg-primary text-white px-5 py-2.5 rounded-lg text-sm font-semibold shadow-md shadow-primary/20 hover:bg-blue-600 hover:shadow-lg hover:shadow-primary/30 active:scale-95 transition-all w-full sm:w-auto"
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
      class="flex flex-col rounded-xl border border-border-light bg-surface-light overflow-hidden shadow-sm dark:bg-surface-light"
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
          <table class="w-full text-left border-collapse">
            <thead
              class="bg-sidebar-light/50 border-b border-border-light dark:bg-sidebar-light/50"
            >
              <tr>
                <th class="p-4 pl-6 w-12">
                  <input
                    type="checkbox"
                    class="rounded border-border-light text-primary focus:ring-primary size-4"
                  />
                </th>
                <th
                  class="p-4 text-[10px] font-bold tracking-widest text-text-secondary uppercase"
                >
                  NIM
                </th>
                <th
                  class="p-4 text-[10px] font-bold tracking-widest text-text-secondary uppercase"
                >
                  Mahasiswa
                </th>
                <th
                  class="p-4 text-[10px] font-bold tracking-widest text-text-secondary uppercase"
                >
                  Angkatan
                </th>
                <th
                  class="p-4 text-[10px] font-bold tracking-widest text-text-secondary uppercase"
                >
                  Program Studi
                </th>
                <th
                  class="p-4 text-[10px] font-bold tracking-widest text-text-secondary uppercase"
                >
                  Status
                </th>
                <th
                  class="p-4 pr-6 text-[10px] font-bold tracking-widest text-text-secondary uppercase text-right"
                >
                  Aksi
                </th>
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
                <td class="p-4 pl-6">
                  <input
                    type="checkbox"
                    class="rounded border-border-light text-primary focus:ring-primary size-4"
                  />
                </td>
                <td class="p-4 text-sm font-medium text-text-main font-mono">
                  {{ item.nim }}
                </td>
                <td class="p-4">
                  <div class="flex items-center gap-3">
                    <div
                      class="size-9 rounded-full flex items-center justify-center font-bold text-xs shrink-0"
                      :class="getAvatarColor(item.nama)"
                    >
                      {{ getInitials(item.nama) }}
                    </div>
                    <span class="text-sm font-bold text-text-main">{{
                      item.nama
                    }}</span>
                  </div>
                </td>
                <td class="p-4 text-sm text-text-secondary">
                  {{ item.angkatan }}
                </td>
                <td class="p-4 text-sm text-text-secondary">
                  {{ item.prodi?.nama || "-" }}
                </td>
                <td class="p-4">
                  <span
                    class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-bold"
                    :class="
                      item.user?.is_active
                        ? 'bg-green-50 text-green-700 border border-green-100'
                        : 'bg-gray-100 text-gray-700 border border-gray-200'
                    "
                  >
                    <span
                      class="size-1.5 rounded-full"
                      :class="
                        item.user?.is_active ? 'bg-green-600' : 'bg-gray-500'
                      "
                    ></span>
                    {{ item.user?.is_active ? "Aktif" : "Non-Aktif" }}
                  </span>
                </td>
                <td class="p-4 pr-6 text-right">
                  <div
                    class="flex items-center justify-end gap-2 opacity-0 group-hover:opacity-100 transition-opacity"
                  >
                    <button
                      @click="openEditModal(item)"
                      class="p-1.5 text-text-secondary hover:text-primary hover:bg-blue-50 dark:hover:bg-blue-900/20 rounded-md transition-colors"
                      title="Edit"
                    >
                      <span class="material-symbols-outlined text-[20px]"
                        >edit</span
                      >
                    </button>
                    <button
                      @click="confirmDelete(item)"
                      class="p-1.5 text-text-secondary hover:text-red-500 hover:bg-red-50 dark:hover:bg-red-900/20 rounded-md transition-colors"
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
          class="p-4 border-t border-border-light flex flex-col md:flex-row items-center justify-between gap-4 bg-gray-50/50 dark:bg-sidebar-light/50"
        >
          <p class="text-xs text-text-secondary font-medium">
            Menampilkan {{ pagination.from || 0 }}-{{ pagination.to || 0 }} dari
            {{ pagination.total }} data
          </p>
          <div class="flex gap-2">
            <button
              @click="goToPage(pagination.current_page - 1)"
              :disabled="pagination.current_page <= 1"
              class="p-1.5 rounded-lg border border-border-light bg-white hover:bg-gray-50 hover:shadow-sm disabled:opacity-50 text-text-secondary transition-all"
            >
              <span class="material-symbols-outlined text-[18px]"
                >chevron_left</span
              >
            </button>
            <button
              class="size-8 flex items-center justify-center rounded-lg border border-primary bg-primary text-white text-xs font-bold shadow-sm shadow-primary/20"
            >
              {{ pagination.current_page }}
            </button>
            <button
              @click="goToPage(pagination.current_page + 1)"
              :disabled="pagination.current_page >= pagination.last_page"
              class="p-1.5 rounded-lg border border-border-light bg-white hover:bg-gray-50 hover:shadow-sm disabled:opacity-50 text-text-secondary transition-all"
            >
              <span class="material-symbols-outlined text-[18px]"
                >chevron_right</span
              >
            </button>
          </div>
        </div>
      </template>
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
                >Angkatan</label
              >
              <input
                v-model="form.angkatan"
                type="text"
                class="w-full px-3 py-2 border border-border-light rounded-lg focus:ring-2 focus:ring-primary/20 focus:border-primary"
                required
              />
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
              <h3 class="text-lg font-bold text-text-main">Hapus Mahasiswa?</h3>
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
  </div>
</template>

<script setup>
import { ref, onMounted, reactive } from "vue";
import adminService from "../../../services/adminService";

const loading = ref(true);
const saving = ref(false);
const deleting = ref(false);
const mahasiswaList = ref([]);
const searchQuery = ref("");
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
  active: 0,
});

const form = reactive({
  id: null,
  nim: "",
  nama: "",
  email: "",
  password: "",
  angkatan: new Date().getFullYear().toString(),
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
  form.angkatan = new Date().getFullYear().toString();
  showModal.value = true;
};

const openEditModal = (item) => {
  isEditing.value = true;
  form.id = item.id;
  form.nim = item.nim;
  form.nama = item.nama;
  form.email = item.user?.email || "";
  form.password = "";
  form.angkatan = item.angkatan;
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
      angkatan: form.angkatan,
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

onMounted(() => {
  fetchMahasiswa();
});
</script>
