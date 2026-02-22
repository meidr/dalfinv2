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
        <span class="text-text-main font-medium">Seminar Proposal</span>
      </div>
      <h1 class="text-text-main text-3xl font-bold leading-tight">
        Seminar Proposal
      </h1>
      <p class="text-text-secondary text-sm font-normal">
        Kelola penjadwalan seminar proposal mahasiswa.
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
            Total Mahasiswa
          </p>
          <div class="bg-primary/10 p-2 rounded-lg text-primary">
            <span class="material-symbols-outlined">school</span>
          </div>
        </div>
        <div class="mt-2">
          <p class="text-text-main text-3xl font-bold leading-tight">
            {{ pagination.total }}
          </p>
          <div class="flex items-center gap-1 mt-1">
            <span class="material-symbols-outlined text-primary text-[18px]"
              >people</span
            >
            <p class="text-primary text-xs font-semibold">Eligible sempro</p>
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
            Sudah Dijadwalkan
          </p>
          <div class="bg-green-100 p-2 rounded-lg text-green-600">
            <span class="material-symbols-outlined">event_available</span>
          </div>
        </div>
        <div class="mt-2">
          <p class="text-text-main text-3xl font-bold leading-tight">
            {{ statsCount.terjadwal }}
          </p>
          <div class="flex items-center gap-1 mt-1">
            <span class="material-symbols-outlined text-green-600 text-[18px]"
              >check_circle</span
            >
            <p class="text-green-600 text-xs font-semibold">Terjadwal sempro</p>
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
            Belum Dijadwalkan
          </p>
          <div class="bg-orange-100 p-2 rounded-lg text-orange-600">
            <span class="material-symbols-outlined">event_busy</span>
          </div>
        </div>
        <div class="mt-2">
          <p class="text-text-main text-3xl font-bold leading-tight">
            {{ statsCount.belum }}
          </p>
          <div class="flex items-center gap-1 mt-1">
            <span class="material-symbols-outlined text-orange-600 text-[18px]"
              >pending</span
            >
            <p class="text-orange-600 text-xs font-semibold">Menunggu jadwal</p>
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
        <!-- Filters -->
        <div class="flex gap-3 w-full md:w-auto">
          <select
            v-model="filterStatus"
            @change="fetchSeminar"
            class="px-4 py-2.5 bg-surface-light border border-border-light rounded-lg text-text-secondary text-sm focus:ring-1 focus:ring-primary"
          >
            <option value="">Semua Status</option>
            <option value="pengajuan">Pengajuan</option>
            <option value="proposal">Proposal</option>
            <option value="sempro">Seminar Proposal</option>
            <option value="bimbingan">Bimbingan</option>
          </select>
          <select
            v-model="filterJadwal"
            @change="fetchSeminar"
            class="px-4 py-2.5 bg-surface-light border border-border-light rounded-lg text-text-secondary text-sm focus:ring-1 focus:ring-primary"
          >
            <option value="">Semua Jadwal</option>
            <option value="terjadwal">Sudah Dijadwalkan</option>
            <option value="belum">Belum Dijadwalkan</option>
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

      <!-- Table -->
      <div v-else class="overflow-x-auto">
        <table class="w-full text-left text-sm whitespace-nowrap">
          <thead
            class="bg-sidebar-light/50 text-text-secondary font-medium border-b border-border-light"
          >
            <tr>
              <th class="px-6 py-4">Mahasiswa</th>
              <th class="px-6 py-4">Judul Skripsi</th>
              <th class="px-6 py-4">Pembimbing</th>
              <th class="px-6 py-4">Status</th>
              <th class="px-6 py-4">Jadwal Sempro</th>
              <th class="px-6 py-4 text-right">Aksi</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-border-light">
            <tr v-if="seminarList.length === 0">
              <td
                colspan="6"
                class="px-6 py-12 text-center text-text-secondary"
              >
                Tidak ada data mahasiswa
              </td>
            </tr>
            <tr
              v-for="item in seminarList"
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
              <td class="px-6 py-4">
                <template v-if="item.is_scheduled && item.sempro_seminar">
                  <div class="flex items-center gap-2 text-sm text-text-main">
                    <span
                      class="material-symbols-outlined text-[18px] text-green-600"
                      >event_available</span
                    >
                    {{ formatDate(item.sempro_seminar.tanggal) }}
                  </div>
                  <p class="text-xs text-text-secondary mt-0.5">
                    {{ item.sempro_seminar.waktu || "" }}
                    {{
                      item.sempro_seminar.ruangan
                        ? "• " + item.sempro_seminar.ruangan
                        : ""
                    }}
                  </p>
                </template>
                <span
                  v-else
                  class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-medium bg-orange-50 text-orange-600 border border-orange-100"
                >
                  <span class="material-symbols-outlined text-[14px]"
                    >event_busy</span
                  >
                  Belum Dijadwalkan
                </span>
              </td>
              <td class="px-6 py-4 text-right">
                <div
                  class="flex items-center justify-end gap-2 opacity-0 group-hover:opacity-100 transition-opacity"
                >
                  <button
                    v-if="!item.is_scheduled"
                    @click="openScheduleModal(item)"
                    class="inline-flex items-center justify-center gap-1 px-3 py-1.5 text-xs font-bold text-white bg-primary rounded-lg hover:bg-primary/90 transition-all shadow-sm"
                  >
                    <span class="material-symbols-outlined text-[16px]"
                      >calendar_month</span
                    >
                    Jadwalkan
                  </button>
                  <button
                    v-if="item.is_scheduled"
                    @click="viewDetail(item.sempro_seminar)"
                    class="inline-flex items-center justify-center gap-1 px-3 py-1.5 text-xs font-bold text-primary bg-primary/10 rounded-lg hover:bg-primary/20 transition-all"
                  >
                    <span class="material-symbols-outlined text-[16px]"
                      >visibility</span
                    >
                    Detail
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

    <!-- Schedule Modal -->
    <Transition name="modal-fade">
      <div
        v-if="showScheduleModal"
        class="fixed inset-0 bg-black/50 flex items-center justify-center z-50 p-4"
      >
        <div
          class="bg-white dark:bg-surface-light rounded-xl shadow-2xl w-full max-w-lg"
        >
          <div class="p-6 border-b border-border-light">
            <h2 class="text-xl font-bold text-text-main">
              Jadwalkan Seminar Proposal
            </h2>
            <p class="text-sm text-text-secondary mt-1">
              {{ selectedSkripsi?.mahasiswa?.nama }} -
              {{ selectedSkripsi?.judul }}
            </p>
          </div>
          <form @submit.prevent="saveSeminarSchedule" class="p-6 space-y-4">
            <div>
              <label class="block text-sm font-medium text-text-main mb-1"
                >Tanggal <span class="text-red-500">*</span></label
              >
              <input
                v-model="scheduleForm.tanggal"
                type="date"
                class="w-full px-3 py-2 border border-border-light rounded-lg focus:ring-2 focus:ring-primary/20 focus:border-primary"
                required
              />
            </div>
            <div class="grid grid-cols-2 gap-4">
              <div>
                <label class="block text-sm font-medium text-text-main mb-1"
                  >Jam Mulai <span class="text-red-500">*</span></label
                >
                <input
                  v-model="scheduleForm.waktu"
                  type="time"
                  class="w-full px-3 py-2 border border-border-light rounded-lg focus:ring-2 focus:ring-primary/20 focus:border-primary"
                  required
                />
              </div>
              <div>
                <label class="block text-sm font-medium text-text-main mb-1"
                  >Ruangan <span class="text-red-500">*</span></label
                >
                <input
                  v-model="scheduleForm.ruangan"
                  type="text"
                  class="w-full px-3 py-2 border border-border-light rounded-lg focus:ring-2 focus:ring-primary/20 focus:border-primary"
                  placeholder="Ruang A101"
                  required
                />
              </div>
            </div>

            <div class="flex gap-3 pt-4">
              <button
                type="button"
                @click="showScheduleModal = false"
                class="flex-1 px-4 py-2.5 border border-border-light rounded-lg text-text-secondary hover:bg-background-light transition-colors"
              >
                Batal
              </button>
              <button
                type="submit"
                :disabled="saving"
                class="flex-1 px-4 py-2.5 bg-primary text-white rounded-lg hover:bg-blue-600 transition-colors disabled:opacity-50"
              >
                {{ saving ? "Menyimpan..." : "Simpan Jadwal" }}
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
import { useRouter } from "vue-router";
import adminService from "../../../services/adminService";

