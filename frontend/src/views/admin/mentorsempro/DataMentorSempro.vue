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
        <span class="text-text-main font-medium">Mentor Seminar Proposal</span>
      </div>
      <h1 class="text-text-main text-3xl font-bold leading-tight">
        Mentor Seminar Proposal
      </h1>
      <p class="text-text-secondary text-sm font-normal">
        Kelola penunjukan mentor untuk mahasiswa yang akan seminar proposal.
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
            @input="debounceFetch"
            class="block w-full pl-10 pr-3 py-2.5 border border-border-light rounded-lg leading-5 bg-background-light text-text-main placeholder-text-secondary focus:outline-none focus:ring-1 focus:ring-primary focus:border-primary sm:text-sm transition-shadow dark:bg-background"
            placeholder="Cari Mahasiswa, NIM, atau Judul..."
            type="text"
          />
        </div>
        <div class="flex gap-3 w-full md:w-auto">
          <select
            v-model="filters.prodi"
            @change="fetchData(1)"
            class="px-4 py-2.5 bg-surface-light dark:bg-background border border-border-light rounded-lg text-text-secondary text-sm focus:ring-1 focus:ring-primary"
          >
            <option value="">Semua Prodi</option>
            <option v-for="p in prodiOptions" :key="p.id" :value="p.id">
              {{ p.nama }}
            </option>
          </select>
          <select
            v-model="filters.status"
            @change="fetchData(1)"
            class="px-4 py-2.5 bg-surface-light dark:bg-background border border-border-light rounded-lg text-text-secondary text-sm focus:ring-1 focus:ring-primary"
          >
            <option value="">Semua Status</option>
            <option value="belum">Belum Ditentukan</option>
            <option value="sudah">Sudah Ditentukan</option>
          </select>
        </div>
      </div>

      <!-- Loading -->
      <div v-if="loading" class="p-12 text-center">
        <div
          class="animate-spin rounded-full h-8 w-8 border-b-2 border-primary mx-auto"
        ></div>
        <p class="text-text-secondary text-sm mt-3">Memuat data...</p>
      </div>

      <!-- Table content -->
      <div v-else class="overflow-x-auto">
        <table class="w-full text-left text-sm whitespace-nowrap">
          <thead class="bg-sidebar-light/50 text-text-secondary font-medium border-b border-border-light">
            <tr>
              <th scope="col" class="px-6 py-4">
                Mahasiswa & Judul
              </th>
              <th scope="col" class="px-6 py-4">
                Status Skripsi
              </th>
              <th scope="col" class="px-6 py-4">
                Mentor
              </th>
              <th scope="col" class="px-6 py-4 text-right">
                Aksi
              </th>
            </tr>
          </thead>
          <tbody class="divide-y divide-border-light bg-surface-light">
            <tr v-if="items.length === 0">
              <td colspan="4" class="px-6 py-12 text-center text-text-secondary">
                Tidak ada data ditemukan
              </td>
            </tr>
            <tr v-for="item in items" :key="item.id" v-else class="group hover:bg-sidebar-light/30 transition-colors">
              <td class="px-6 py-4">
                <div class="flex items-center gap-3">
                  <div
                    class="size-10 rounded-full flex items-center justify-center text-xs font-bold shrink-0 bg-primary/20 text-primary"
                  >
                    {{ getInitials(item.mahasiswa?.nama) }}
                  </div>
                  <div class="max-w-md">
                    <p class="font-bold text-text-main text-sm">
                      {{ item.mahasiswa?.nama || "-" }}
                    </p>
                    <p class="text-xs text-text-secondary font-medium">
                      {{ item.mahasiswa?.nim || "-" }} · {{ item.mahasiswa?.prodi?.nama || "-" }}
                    </p>
                    <p class="mt-1 text-xs text-text-main truncate" :title="item.judul">
                      {{ item.judul || "-" }}
                    </p>
                  </div>
                </div>
              </td>
              <td class="px-6 py-4">
                <span
                  class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-medium"
                  :class="getStatusClass(item.status)"
                >
                  {{ getStatusLabel(item.status) }}
                </span>
              </td>
              <td class="px-6 py-4">
                <div v-if="item.mentor_sempro && item.mentor_sempro.length > 0" class="flex flex-col gap-1.5">
                  <div v-for="mentor in item.mentor_sempro" :key="mentor.id" class="flex items-center gap-2">
                    <span 
                      class="inline-flex items-center rounded text-[10px] font-bold px-1.5 py-0.5"
                      :class="mentor.jenis === 'mentor_1' ? 'bg-blue-100 text-blue-700' : 'bg-indigo-100 text-indigo-700'"
                    >
                      {{ mentor.jenis === 'mentor_1' ? 'Utama' : 'Pendamping' }}
                    </span>
                    <span class="text-xs text-text-main font-medium">{{ mentor.dosen?.nama }}</span>
                  </div>
                </div>
                <div v-else>
                  <span class="inline-flex items-center rounded-full bg-yellow-50 text-yellow-700 px-2 py-0.5 text-xs font-medium border border-yellow-200">
                    Belum Ditentukan
                  </span>
                </div>
              </td>
              <td class="px-6 py-4 text-right flex items-center justify-end gap-2">
                <button
                  v-if="item.mentor_sempro?.length"
                  type="button"
                  @click="downloadPdf(item.id)"
                  class="inline-flex items-center justify-center gap-1.5 px-3 py-1.5 bg-gray-100 hover:bg-gray-200 text-text-secondary rounded-lg font-medium text-xs transition-colors"
                  title="Cetak Data Mentor"
                >
                  <span class="material-symbols-outlined text-[16px]">print</span>
                  Cetak
                </button>
                <button
                  type="button"
                  @click="openAssignModal(item)"
                  class="inline-flex items-center justify-center gap-1.5 px-3 py-1.5 bg-primary/10 hover:bg-primary/20 text-primary rounded-lg font-medium text-xs transition-colors"
                >
                  <span class="material-symbols-outlined text-[16px]">{{ item.mentor_sempro?.length ? 'edit' : 'add' }}</span>
                  {{ item.mentor_sempro?.length ? 'Ubah Mentor' : 'Tentukan Mentor' }}
                </button>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
      
      <!-- Pagination -->
      <TablePagination 
        :pagination="pagination"
        :disabled="loading"
        @page-change="handlePageChange"
        @per-page-change="changePerPage"
      />
    </div>

    <!-- Assign Modal -->
    <Teleport to="body">

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
              {{ form.id ? 'Ubah Mentor' : 'Tentukan Mentor Sempro' }}
            </h2>
          </div>
          <form @submit.prevent="submitAssignment" class="p-6 space-y-6">
            <div class="bg-sidebar-light p-4 rounded-lg">
              <h4 class="text-sm font-medium text-text-secondary mb-2">Mahasiswa</h4>
              <p class="font-bold text-text-main text-base">{{ selectedItem?.mahasiswa?.nama }}</p>
              <p class="text-sm text-text-secondary">{{ selectedItem?.mahasiswa?.nim }}</p>
              <p class="text-sm text-text-main mt-2 font-medium">{{ selectedItem?.judul }}</p>
            </div>

            <div class="space-y-4">
              <!-- Mentor 1 -->
              <div>
                <label class="block text-sm font-medium text-text-main mb-1">
                  Mentor Utama <span class="text-red-500">*</span>
                </label>
                <select
                  v-model="form.mentor_1_id"
                  required
                  class="w-full px-3 py-2.5 border border-border-light rounded-lg bg-background-light text-text-main focus:ring-2 focus:ring-primary/20 focus:border-primary text-sm"
                >
                  <option value="">Pilih Mentor Utama</option>
                  <optgroup v-for="(group, key) in groupedDosen" :key="key" :label="key">
                    <option 
                      v-for="dosen in group" 
                      :key="dosen.id" 
                      :value="dosen.id"
                      :disabled="!dosen.is_available && form.original_mentor_1 !== dosen.id"
                    >
                      {{ dosen.nama }} ({{ dosen.current_mentor }}/{{ dosen.kuota_mentor }})
                    </option>
                  </optgroup>
                </select>
              </div>

              <!-- Mentor 2 -->
              <div>
                <label class="block text-sm font-medium text-text-main mb-1">
                  Mentor Pendamping <span class="text-text-secondary font-normal">(Opsional)</span>
                </label>
                <select
                  v-model="form.mentor_2_id"
                  class="w-full px-3 py-2.5 border border-border-light rounded-lg bg-background-light text-text-main focus:ring-2 focus:ring-primary/20 focus:border-primary text-sm"
                >
                  <option value="">Tidak ada pendamping</option>
                  <optgroup v-for="(group, key) in groupedDosen" :key="key" :label="key">
                    <option 
                      v-for="dosen in group" 
                      :key="dosen.id" 
                      :value="dosen.id"
                      :disabled="(!dosen.is_available && form.original_mentor_2 !== dosen.id) || form.mentor_1_id === dosen.id"
                    >
                      {{ dosen.nama }} ({{ dosen.current_mentor }}/{{ dosen.kuota_mentor }})
                    </option>
                  </optgroup>
                </select>
              </div>
            </div>

            <div class="flex gap-3 pt-4 border-t border-border-light">
              <button
                type="button"
                @click="closeModal"
                class="flex-1 px-4 py-2.5 border border-border-light rounded-lg text-text-secondary hover:bg-gray-50 transition-colors"
              >
                Batal
              </button>
              <button
                type="submit"
                :disabled="loadingSubmit || !form.mentor_1_id"
                class="flex-1 px-4 py-2.5 bg-primary text-white rounded-lg hover:bg-blue-600 transition-colors disabled:opacity-50"
              >
                {{ loadingSubmit ? 'Menyimpan...' : 'Simpan' }}
              </button>
            </div>
          </form>
        </div>
      </div>
    </Transition>
    </Teleport>
  </div>
