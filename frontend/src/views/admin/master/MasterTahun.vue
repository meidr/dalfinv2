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
        <span class="text-text-main font-medium">Master Tahun Akademik</span>
      </div>
      <h1 class="text-text-main text-3xl font-bold leading-tight">
        Master Tahun Akademik
      </h1>
      <p class="text-text-secondary text-sm font-normal">
        Kelola data tahun akademik / angkatan
      </p>
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
            v-model="filters.search"
            type="text"
            placeholder="Cari tahun..."
            class="block w-full pl-10 pr-3 py-2.5 border border-border-light rounded-lg leading-5 bg-background-light text-text-main placeholder-text-secondary focus:outline-none focus:ring-1 focus:ring-primary focus:border-primary sm:text-sm transition-shadow dark:bg-background"
          />
        </div>
        <!-- Actions -->
        <div class="flex gap-3 w-full md:w-auto">
          <button
            @click="openModal()"
            class="flex items-center justify-center gap-2 px-4 py-2.5 bg-primary hover:bg-blue-600 text-white rounded-lg font-medium text-sm shadow-sm shadow-blue-500/20 transition-all w-full md:w-auto whitespace-nowrap"
          >
            <span class="material-symbols-outlined text-[20px]">add</span>
            Tambah Tahun
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
              <th class="px-6 py-4">Nama (Angkatan)</th>
              <th class="px-6 py-4">Kode</th>
              <th class="px-6 py-4">Semester</th>
              <th class="px-6 py-4">Status</th>
              <th class="px-6 py-4 text-right">Aksi</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-border-light">
            <tr v-if="items.length === 0">
              <td
                colspan="5"
                class="px-6 py-12 text-center text-text-secondary"
              >
                Tidak ada data ditemukan
              </td>
            </tr>
            <tr
              v-else
              v-for="item in items"
              :key="item.id"
              class="group hover:bg-sidebar-light/30 transition-colors"
            >
              <td class="px-6 py-4 text-text-main font-medium">
                {{ item.name }}
              </td>
              <td class="px-6 py-4 text-text-secondary">
                {{ item.kode || "-" }}
              </td>
              <td class="px-6 py-4 text-text-secondary">
                {{ item.semester || "-" }}
              </td>
              <td class="px-6 py-4">
                <span
                  class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-medium"
                  :class="
                    item.is_active
                      ? 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400'
                      : 'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400'
                  "
                >
                  <span
                    class="w-1.5 h-1.5 rounded-full"
                    :class="item.is_active ? 'bg-green-500' : 'bg-red-500'"
                  ></span>
                  {{ item.is_active ? "Aktif" : "Non-Aktif" }}
                </span>
              </td>
              <td class="px-6 py-4 text-right">
                <div
                  class="flex items-center justify-end gap-2 opacity-0 group-hover:opacity-100 transition-opacity"
                >
                  <button
                    @click="openModal(item)"
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
                    title="Hapus"
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
    </div>

    <!-- Modal Form -->
    <Transition name="modal-fade">
      <div
        v-if="showModal"
        class="fixed inset-0 bg-black/50 flex items-center justify-center z-50 p-4"
      >
        <div
          class="bg-white dark:bg-surface-light rounded-xl shadow-2xl w-full max-w-md"
        >
          <div class="p-6 border-b border-border-light">
            <h2 class="text-xl font-bold text-text-main">
              {{ isEditing ? "Edit Tahun" : "Tambah Tahun" }}
            </h2>
          </div>
          <form @submit.prevent="saveItem" class="p-6 space-y-4">
            <div>
              <label class="block text-sm font-medium text-text-main mb-1"
                >Nama Tahun <span class="text-red-500">*</span></label
              >
              <input
                v-model="form.name"
                type="text"
                required
                placeholder="Contoh: 2024"
                class="w-full px-3 py-2.5 border border-border-light rounded-lg focus:ring-2 focus:ring-primary/20 focus:border-primary bg-background-light text-text-main dark:bg-background"
              />
            </div>

            <div class="grid grid-cols-2 gap-4">
              <div>
                <label class="block text-sm font-medium text-text-main mb-1"
                  >Kode</label
                >
                <input
                  v-model="form.kode"
                  type="text"
                  placeholder="Contoh: 2024/2025"
                  class="w-full px-3 py-2.5 border border-border-light rounded-lg focus:ring-2 focus:ring-primary/20 focus:border-primary bg-background-light text-text-main dark:bg-background"
                />
              </div>
              <div>
                <label class="block text-sm font-medium text-text-main mb-1"
                  >Semester</label
                >
                <input
                  v-model="form.semester"
                  type="text"
                  placeholder="Contoh: Ganjil"
                  class="w-full px-3 py-2.5 border border-border-light rounded-lg focus:ring-2 focus:ring-primary/20 focus:border-primary bg-background-light text-text-main dark:bg-background"
                />
              </div>
            </div>

            <div class="flex items-center gap-2">
              <input
                v-model="form.is_active"
                type="checkbox"
                id="is_active"
                class="rounded border-border-light text-primary focus:ring-primary h-4 w-4"
              />
              <label
                for="is_active"
                class="text-sm font-medium text-text-secondary"
                >Aktif</label
              >
            </div>

            <div class="flex gap-3 pt-4">
              <button
                type="button"
                @click="closeModal()"
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
  </div>
