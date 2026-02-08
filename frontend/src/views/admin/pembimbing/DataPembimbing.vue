<template>
  <div class="flex flex-col gap-6">
    <div class="flex flex-col gap-1">
      <h1 class="text-text-main text-3xl font-bold leading-tight">
        Penetapan Pembimbing
      </h1>
      <p class="text-text-secondary text-sm font-normal">
        Kelola pengajuan judul skripsi dan penetapan dosen pembimbing mahasiswa.
      </p>
    </div>

    <!-- Filter/Action Bar -->
    <div
      class="flex flex-col rounded-xl border border-border-light bg-surface-light dark:bg-surface-light overflow-hidden shadow-sm"
    >
      <div
        class="p-6 border-b border-border-light flex flex-wrap gap-4 justify-between items-center bg-gray-50/50 dark:bg-sidebar-light/50"
      >
        <div>
          <h3 class="text-text-main text-lg font-bold">
            Mahasiswa Menunggu Pembimbing
          </h3>
          <p class="text-text-secondary text-xs">
            Menampilkan daftar mahasiswa yang menunggu penetapan pembimbing.
          </p>
        </div>
        <div class="flex gap-2">
          <button
            @click="fetchPembimbing"
            class="flex items-center gap-2 px-4 py-2 bg-primary/10 text-primary rounded-lg text-xs font-bold hover:bg-primary/20 transition-colors"
          >
            <span class="material-symbols-outlined text-[16px]">refresh</span>
            Refresh
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
        <table class="w-full text-left border-collapse">
          <thead>
            <tr
              class="bg-sidebar-light/50 border-b border-border-light dark:bg-sidebar-light/50"
            >
              <th
                class="p-4 pl-6 text-[10px] font-bold tracking-widest text-text-secondary uppercase w-1/4"
              >
                Mahasiswa
              </th>
              <th
                class="p-4 text-[10px] font-bold tracking-widest text-text-secondary uppercase w-1/2"
              >
                Judul Skripsi
              </th>
              <th
                class="p-4 pr-6 text-[10px] font-bold tracking-widest text-text-secondary uppercase text-right"
              >
                Action
              </th>
            </tr>
          </thead>
          <tbody class="divide-y divide-border-light">
            <tr v-if="skripsiList.length === 0">
              <td colspan="3" class="p-12 text-center text-text-secondary">
                Tidak ada mahasiswa yang menunggu penetapan pembimbing
              </td>
            </tr>
            <tr
              v-for="item in skripsiList"
              :key="item.id"
              class="group hover:bg-sidebar-light/30 transition-colors"
            >
              <td class="p-4 pl-6 align-top">
                <div class="flex items-start gap-3">
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
                    <p class="text-[10px] text-text-secondary mt-1">
                      {{ item.mahasiswa?.prodi?.nama || "-" }}
                    </p>
                  </div>
                </div>
              </td>
              <td class="p-4 align-top">
                <div class="flex flex-col gap-2">
                  <p class="text-sm font-medium text-text-main leading-relaxed">
                    {{ item.judul || "-" }}
                  </p>
                  <div class="flex gap-2">
                    <span
                      class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-medium bg-blue-50 text-blue-600 border border-blue-100"
                      >{{ getStatusLabel(item.status) }}</span
                    >
                  </div>
                </div>
              </td>
              <td class="p-4 pr-6 align-top text-right">
                <button
                  @click="openAssignModal(item)"
                  class="inline-flex items-center justify-center px-4 py-2 text-xs font-bold text-white bg-primary rounded-lg hover:bg-primary/90 transition-all shadow-sm shadow-primary/20"
                >
                  Tetapkan
                </button>
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <!-- Footer -->
      <div
        class="p-4 border-t border-border-light flex items-center justify-between bg-gray-50/50 dark:bg-sidebar-light/50"
      >
        <p class="text-xs text-text-secondary">
          Showing
          <span class="font-bold text-text-main"
            >{{ pagination.from || 0 }}-{{ pagination.to || 0 }}</span
          >
          of
          <span class="font-bold text-text-main">{{ pagination.total }}</span>
          mahasiswa
        </p>
        <div class="flex gap-2">
          <button
            @click="goToPage(pagination.current_page - 1)"
            :disabled="pagination.current_page <= 1"
            class="size-8 flex items-center justify-center rounded-md border border-border-light bg-white text-text-secondary hover:bg-gray-50 transition-colors disabled:opacity-50"
          >
            <span class="material-symbols-outlined text-[16px]"
              >chevron_left</span
            >
          </button>
          <button
            @click="goToPage(pagination.current_page + 1)"
            :disabled="pagination.current_page >= pagination.last_page"
            class="size-8 flex items-center justify-center rounded-md border border-border-light bg-white text-text-secondary hover:bg-gray-50 transition-colors"
          >
            <span class="material-symbols-outlined text-[16px]"
              >chevron_right</span
            >
          </button>
        </div>
      </div>
    </div>

    <!-- Assign Pembimbing Modal -->
    <div
      v-if="showAssignModal"
      class="fixed inset-0 bg-black/50 flex items-center justify-center z-50 p-4"
    >
      <div
        class="bg-white dark:bg-surface-light rounded-xl shadow-2xl w-full max-w-lg"
      >
        <div class="p-6 border-b border-border-light">
          <h2 class="text-xl font-bold text-text-main">Tetapkan Pembimbing</h2>
          <p class="text-sm text-text-secondary mt-1">
            {{ selectedSkripsi?.mahasiswa?.nama }} -
            {{ selectedSkripsi?.judul }}
          </p>
        </div>
        <form @submit.prevent="assignPembimbing" class="p-6 space-y-4">
          <div>
            <label class="block text-sm font-medium text-text-main mb-1"
              >Pembimbing 1</label
            >
            <select
              v-model="assignForm.pembimbing_1_id"
              class="w-full px-3 py-2 border border-border-light rounded-lg focus:ring-2 focus:ring-primary/20 focus:border-primary"
              required
            >
              <option value="">Pilih Dosen</option>
              <option
                v-for="dosen in dosenList"
                :key="dosen.id"
                :value="dosen.id"
              >
                {{ dosen.nama_lengkap }}
              </option>
            </select>
          </div>
          <div>
            <label class="block text-sm font-medium text-text-main mb-1"
              >Pembimbing 2 (Opsional)</label
            >
            <select
              v-model="assignForm.pembimbing_2_id"
              class="w-full px-3 py-2 border border-border-light rounded-lg focus:ring-2 focus:ring-primary/20 focus:border-primary"
            >
              <option value="">Pilih Dosen</option>
              <option
                v-for="dosen in dosenList"
                :key="dosen.id"
                :value="dosen.id"
              >
                {{ dosen.nama_lengkap }}
              </option>
            </select>
          </div>
          <div class="flex gap-3 pt-4">
            <button
              type="button"
              @click="showAssignModal = false"
              class="flex-1 px-4 py-2.5 border border-border-light rounded-lg text-text-secondary hover:bg-background-light transition-colors"
            >
              Batal
            </button>
            <button
              type="submit"
              :disabled="saving"
              class="flex-1 px-4 py-2.5 bg-primary text-white rounded-lg hover:bg-blue-600 transition-colors disabled:opacity-50"
            >
              {{ saving ? "Menyimpan..." : "Tetapkan" }}
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, reactive } from "vue";
import adminService from "../../../services/adminService";