</template>

<script setup>
import { ref, onMounted, computed } from 'vue';
import adminService from '../../../services/adminService';
import TablePagination from '../../../components/ui/TablePagination.vue';
import { useToast } from 'vue-toastification';

const toast = useToast();

const loading = ref(false);
const loadingSubmit = ref(false);
const items = ref([]);
const pagination = ref({
  current_page: 1,
  last_page: 1,
  per_page: 15,
  total: 0
});

const filters = ref({
  search: '',
  status: '',
  prodi: ''
});

const prodiOptions = ref([]);
const availableDosen = ref([]);
const groupedDosen = computed(() => {
  return availableDosen.value.reduce((groups, dosen) => {
    const groupName = dosen.prodi?.nama || 'Tanpa Prodi';
    
    if (!groups[groupName]) {
      groups[groupName] = [];
    }
    groups[groupName].push(dosen);
    return groups;
  }, {});
});

const showModal = ref(false);
const selectedItem = ref(null);
const form = ref({
  skripsi_id: '',
  mentor_1_id: '',
  mentor_2_id: '',
  original_mentor_1: null,
  original_mentor_2: null
});

// Debounce timer
let timeout;

const fetchProdi = async () => {
  // Assume adminService has getProdi if not just ignore or hardcode if needed
  try {
    // If not available, we can mock or comment out
    // const res = await adminService.getProdi();
    // prodiOptions.value = res.data || res;
  } catch (error) {
    console.error('Error fetching prodi:', error);
  }
};

