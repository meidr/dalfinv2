<template>
  <div class="flex flex-col gap-6">
    <div class="flex flex-col gap-1">
      <h1 class="text-text-main text-3xl font-bold leading-tight">
        Seminar Proposal
      </h1>
      <p class="text-text-secondary text-sm font-normal">
        Daftar mahasiswa yang memenuhi syarat untuk melaksanakan seminar
        proposal.
      </p>
    </div>

    <!-- Table Container -->
    <div
      class="flex flex-col rounded-xl border border-border-light bg-surface-light dark:bg-surface-light overflow-hidden shadow-sm"
    >
      <div
        class="p-6 border-b border-border-light flex flex-wrap gap-4 justify-between items-center bg-gray-50/50 dark:bg-sidebar-light/50"
      >
        <div>
          <h3 class="text-text-main text-lg font-bold">Antrian Seminar</h3>
          <p class="text-text-secondary text-xs">
            Kelola jadwal dan input nilai.
          </p>
        </div>
        <div class="flex items-center gap-3">
          <select
            v-model="filterJenis"
            @change="fetchSeminar"
            class="px-3 py-1.5 bg-white border border-border-light rounded-lg text-xs font-medium focus:ring-1 focus:ring-primary"
          >
            <option value="">Semua Jenis</option>
            <option value="sempro">Seminar Proposal</option>
            <option value="semhas">Seminar Hasil</option>
          </select>
          <select
            v-model="filterStatus"
            @change="fetchSeminar"
            class="px-3 py-1.5 bg-white border border-border-light rounded-lg text-xs font-medium focus:ring-1 focus:ring-primary"
          >
            <option value="">Semua Status</option>
            <option value="pending">Pending</option>
            <option value="terjadwal">Terjadwal</option>
            <option value="selesai">Selesai</option>
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

      <div v-else class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
          <thead>
            <tr
              class="bg-sidebar-light/50 border-b border-border-light dark:bg-sidebar-light/50"
            >
              <th
                class="p-4 text-[10px] font-bold tracking-widest text-text-secondary uppercase w-1/2"
              >
                Mahasiswa
              </th>
              <th
                class="p-4 text-[10px] font-bold tracking-widest text-text-secondary uppercase w-1/3"
              >
                Jadwal
              </th>
              <th
                class="p-4 text-[10px] font-bold tracking-widest text-text-secondary uppercase text-right"
              >
                Action
              </th>
            </tr>
          </thead>
          <tbody class="divide-y divide-border-light">
            <tr v-if="seminarList.length === 0">
              <td colspan="3" class="p-12 text-center text-text-secondary">
                Tidak ada data seminar
              </td>
            </tr>
            <tr
              v-for="item in seminarList"
              :key="item.id"
              class="group hover:bg-sidebar-light/30 transition-colors"
            >
              <td class="p-4 align-top">
                <div class="flex items-start gap-4">
                  <div
                    class="size-12 rounded-full flex items-center justify-center text-sm font-bold shrink-0"
                    :class="getAvatarColor(item.skripsi?.mahasiswa?.nama)"
                  >
                    {{ getInitials(item.skripsi?.mahasiswa?.nama) }}
                  </div>
                  <div class="flex flex-col gap-1">
                    <div>
                      <p class="font-bold text-text-main text-base">
                        {{ item.skripsi?.mahasiswa?.nama || "-" }}
                      </p>
                      <p class="text-xs text-text-secondary font-medium">
                        NIM: {{ item.skripsi?.mahasiswa?.nim || "-" }}
                      </p>
                    </div>
                    <p
                      class="text-sm text-text-main line-clamp-2 leading-relaxed"
                    >
                      {{ item.skripsi?.judul || "-" }}
                    </p>
                    <div class="mt-1">
                      <span
                        class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wide"
                        :class="getStatusClass(item.status)"
                      >
                        <span
                          class="size-1.5 rounded-full"
                          :class="getStatusDot(item.status)"
                        ></span>
                        {{ getStatusLabel(item.status) }}
                      </span>
                    </div>
                  </div>
                </div>
              </td>
              <td class="p-4 align-top">
                <div class="flex flex-col justify-center h-full pt-1">
                  <template v-if="item.tanggal">
                    <div class="flex items-center gap-2 text-sm text-text-main">
                      <span
                        class="material-symbols-outlined text-[18px] text-primary"
                        >calendar_today</span
                      >
                      {{ formatDate(item.tanggal) }}
                    </div>
                    <p class="text-xs text-text-secondary mt-1">
                      {{ item.waktu || "" }} - {{ item.ruangan || "" }}
                    </p>
                  </template>
                  <span
                    v-else
                    class="inline-flex items-center gap-2 text-text-secondary italic text-sm p-2 bg-gray-50 rounded-lg border border-gray-100 border-dashed w-fit"
                  >
                    <span class="material-symbols-outlined text-[18px]"
                      >calendar_today</span
                    >
                    Belum Dijadwalkan
                  </span>
                </div>
              </td>
              <td class="p-4 align-middle text-right">
                <button
                  v-if="!item.tanggal"
                  @click="openScheduleModal(item)"
                  class="inline-flex items-center justify-center px-4 py-2 text-xs font-bold text-white bg-primary rounded-lg hover:bg-primary/90 transition-all shadow-sm"
                >
                  Input Jadwal
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

      <!-- Footer -->
      <div
        class="p-4 border-t border-border-light flex items-center justify-between bg-gray-50/50 dark:bg-sidebar-light/50"
      >
        <span class="text-xs text-text-secondary">
          Menampilkan {{ pagination.from || 0 }}-{{ pagination.to || 0 }} dari
          {{ pagination.total }} mahasiswa
        </span>
        <div class="flex gap-1">
          <button
            @click="goToPage(pagination.current_page - 1)"
            :disabled="pagination.current_page <= 1"
            class="size-8 flex items-center justify-center rounded border border-border-light bg-white text-text-secondary hover:bg-gray-100 disabled:opacity-50"
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
            class="size-8 flex items-center justify-center rounded border border-border-light bg-white text-text-secondary hover:bg-gray-100 disabled:opacity-50"
          >
            <span class="material-symbols-outlined text-sm">chevron_right</span>
          </button>
        </div>
      </div>
    </div>

    <!-- Schedule Modal -->
    <div
      v-if="showScheduleModal"
      class="fixed inset-0 bg-black/50 flex items-center justify-center z-50 p-4"
    >
      <div
        class="bg-white dark:bg-surface-light rounded-xl shadow-2xl w-full max-w-lg"
      >
        <div class="p-6 border-b border-border-light">
          <h2 class="text-xl font-bold text-text-main">Atur Jadwal Seminar</h2>
          <p class="text-sm text-text-secondary mt-1">
            {{ selectedSeminar?.skripsi?.mahasiswa?.nama }}
          </p>
        </div>
        <form @submit.prevent="saveSeminarSchedule" class="p-6 space-y-4">
          <div>
            <label class="block text-sm font-medium text-text-main mb-1"
              >Tanggal</label
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
                >Jam Mulai</label
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
                >Ruangan</label
              >
              <input
                v-model="scheduleForm.ruangan"
                type="text"
                class="w-full px-3 py-2 border border-border-light rounded-lg focus:ring-2 focus:ring-primary/20 focus:border-primary"
                placeholder="Ruang A101"
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
const filterJenis = ref("");
const filterStatus = ref("");
const showScheduleModal = ref(false);
const selectedSeminar = ref(null);