const loading = ref(true);
const saving = ref(false);
const skripsiList = ref([]);
const dosenList = ref([]);
const showAssignModal = ref(false);
const selectedSkripsi = ref(null);

const pagination = reactive({
  current_page: 1,
  last_page: 1,
  total: 0,
  from: 0,
  to: 0,
});

const assignForm = reactive({
  pembimbing_1_id: "",
  pembimbing_2_id: "",
});

const fetchPembimbing = async () => {
  try {
    loading.value = true;
    // Get skripsi that need pembimbing assignment (status: proposal, no pembimbing)
    const params = {
      page: pagination.current_page,
      status: "proposal",
    };
    const response = await adminService.getSkripsi(params);
    if (response.success) {
      // Filter those without pembimbing
      const data = response.data.data || response.data;
      skripsiList.value = Array.isArray(data)
        ? data.filter((s) => !s.pembimbing || s.pembimbing.length === 0)
        : [];
      if (response.data.current_page) {
        Object.assign(pagination, {
          current_page: response.data.current_page,
          last_page: response.data.last_page,
          total: response.data.total,
          from: response.data.from,
          to: response.data.to,
        });
      }
    }
  } catch (error) {
    console.error("Failed to fetch pembimbing data:", error);
  } finally {
    loading.value = false;
  }
};

const fetchDosen = async () => {
  try {
    const response = await adminService.getDosen({ per_page: 100 });
    if (response.success) {
      dosenList.value = response.data.data || response.data;
    }
  } catch (error) {
    console.error("Failed to fetch dosen:", error);
  }
};

const goToPage = (page) => {
  if (page >= 1 && page <= pagination.last_page) {
    pagination.current_page = page;
    fetchPembimbing();
  }
};

const openAssignModal = (item) => {
  selectedSkripsi.value = item;
  assignForm.pembimbing_1_id = "";
  assignForm.pembimbing_2_id = "";
  showAssignModal.value = true;
};

const assignPembimbing = async () => {
  try {
    saving.value = true;
    await adminService.assignPembimbing(selectedSkripsi.value.id, {
      pembimbing_1_id: assignForm.pembimbing_1_id,
      pembimbing_2_id: assignForm.pembimbing_2_id || null,
    });
    showAssignModal.value = false;
    fetchPembimbing();
  } catch (error) {
    console.error("Failed to assign pembimbing:", error);
    alert(
      "Gagal menetapkan pembimbing: " +
        (error.response?.data?.message || error.message),
    );
  } finally {
    saving.value = false;
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

const getStatusLabel = (status) => {
  const labels = {
    proposal: "Baru Isi Judul",
    bimbingan: "Bimbingan",
    sempro: "Seminar Proposal",
  };
  return labels[status] || status;
};

onMounted(() => {
  fetchPembimbing();
  fetchDosen();
});
</script>