const fetchData = async (page = 1) => {
  try {
    loading.value = true;
    const res = await adminService.getMentorSempro({
      page,
      per_page: pagination.value.per_page,
      ...filters.value
    });
    items.value = res.data.data;
    pagination.value = {
      current_page: res.data.current_page,
      last_page: res.data.last_page,
      per_page: res.data.per_page,
      total: res.data.total
    };
  } catch (error) {
    toast.error('Gagal memuat data skripsi');
  } finally {
    loading.value = false;
  }
};

const fetchAvailableDosen = async () => {
  try {
    const res = await adminService.getAvailableMentorDosen();
    availableDosen.value = res.data;
  } catch (error) {
    console.error('Error fetching dosen:', error);
  }
};

const debounceFetch = () => {
  clearTimeout(timeout);
  timeout = setTimeout(() => {
    fetchData(1);
  }, 500);
};

const handlePageChange = (page) => {
  fetchData(page);
};

const changePerPage = (perPage) => {
  pagination.value.per_page = perPage;
  fetchData(1);
};

const openAssignModal = async (item) => {
  selectedItem.value = item;
  
  // Fill form if mentors exist
  const mentor1 = item.mentor_sempro?.find(m => m.jenis === 'mentor_1');
  const mentor2 = item.mentor_sempro?.find(m => m.jenis === 'mentor_2');
  
  form.value = {
    skripsi_id: item.id,
    mentor_1_id: mentor1?.dosen_id || '',
    mentor_2_id: mentor2?.dosen_id || '',
    original_mentor_1: mentor1?.dosen_id || null,
    original_mentor_2: mentor2?.dosen_id || null
  };
  
  await fetchAvailableDosen();
  showModal.value = true;
};