</template>

<script setup>
import { ref, reactive, onMounted, watch } from "vue";
import { useToast } from "vue-toastification";
import Swal from "sweetalert2";
import adminService from "../../../services/adminService";

const toast = useToast();
const loading = ref(false);
const saving = ref(false);
const items = ref([]);
const showModal = ref(false);
const isEditing = ref(false);
const currentId = ref(null);

const filters = reactive({
  search: "",
});

const form = reactive({
  name: "",
  kode: "",
  semester: "",
  is_active: true,
});

const fetchData = async () => {
  loading.value = true;
  try {
    const response = await adminService.getTahun(filters);
    if (response.success) {
      items.value = response.data;
    }
  } catch (error) {
    toast.error(error.response?.data?.message || "Gagal memuat data");
  } finally {
    loading.value = false;
  }
};

const openModal = (item = null) => {
  if (item) {
    isEditing.value = true;
    currentId.value = item.id;
    form.name = item.name;
    form.kode = item.kode || "";
    form.semester = item.semester || "";
    form.is_active = item.is_active;
  } else {
    isEditing.value = false;
    currentId.value = null;
    form.name = "";
    form.kode = "";
    form.semester = "";
    form.is_active = true;
  }
  showModal.value = true;
};

const closeModal = () => {
  showModal.value = false;
};

const saveItem = async () => {
  saving.value = true;
  try {
    if (isEditing.value) {
      await adminService.updateTahun(currentId.value, form);
      toast.success("Tahun berhasil diperbarui");
    } else {
      await adminService.createTahun(form);
      toast.success("Tahun berhasil ditambahkan");
    }
    closeModal();
    fetchData();
  } catch (error) {
    toast.error(error.response?.data?.message || "Gagal menyimpan data");
  } finally {
    saving.value = false;
  }
};

const confirmDelete = async (item) => {
  const result = await Swal.fire({
    title: "Apakah Anda yakin?",
    text: `Akan menghapus tahun ${item.name}`,
    icon: "warning",
    showCancelButton: true,
    confirmButtonColor: "#d33",
    cancelButtonColor: "#3085d6",
    confirmButtonText: "Ya, Hapus!",
    cancelButtonText: "Batal",
  });

  if (result.isConfirmed) {
    try {
      await adminService.deleteTahun(item.id);
      toast.success("Tahun berhasil dihapus");
      fetchData();
    } catch (error) {
      toast.error(error.response?.data?.message || "Gagal menghapus data");
    }
  }
};

watch(
  () => filters.search,
  () => {
    fetchData();
  },
);

onMounted(() => {
  fetchData();
});
</script>
