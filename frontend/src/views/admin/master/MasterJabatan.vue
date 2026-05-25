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
        <span class="text-text-main font-medium">Master Jabatan</span>
      </div>
      <h1 class="text-text-main text-3xl font-bold leading-tight">
        Master Jabatan
      </h1>
      <p class="text-text-secondary text-sm font-normal">
        Kelola daftar jabatan struktural (REKTOR, DEKAN, KAPRODI, dll)
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
            placeholder="Cari jabatan..."
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
            Tambah Jabatan
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
      <DataTableScroll v-else>
        <table class="w-full text-left text-sm whitespace-nowrap">
          <thead
            class="bg-sidebar-light/50 text-text-secondary font-medium border-b border-border-light"
          >
            <tr>
              <th class="px-6 py-4">Kode</th>
              <th class="px-6 py-4">Nama Jabatan</th>
              <th class="px-6 py-4">Level</th>
              <th class="px-6 py-4 text-right">Aksi</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-border-light">
            <tr v-if="items.length === 0">
              <td
                colspan="4"
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
              <td class="px-6 py-4">
                <span
                  class="inline-flex items-center px-2.5 py-1 rounded-md text-xs font-mono font-bold bg-blue-50 text-blue-700 dark:bg-blue-900/20 dark:text-blue-400"
                >
                  {{ item.kode }}
                </span>
              </td>
              <td class="px-6 py-4 text-text-main font-medium">
                {{ item.nama }}
              </td>
              <td class="px-6 py-4">
                <span
                  :class="getLevelClass(item.level)"
                  class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium capitalize"
                >
                  {{ item.level }}
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
      </DataTableScroll>
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
              {{ isEditing ? "Edit Jabatan" : "Tambah Jabatan" }}
            </h2>
          </div>
          <form @submit.prevent="saveItem" class="p-6 space-y-4">
            <div>
              <label class="block text-sm font-medium text-text-main mb-1"
                >Kode <span class="text-red-500">*</span></label
              >
              <input
                v-model="form.kode"
                type="text"
                required
                maxlength="30"
                placeholder="Contoh: KAPRODI"
                class="w-full px-3 py-2.5 border border-border-light rounded-lg focus:ring-2 focus:ring-primary/20 focus:border-primary bg-background-light text-text-main dark:bg-background uppercase"
              />
            </div>
            <div>
              <label class="block text-sm font-medium text-text-main mb-1"
                >Nama Jabatan <span class="text-red-500">*</span></label
              >
              <input
                v-model="form.nama"
                type="text"
                required
                maxlength="100"
                placeholder="Contoh: Ketua Program Studi"
                class="w-full px-3 py-2.5 border border-border-light rounded-lg focus:ring-2 focus:ring-primary/20 focus:border-primary bg-background-light text-text-main dark:bg-background"
              />
            </div>
            <div>
              <label class="block text-sm font-medium text-text-main mb-1"
                >Level <span class="text-red-500">*</span></label
              >
              <select
                v-model="form.level"
                required
                class="w-full px-3 py-2.5 border border-border-light rounded-lg focus:ring-2 focus:ring-primary/20 focus:border-primary bg-background-light text-text-main dark:bg-background"
              >
                <option value="">Pilih Level</option>
                <option value="kampus">Kampus</option>
                <option value="fakultas">Fakultas</option>
                <option value="prodi">Prodi</option>
              </select>
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
  kode: "",
  nama: "",
  level: "",
});

const getLevelClass = (level) => {
  const classes = {
    kampus:
      "bg-purple-50 text-purple-700 dark:bg-purple-900/20 dark:text-purple-400",
    fakultas:
      "bg-orange-50 text-orange-700 dark:bg-orange-900/20 dark:text-orange-400",
    prodi:
      "bg-green-50 text-green-700 dark:bg-green-900/20 dark:text-green-400",
  };
  return classes[level] || "bg-gray-50 text-gray-700";
};

const fetchData = async () => {
  loading.value = true;
  try {
    const response = await adminService.getJabatan(filters);
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
    form.kode = item.kode;
    form.nama = item.nama;
    form.level = item.level;
  } else {
    isEditing.value = false;
    currentId.value = null;
    form.kode = "";
    form.nama = "";
    form.level = "";
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
      await adminService.updateJabatan(currentId.value, form);
      toast.success("Jabatan berhasil diperbarui");
    } else {
      await adminService.createJabatan(form);
      toast.success("Jabatan berhasil ditambahkan");
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
    text: `Akan menghapus jabatan ${item.nama}`,
    icon: "warning",
    showCancelButton: true,
    confirmButtonColor: "#d33",
    cancelButtonColor: "#3085d6",
    confirmButtonText: "Ya, Hapus!",
    cancelButtonText: "Batal",
  });

  if (result.isConfirmed) {
    try {
      await adminService.deleteJabatan(item.id);
      toast.success("Jabatan berhasil dihapus");
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