const closeModal = () => {
  showModal.value = false;
  selectedItem.value = null;
  form.value = {
    skripsi_id: '',
    mentor_1_id: '',
    mentor_2_id: '',
    original_mentor_1: null,
    original_mentor_2: null
  };
};

const submitAssignment = async () => {
  if (!form.value.mentor_1_id) {
    toast.error('Mentor utama harus dipilih');
    return;
  }
  
  if (form.value.mentor_1_id === form.value.mentor_2_id) {
    toast.error('Mentor utama dan pendamping tidak boleh sama');
    return;
  }

  try {
    loadingSubmit.value = true;
    
    await adminService.assignMentorSempro({
      skripsi_id: form.value.skripsi_id,
      mentor_1_id: form.value.mentor_1_id,
      mentor_2_id: form.value.mentor_2_id || null
    });
    
    toast.success('Mentor berhasil ditentukan');
    closeModal();
    fetchData(pagination.value.current_page);
  } catch (error) {
    toast.error(error.response?.data?.message || 'Gagal menyimpan penentuan mentor');
  } finally {
    loadingSubmit.value = false;
  }
};

const downloadPdf = async (id) => {
  try {
    const res = await adminService.getSuratMentorSempro(id);
    const url = window.URL.createObjectURL(new Blob([res.data]));
    const link = document.createElement('a');
    link.href = url;
    link.setAttribute('download', `Mentor_Sempro_${id}.pdf`);
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
  } catch (error) {
    toast.error('Gagal mengunduh PDF');
  }
};

const getInitials = (name) => {
  if (!name) return "?";
  return name
    .split(" ")
    .map((n) => n[0])
    .slice(0, 2)
    .join("")
    .toUpperCase();
};

const getStatusLabel = (status) => {
  const map = {
    penentuan_mentor: "Penentuan Mentor",
    mentor: "Mentor Ditentukan",
    proposal: "Proposal",
    sempro: "Seminar Proposal",
  };
  return map[status] || status || "-";
};

const getStatusClass = (status) => {
  const map = {
    penentuan_mentor: "bg-yellow-100 text-yellow-800",
    mentor: "bg-blue-100 text-blue-800",
    proposal: "bg-purple-100 text-purple-800",
    sempro: "bg-cyan-100 text-cyan-800",
  };
  return map[status] || "bg-gray-100 text-gray-800";
};

onMounted(() => {
  fetchProdi();
  fetchData();
});
</script>

<style scoped>
.modal-fade-enter-active,
.modal-fade-leave-active {
  transition: opacity 0.2s ease;
}

.modal-fade-enter-from,
.modal-fade-leave-to {
  opacity: 0;
}
</style>