const pagination = reactive({
  current_page: 1,
  last_page: 1,
  total: 0,
  from: 0,
  to: 0,
});

const scheduleForm = reactive({
  tanggal: "",
  waktu: "",
  ruangan: "",
});

const fetchSeminar = async () => {
  try {
    loading.value = true;
    const params = {
      page: pagination.current_page,
      jenis: filterJenis.value,
      status: filterStatus.value,
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
    console.error("Failed to fetch seminar:", error);
  } finally {
    loading.value = false;
  }
};

const goToPage = (page) => {
  if (page >= 1 && page <= pagination.last_page) {
    pagination.current_page = page;
    fetchSeminar();
  }
};

const openScheduleModal = (item) => {
  selectedSeminar.value = item;
  scheduleForm.tanggal = "";
  scheduleForm.waktu = "";
  scheduleForm.ruangan = "";
  showScheduleModal.value = true;
};

const saveSeminarSchedule = async () => {
  try {
    saving.value = true;
    await adminService.updateSeminar(selectedSeminar.value.id, {
      tanggal: scheduleForm.tanggal,
      waktu: scheduleForm.waktu,
      ruangan: scheduleForm.ruangan,
      status: "terjadwal",
    });
    showScheduleModal.value = false;
    fetchSeminar();
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

const viewDetail = (item) => {
  router.push(`/admin/seminar/${item.id}`);
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
    weekday: "long",
    day: "numeric",
    month: "long",
    year: "numeric",
  });
};

const getStatusClass = (status) => {
  const classes = {
    pending: "bg-yellow-50 text-yellow-600 border border-yellow-100",
    terjadwal: "bg-blue-50 text-blue-600 border border-blue-100",
    selesai: "bg-green-50 text-green-600 border border-green-100",
  };
  return classes[status] || "bg-gray-50 text-gray-600 border border-gray-100";
};

const getStatusDot = (status) => {
  const dots = {
    pending: "bg-yellow-600",
    terjadwal: "bg-blue-600",
    selesai: "bg-green-600",
  };
  return dots[status] || "bg-gray-600";
};

const getStatusLabel = (status) => {
  const labels = {
    pending: "Siap Sempro",
    terjadwal: "Terjadwal",
    selesai: "Selesai",
  };
  return labels[status] || status;
};

onMounted(() => {
  fetchSeminar();
});
</script>