const router = useRouter();

const loading = ref(true);
const saving = ref(false);
const seminarList = ref([]);
const searchQuery = ref("");
const filterStatus = ref("");
const filterJadwal = ref("");
const showScheduleModal = ref(false);
const selectedSkripsi = ref(null);

const pagination = reactive({
  current_page: 1,
  last_page: 1,
  total: 0,
  from: 0,
  to: 0,
});

const statsCount = reactive({
  terjadwal: 0,
  belum: 0,
});

const scheduleForm = reactive({
  tanggal: "",
  waktu: "",
  ruangan: "",
});

let searchTimeout = null;

const fetchSeminar = async () => {
  try {
    loading.value = true;
    const params = {
      page: pagination.current_page,
      search: searchQuery.value,
      status: filterStatus.value,
      jadwal: filterJadwal.value,
    };
    const response = await adminService.getSeminar(params);
    if (response.success) {
      seminarList.value = response.data.data || response.data;
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
    console.error("Failed to fetch seminar data:", error);
  } finally {
    loading.value = false;
  }
};

const fetchStats = async () => {
  try {
    // Fetch counts for terjadwal and belum
    const [terjadwalRes, belumRes] = await Promise.all([
      adminService.getSeminar({ jadwal: "terjadwal", per_page: 1 }),
      adminService.getSeminar({ jadwal: "belum", per_page: 1 }),
    ]);
    if (terjadwalRes.success) {
      statsCount.terjadwal = terjadwalRes.data.total || 0;
    }
    if (belumRes.success) {
      statsCount.belum = belumRes.data.total || 0;
    }
  } catch (error) {
    console.error("Failed to fetch stats:", error);
  }
};

const debouncedSearch = () => {
  clearTimeout(searchTimeout);
  searchTimeout = setTimeout(() => {
    pagination.current_page = 1;
    fetchSeminar();
  }, 300);
};

const goToPage = (page) => {
  if (page >= 1 && page <= pagination.last_page) {
    pagination.current_page = page;
    fetchSeminar();
  }
};

const openScheduleModal = (item) => {
  selectedSkripsi.value = item;
  scheduleForm.tanggal = "";
  scheduleForm.waktu = "";
  scheduleForm.ruangan = "";
  showScheduleModal.value = true;
};

const saveSeminarSchedule = async () => {
  try {
    saving.value = true;
    await adminService.createSeminar({
      skripsi_id: selectedSkripsi.value.id,
      jenis: "sempro",
      tanggal: scheduleForm.tanggal,
      waktu: scheduleForm.waktu,
      ruangan: scheduleForm.ruangan,
    });
    showScheduleModal.value = false;
    fetchSeminar();
    fetchStats();
  } catch (error) {
    console.error("Failed to save schedule:", error);
    alert(
      "Gagal menyimpan jadwal: " +
        (error.response?.data?.message || error.message),
    );
  } finally {
    saving.value = false;
  }
};

const viewDetail = (seminar) => {
  if (seminar && seminar.id) {
    router.push(`/admin/seminar/${seminar.id}`);
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

const getPembimbing = (pembimbingList) => {
  if (!pembimbingList || pembimbingList.length === 0) return "-";
  const p1 = pembimbingList.find((p) => p.jenis === "pembimbing_1");
  return (
    p1?.dosen?.nama_lengkap || pembimbingList[0]?.dosen?.nama_lengkap || "-"
  );
};

const formatDate = (date) => {
  if (!date) return "-";
  return new Date(date).toLocaleDateString("id-ID", {
    weekday: "short",
    day: "numeric",
    month: "short",
    year: "numeric",
  });
};

const getStatusClass = (status) => {
  const classes = {
    pengajuan: "bg-gray-50 text-gray-600 border border-gray-100",
    proposal: "bg-yellow-50 text-yellow-600 border border-yellow-100",
    sempro: "bg-blue-50 text-blue-600 border border-blue-100",
  };
  return classes[status] || "bg-gray-50 text-gray-600 border border-gray-100";
};

const getStatusDot = (status) => {
  const dots = {
    pengajuan: "bg-gray-600",
    proposal: "bg-yellow-600",
    sempro: "bg-blue-600",
  };
  return dots[status] || "bg-gray-600";
};

const getStatusLabel = (status) => {
  const labels = {
    pengajuan: "Pengajuan",
    proposal: "Proposal",
    sempro: "Sem. Proposal",
  };
  return labels[status] || status;
};

onMounted(() => {
  fetchSeminar();
  fetchStats();
});
</script>
