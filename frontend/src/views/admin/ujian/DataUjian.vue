<template>
  <div class="flex flex-col gap-6">
    <div class="flex flex-col gap-1">
      <div class="flex items-center justify-between">
        <div>
          <h1 class="text-text-main text-3xl font-bold leading-tight">
            Ujian Skripsi
          </h1>
          <p class="text-text-secondary text-sm font-normal">
            Daftar mahasiswa yang siap melaksanakan ujian akhir skripsi.
          </p>
        </div>
        <div class="flex gap-3">
          <select
            v-model="filterStatus"
            @change="fetchUjian"
            class="px-4 py-2 bg-white border border-border-light rounded-lg text-sm focus:ring-1 focus:ring-primary"
          >
            <option value="">Semua Status</option>
            <option value="pending">Menunggu Nilai</option>
            <option value="selesai">Selesai</option>
          </select>
          <button
            @click="openScheduleModal"
            class="flex items-center gap-2 px-4 py-2 bg-primary text-white rounded-lg hover:bg-blue-600 transition-all text-sm font-bold shadow-md shadow-primary/20"
          >
            <span class="material-symbols-outlined text-[18px]">add</span>
            Jadwalkan Baru
          </button>
        </div>
      </div>
    </div>

    <!-- Loading -->
    <div v-if="loading" class="p-12 text-center">
      <div
        class="animate-spin rounded-full h-8 w-8 border-b-2 border-primary mx-auto"
      ></div>
      <p class="text-text-secondary text-sm mt-3">Memuat data...</p>
    </div>

    <!-- Table Container -->
    <div
      v-else
      class="flex flex-col rounded-xl border border-border-light bg-surface-light dark:bg-surface-light overflow-hidden shadow-sm"
    >
      <div
        class="p-6 border-b border-border-light flex flex-wrap gap-4 justify-between items-center bg-gray-50/50 dark:bg-sidebar-light/50"
      >
        <div class="flex gap-4 items-center">
          <h3 class="text-text-main text-lg font-bold">Jadwal Ujian</h3>
          <span
            class="px-2 py-0.5 rounded-md bg-blue-50 text-blue-600 text-xs font-bold border border-blue-100"
            >{{ pagination.total }} Data</span
          >
        </div>
        <div class="relative">
          <input
            v-model="searchQuery"
            @input="debouncedSearch"
            class="pl-9 pr-4 py-2 rounded-lg border border-border-light bg-white text-sm w-64 focus:ring-1 focus:ring-primary"
            placeholder="Cari mahasiswa..."
          />
          <span
            class="material-symbols-outlined absolute left-2 top-2 text-[18px] text-text-secondary"
            >search</span
          >
        </div>
      </div>

      <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
          <thead>
            <tr
              class="bg-sidebar-light/50 border-b border-border-light dark:bg-sidebar-light/50"
            >
              <th
                class="p-4 text-[10px] font-bold tracking-widest text-text-secondary uppercase w-[25%]"
              >
                Mahasiswa
              </th>
              <th
                class="p-4 text-[10px] font-bold tracking-widest text-text-secondary uppercase w-[25%]"
              >
                Judul Skripsi
              </th>
              <th
                class="p-4 text-[10px] font-bold tracking-widest text-text-secondary uppercase w-[20%]"
              >
                Jadwal &amp; Ruang
              </th>
              <th
                class="p-4 text-[10px] font-bold tracking-widest text-text-secondary uppercase w-[15%]"
              >
                Status
              </th>
              <th
                class="p-4 text-[10px] font-bold tracking-widest text-text-secondary uppercase w-[15%] text-right"
              >
                Aksi
              </th>
            </tr>
          </thead>
          <tbody class="divide-y divide-border-light">
            <tr v-if="ujianList.length === 0">
              <td colspan="5" class="p-12 text-center text-text-secondary">
                Tidak ada data ujian
              </td>
            </tr>
            <tr
              v-for="item in ujianList"
              :key="item.id"
              class="group hover:bg-sidebar-light/30 transition-colors"
            >
              <td class="p-4 align-top">
                <div class="flex items-center gap-3">
                  <div
                    class="size-10 rounded-full flex items-center justify-center text-xs font-bold shrink-0"
                    :class="getAvatarColor(item.skripsi?.mahasiswa?.nama)"
                  >
                    {{ getInitials(item.skripsi?.mahasiswa?.nama) }}
                  </div>
                  <div>
                    <p class="font-bold text-text-main text-sm">
                      {{ item.skripsi?.mahasiswa?.nama || "-" }}
                    </p>
                    <p class="text-xs text-text-secondary font-medium">
                      {{ item.skripsi?.mahasiswa?.nim || "-" }}
                    </p>
                  </div>
                </div>
              </td>
              <td class="p-4 align-top">
                <p
                  class="text-sm font-medium text-text-main line-clamp-2 leading-snug"
                >
                  {{ item.skripsi?.judul || "-" }}
                </p>
                <div class="mt-2 flex flex-col gap-1">
                  <p
                    class="text-[10px] text-text-secondary"
                    v-if="item.skripsi?.pembimbing?.length"
                  >
                    <span class="font-bold">Pembimbing:</span>
                    {{ item.skripsi?.pembimbing[0]?.dosen?.nama_lengkap }}
                  </p>
                </div>
              </td>
              <td class="p-4 align-top">
                <div class="flex flex-col gap-1">
                  <div
                    class="flex items-center gap-2 text-text-main text-xs font-bold"
                  >
                    <span
                      class="material-symbols-outlined text-[16px] text-primary"
                      >calendar_month</span
                    >
                    {{ formatDate(item.tanggal) }}
                  </div>
                  <div
                    class="flex items-center gap-2 text-text-secondary text-xs"
                  >
                    <span class="material-symbols-outlined text-[16px]"
                      >schedule</span
                    >
                    {{ item.waktu || "-" }}
                  </div>
                  <div
                    class="flex items-center gap-2 text-text-secondary text-xs mt-1"
                  >
                    <span class="material-symbols-outlined text-[16px]"
                      >meeting_room</span
                    >
                    {{ item.ruangan || "-" }}
                  </div>
                </div>
              </td>
              <td class="p-4 align-top">
                <span
                  class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[11px] font-bold"
                  :class="getStatusClass(item.status)"
                >
                  <span
                    class="size-1.5 rounded-full"
                    :class="getStatusDot(item.status)"
                  ></span>
                  {{ getStatusLabel(item.status) }}
                </span>
              </td>
              <td class="p-4 text-right align-middle">
                <button
                  v-if="item.status === 'pending'"
                  @click="openNilaiModal(item)"
                  class="inline-flex items-center justify-center px-4 py-2 text-xs font-bold text-white bg-primary rounded-lg hover:bg-primary/90 transition-all shadow-sm w-full md:w-auto"
                >
                  Input Nilai
                </button>
                <button
                  v-else
                  @click="viewDetail(item)"
                  class="inline-flex items-center justify-center px-4 py-2 text-xs font-bold text-primary bg-primary/10 rounded-lg hover:bg-primary/20 transition-all"
                >
                  Lihat Detail
                </button>
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <!-- Pagination -->
      <div
        class="p-4 border-t border-border-light bg-gray-50 dark:bg-sidebar-light flex justify-between items-center"
      >
        <span class="text-xs text-text-secondary">
          Menampilkan {{ pagination.from || 0 }}-{{ pagination.to || 0 }} dari
          {{ pagination.total }} data
        </span>
        <div class="flex gap-1">
          <button
            @click="goToPage(pagination.current_page - 1)"
            :disabled="pagination.current_page <= 1"
            class="size-8 flex items-center justify-center rounded border border-border-light hover:bg-gray-50 text-text-secondary disabled:opacity-50"
          >
            <span class="material-symbols-outlined text-sm">chevron_left</span>
          </button>
          <button
            class="size-8 flex items-center justify-center rounded border border-primary bg-primary text-white text-xs font-bold"
          >
            {{ pagination.current_page }}
          </button>
          <button
            @click="goToPage(pagination.current_page + 1)"
            :disabled="pagination.current_page >= pagination.last_page"
            class="size-8 flex items-center justify-center rounded border border-border-light hover:bg-gray-50 text-text-secondary"
          >
            <span class="material-symbols-outlined text-sm">chevron_right</span>
          </button>
        </div>
      </div>
    </div>

    <!-- Input Nilai Modal -->
    <div
      v-if="showNilaiModal"
      class="fixed inset-0 bg-black/50 flex items-center justify-center z-50 p-4"
    >
      <div
        class="bg-white dark:bg-surface-light rounded-xl shadow-2xl w-full max-w-md"
      >
        <div class="p-6 border-b border-border-light">
          <h2 class="text-xl font-bold text-text-main">Input Nilai Ujian</h2>
          <p class="text-sm text-text-secondary mt-1">
            {{ selectedUjian?.skripsi?.mahasiswa?.nama }}
          </p>
        </div>
        <form @submit.prevent="saveNilai" class="p-6 space-y-4">
          <div>
            <label class="block text-sm font-medium text-text-main mb-1"
              >Nilai (0-100)</label
            >
            <input
              v-model.number="nilaiForm.nilai"
              type="number"
              min="0"
              max="100"
              class="w-full px-3 py-2 border border-border-light rounded-lg focus:ring-2 focus:ring-primary/20 focus:border-primary"
              required
            />
          </div>
          <div>
            <label class="block text-sm font-medium text-text-main mb-1"
              >Hasil</label
            >
            <select
              v-model="nilaiForm.hasil"
              class="w-full px-3 py-2 border border-border-light rounded-lg focus:ring-2 focus:ring-primary/20 focus:border-primary"
              required
            >
              <option value="lulus">Lulus</option>
              <option value="lulus_revisi">Lulus dengan Revisi</option>
              <option value="tidak_lulus">Tidak Lulus</option>
            </select>
          </div>
          <div>
            <label class="block text-sm font-medium text-text-main mb-1"
              >Catatan</label
            >
            <textarea
              v-model="nilaiForm.catatan"
              rows="3"
              class="w-full px-3 py-2 border border-border-light rounded-lg focus:ring-2 focus:ring-primary/20 focus:border-primary"
            ></textarea>
          </div>
          <div class="flex gap-3 pt-4">
            <button
              type="button"
              @click="showNilaiModal = false"
              class="flex-1 px-4 py-2.5 border border-border-light rounded-lg text-text-secondary hover:bg-background-light transition-colors"
            >
              Batal
            </button>
            <button
              type="submit"
              :disabled="saving"
              class="flex-1 px-4 py-2.5 bg-primary text-white rounded-lg hover:bg-blue-600 transition-colors disabled:opacity-50"
            >
              {{ saving ? "Menyimpan..." : "Simpan Nilai" }}
            </button>
          </div>
        </form>
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
const ujianList = ref([]);
const searchQuery = ref("");
const filterStatus = ref("");
const showNilaiModal = ref(false);
const selectedUjian = ref(null);

const pagination = reactive({
  current_page: 1,
  last_page: 1,
  total: 0,
  from: 0,
  to: 0,
});

const nilaiForm = reactive({
  nilai: 0,
  hasil: "lulus",
  catatan: "",
});

let searchTimeout = null;

const fetchUjian = async () => {
  try {
    loading.value = true;
    const params = {
      page: pagination.current_page,
      search: searchQuery.value,
      status: filterStatus.value,
    };
    const response = await adminService.getUjian(params);
    if (response.success) {
      ujianList.value = response.data.data || response.data;
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
    console.error("Failed to fetch ujian:", error);
  } finally {
    loading.value = false;
  }
};

const debouncedSearch = () => {
  clearTimeout(searchTimeout);
  searchTimeout = setTimeout(() => {
    pagination.current_page = 1;
    fetchUjian();
  }, 300);
};

const goToPage = (page) => {
  if (page >= 1 && page <= pagination.last_page) {
    pagination.current_page = page;
    fetchUjian();
  }
};

const openNilaiModal = (item) => {
  selectedUjian.value = item;
  nilaiForm.nilai = item.nilai || 0;
  nilaiForm.hasil = item.hasil || "lulus";
  nilaiForm.catatan = "";
  showNilaiModal.value = true;
};

const saveNilai = async () => {
  try {
    saving.value = true;
    await adminService.updateUjian(selectedUjian.value.id, {
      nilai: nilaiForm.nilai,
      hasil: nilaiForm.hasil,
      catatan: nilaiForm.catatan,
      status: "selesai",
    });
    showNilaiModal.value = false;
    fetchUjian();
  } catch (error) {
    console.error("Failed to save nilai:", error);
    alert(
      "Gagal menyimpan nilai: " +
        (error.response?.data?.message || error.message),
    );
  } finally {
    saving.value = false;
  }
};

const viewDetail = (item) => {
  router.push(`/admin/ujian/${item.id}`);
};

const openScheduleModal = () => {
  router.push("/admin/ujian/create");
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

const formatDate = (date) => {
  if (!date) return "-";
  return new Date(date).toLocaleDateString("id-ID", {
    day: "numeric",
    month: "short",
    year: "numeric",
  });
};

const getStatusClass = (status) => {
  const classes = {
    pending: "bg-orange-50 text-orange-600 border border-orange-100",
    selesai: "bg-green-50 text-green-600 border border-green-100",
  };
  return classes[status] || "bg-gray-50 text-gray-600 border border-gray-100";
};

const getStatusDot = (status) => {
  const dots = { pending: "bg-orange-600", selesai: "bg-green-600" };
  return dots[status] || "bg-gray-600";
};

const getStatusLabel = (status) => {
  const labels = { pending: "Menunggu Nilai", selesai: "Selesai" };
  return labels[status] || status;
};

onMounted(() => {
  fetchUjian();
});
</script>
