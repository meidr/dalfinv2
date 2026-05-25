<template>
  <div class="flex flex-col gap-8 animate-fade-in">
    <!-- Loading -->
    <div v-if="loading" class="flex flex-col gap-8">
      <div class="h-10 w-80 bg-gray-200 rounded-lg animate-pulse"></div>
      <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div
          v-for="i in 3"
          :key="i"
          class="bg-surface-light border border-border-light rounded-xl p-6 h-40 animate-pulse"
        >
          <div class="h-4 w-32 bg-gray-200 rounded mb-3"></div>
          <div class="h-8 w-16 bg-gray-200 rounded"></div>
        </div>
      </div>
    </div>

    <template v-else>
      <!-- Welcome Header -->
      <header
        class="flex flex-col md:flex-row md:items-end justify-between gap-4"
      >
        <div>
          <h1 class="text-3xl font-bold tracking-tight text-text-main">
            Selamat Datang, {{ dosenGreeting }}
          </h1>
          <p class="text-text-secondary text-base mt-2">
            Berikut adalah ringkasan aktivitas bimbingan Anda hari ini.
          </p>
        </div>
        <div class="flex gap-3">
          <router-link
            to="/dosen/jadwal"
            class="flex items-center gap-2 bg-surface-light border border-border-light px-4 py-2 rounded-lg text-sm font-medium hover:bg-sidebar-light transition-colors shadow-sm text-text-main"
          >
            <span class="material-symbols-outlined text-[20px]"
              >calendar_month</span
            >
            Lihat Jadwal
          </router-link>
        </div>
      </header>

      <!-- Stats Grid -->
      <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <!-- Card 1: Total Advisees -->
        <router-link
          to="/dosen/bimbingan"
          class="bg-surface-light p-6 rounded-xl border border-border-light shadow-sm flex flex-col justify-between h-40 group hover:border-primary/50 transition-colors cursor-pointer"
        >
          <div class="flex justify-between items-start">
            <div>
              <p class="text-text-secondary text-sm font-medium mb-1">
                Mahasiswa Bimbingan
              </p>
              <p class="text-3xl font-bold text-text-main">
                {{ stats.total_bimbingan }}
              </p>
            </div>
            <div
              class="size-10 rounded-full bg-blue-50 text-primary flex items-center justify-center group-hover:bg-primary group-hover:text-white transition-colors"
            >
              <span class="material-symbols-outlined">groups</span>
            </div>
          </div>
          <div
            class="w-full bg-gray-100 rounded-full h-1.5 mt-4 overflow-hidden"
          >
            <div
              class="bg-primary h-1.5 rounded-full transition-all"
              :style="{
                width: stats.kuota_bimbingan
                  ? `${Math.min(100, (stats.total_bimbingan / stats.kuota_bimbingan) * 100)}%`
                  : '0%',
              }"
            ></div>
          </div>
          <p class="text-xs text-text-secondary mt-2">
            Kuota: {{ stats.total_bimbingan }} /
            {{ stats.kuota_bimbingan || "-" }}
          </p>
        </router-link>

        <!-- Card 2: Sidang Terdekat -->
        <div
          class="bg-surface-light p-6 rounded-xl border-l-4 border-l-primary border-y border-r border-border-light shadow-sm flex flex-col justify-between h-40"
        >
          <div class="flex justify-between items-start">
            <div>
              <p class="text-text-secondary text-sm font-medium mb-1">
                Sidang Terdekat
              </p>
              <template v-if="upcomingSeminar">
                <p
                  class="text-xl font-bold text-text-main leading-tight mt-1 capitalize"
                >
                  {{ getJenisLabel(upcomingSeminar.jenis) }}
                </p>
                <p class="text-sm text-text-main font-medium mt-1">
                  {{ formatDate(upcomingSeminar.tanggal) }},
                  {{ formatTime(upcomingSeminar.waktu) }}
                </p>
              </template>
              <p v-else class="text-sm text-text-secondary mt-2 italic">
                Tidak ada jadwal mendatang
              </p>
            </div>
            <div
              class="size-10 rounded-full bg-orange-50 text-orange-500 flex items-center justify-center"
            >
              <span class="material-symbols-outlined">event_available</span>
            </div>
          </div>
          <div
            v-if="upcomingSeminar"
            class="flex items-center gap-2 mt-2 pt-2 border-t border-dashed border-gray-100"
          >
            <span class="material-symbols-outlined text-gray-400 text-sm"
              >person</span
            >
            <p class="text-sm text-text-secondary">
              Mahasiswa:
              <span class="font-medium text-text-main">{{
                upcomingSeminar.mahasiswa_nama
              }}</span>
            </p>
          </div>
        </div>

        <!-- Card 3: Pending Approvals -->
        <router-link
          to="/dosen/bimbingan"
          class="bg-surface-light p-6 rounded-xl border border-border-light shadow-sm flex flex-col justify-between h-40 group hover:border-red-200 transition-colors cursor-pointer"
        >
          <div class="flex justify-between items-start">
            <div>
              <p class="text-text-secondary text-sm font-medium mb-1">
                Menunggu Persetujuan
              </p>
              <p class="text-3xl font-bold text-text-main">
                {{ stats.pending_approvals }}
              </p>
            </div>
            <div
              class="size-10 rounded-full bg-red-50 text-red-500 flex items-center justify-center group-hover:bg-red-500 group-hover:text-white transition-colors"
            >
              <span class="material-symbols-outlined">pending_actions</span>
            </div>
          </div>
          <div
            v-if="
              stats.pending_by_type &&
              Object.keys(stats.pending_by_type).length > 0
            "
            class="flex flex-wrap gap-2 mt-4"
          >
            <span
              v-for="(count, type) in stats.pending_by_type"
              :key="type"
              class="text-xs px-2 py-1 rounded font-medium border"
              :class="getPendingTypeClass(type)"
            >
              {{ count }} {{ getPendingTypeLabel(type) }}
            </span>
          </div>
          <p
            v-else-if="stats.pending_approvals === 0"
            class="text-xs text-green-600 mt-4 font-medium"
          >
            ✓ Semua sudah ditinjau
          </p>
        </router-link>
      </div>

      <!-- Ujian Skripsi Requests -->
      <section
        v-if="ujianRequests.length > 0"
        class="bg-surface-light rounded-xl shadow-sm border border-border-light overflow-hidden"
      >
        <div
          class="p-5 border-b border-border-light flex items-center justify-between"
        >
          <div class="flex items-center gap-3">
            <div
              class="p-2 bg-yellow-50 dark:bg-yellow-900/20 rounded-lg text-yellow-600"
            >
              <span class="material-symbols-outlined">school</span>
            </div>
            <div>
              <h3 class="text-lg font-bold text-text-main">
                Pengajuan Ujian Skripsi
              </h3>
              <p class="text-sm text-text-secondary">
                {{ ujianRequests.length }} mahasiswa mengajukan ujian
              </p>
            </div>
          </div>
        </div>
        <div class="divide-y divide-border-light">
          <div
            v-for="req in ujianRequests"
            :key="req.id"
            class="p-5 flex flex-col md:flex-row md:items-center gap-4"
          >
            <div class="flex items-center gap-3 flex-1 min-w-0">
              <div
                class="size-10 rounded-full flex items-center justify-center text-white text-sm font-bold shrink-0"
                :style="{
                  backgroundColor: getAvatarColor(req.mahasiswa?.nama || ''),
                }"
              >
                {{ getInitials(req.mahasiswa?.nama || "") }}
              </div>
              <div class="min-w-0">
                <p class="text-sm font-bold text-text-main truncate">
                  {{ req.mahasiswa?.nama || "-" }}
                </p>
                <p class="text-xs text-text-secondary">
                  {{ req.mahasiswa?.nim || "-" }} ·
                  {{ req.judul || "Tanpa judul" }}
                </p>
              </div>
            </div>
            <div class="flex items-center gap-2 shrink-0">
              <button
                @click="approveUjian(req.id)"
                :disabled="processingUjian === req.id"
                class="inline-flex items-center gap-1.5 px-4 py-2 rounded-lg bg-green-600 hover:bg-green-700 text-white text-sm font-bold transition-colors disabled:opacity-50"
              >
                <span class="material-symbols-outlined text-[18px]">check</span>
                Setujui
              </button>
              <button
                @click="openRejectModal(req)"
                :disabled="processingUjian === req.id"
                class="inline-flex items-center gap-1.5 px-4 py-2 rounded-lg bg-red-50 hover:bg-red-100 text-red-600 text-sm font-bold transition-colors border border-red-200 disabled:opacity-50"
              >
                <span class="material-symbols-outlined text-[18px]">close</span>
                Tolak
              </button>
            </div>
          </div>
        </div>
      </section>

      <!-- Ujian Reject Modal -->
      <Transition name="modal-fade">
        <div
          v-if="rejectModal.show"
          class="fixed inset-0 z-50 flex items-center justify-center p-4"
        >
          <div
            class="absolute inset-0 bg-black/50 backdrop-blur-sm"
            @click="rejectModal.show = false"
          ></div>
          <div
            class="relative bg-surface-light rounded-xl shadow-xl border border-border-light w-full max-w-md p-6 flex flex-col gap-5"
          >
            <div class="flex items-center gap-3">
              <div class="p-2 bg-red-50 rounded-lg text-red-500">
                <span class="material-symbols-outlined">block</span>
              </div>
              <div>
                <h3 class="text-lg font-bold text-text-main">
                  Tolak Pengajuan
                </h3>
                <p class="text-xs text-text-secondary">
                  {{ rejectModal.mahasiswaNama }}
                </p>
              </div>
            </div>
            <div>
              <label class="block text-sm font-medium text-text-main mb-2">
                Alasan Penolakan <span class="text-red-500">*</span>
              </label>
              <textarea
                v-model="rejectModal.alasan"
                rows="3"
                class="w-full border border-border-light rounded-lg px-4 py-2.5 text-sm text-text-main bg-surface-light focus:ring-2 focus:ring-primary/20 focus:border-primary outline-none resize-none"
                placeholder="Jelaskan alasan penolakan..."
              ></textarea>
            </div>
            <div class="flex justify-end gap-3">
              <button
                @click="rejectModal.show = false"
                class="px-5 py-2.5 rounded-lg text-text-secondary font-bold hover:bg-sidebar-light transition-colors text-sm"
              >
                Batal
              </button>
              <button
                @click="confirmRejectUjian"
                :disabled="
                  !rejectModal.alasan.trim() ||
                  processingUjian === rejectModal.skripsiId
                "
                class="px-5 py-2.5 rounded-lg bg-red-600 text-white font-bold hover:bg-red-700 transition-colors shadow-sm text-sm flex items-center gap-2 disabled:opacity-50"
              >
                <span class="material-symbols-outlined text-[18px]">block</span>
                Tolak Pengajuan
              </button>
            </div>
          </div>
        </div>
      </Transition>

      <!-- Ujian Toast -->
      <Transition name="toast-slide">
        <div
          v-if="ujianToast.show"
          class="fixed bottom-6 right-6 z-50 flex items-center gap-3 px-5 py-3.5 rounded-xl shadow-lg text-white text-sm font-bold"
          :class="
            ujianToast.type === 'success'
              ? 'bg-green-600 shadow-green-600/30'
              : 'bg-red-600 shadow-red-600/30'
          "
        >
          <span class="material-symbols-outlined text-[20px]">{{
            ujianToast.type === "success" ? "check_circle" : "error"
          }}</span>
          {{ ujianToast.message }}
        </div>
      </Transition>

      <!-- Content Split Layout -->
      <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 h-full">
        <!-- Left: Recent Activity Table -->
        <div class="lg:col-span-2 flex flex-col gap-4">
          <div class="flex items-center justify-between">
            <h2 class="text-text-main text-[20px] font-bold leading-tight">
              Aktivitas Mahasiswa Terbaru
            </h2>
            <router-link
              to="/dosen/bimbingan"
              class="text-primary text-sm font-medium hover:underline"
            >
              Lihat Semua
            </router-link>
          </div>
          <div
            class="bg-surface-light border border-border-light rounded-xl overflow-hidden shadow-sm"
          >
            <DataTableScroll>
              <table class="w-full text-left border-collapse">
                <thead>
                  <tr
                    class="bg-sidebar-light border-b border-border-light text-text-secondary"
                  >
                    <th
                      class="px-6 py-4 text-xs font-semibold uppercase tracking-wider"
                    >
                      Mahasiswa
                    </th>
                    <th
                      class="px-6 py-4 text-xs font-semibold uppercase tracking-wider"
                    >
                      Aktivitas
                    </th>
                    <th
                      class="px-6 py-4 text-xs font-semibold uppercase tracking-wider"
                    >
                      Status
                    </th>
                    <th
                      class="px-6 py-4 text-xs font-semibold uppercase tracking-wider w-32"
                    >
                      Waktu
                    </th>
                  </tr>
                </thead>
                <tbody class="divide-y divide-border-light">
                  <tr v-if="recentActivities.length === 0">
                    <td
                      colspan="4"
                      class="px-6 py-12 text-center text-text-secondary"
                    >
                      <span
                        class="material-symbols-outlined text-4xl mb-2 block opacity-40"
                        >inbox</span
                      >
                      <p class="text-sm">Belum ada aktivitas terbaru</p>
                    </td>
                  </tr>
                  <tr
                    v-for="activity in recentActivities"
                    :key="activity.id"
                    class="group hover:bg-sidebar-light transition-colors cursor-pointer"
                    @click="
                      activity.skripsi_id &&
                      $router.push({
                        name: 'DosenBimbinganProgress',
                        params: { id: activity.skripsi_id },
                      })
                    "
                  >
                    <td class="px-6 py-4">
                      <div class="flex items-center gap-3">
                        <div
                          class="size-8 rounded-full flex items-center justify-center text-white text-xs font-bold shrink-0"
                          :style="{
                            backgroundColor: getAvatarColor(
                              activity.mahasiswa_nama,
                            ),
                          }"
                        >
                          {{ getInitials(activity.mahasiswa_nama) }}
                        </div>
                        <div>
                          <p class="text-sm font-medium text-text-main">
                            {{ activity.mahasiswa_nama }}
                          </p>
                          <p class="text-xs text-text-secondary">
                            {{ activity.mahasiswa_nim }}
                          </p>
                        </div>
                      </div>
                    </td>
                    <td class="px-6 py-4">
                      <p class="text-sm text-text-main font-medium">
                        {{ activity.aktivitas }}
                      </p>
                      <p
                        v-if="activity.file_name"
                        class="text-xs text-text-secondary mt-0.5"
                      >
                        {{ activity.file_name }}
                      </p>
                    </td>
                    <td class="px-6 py-4">
                      <span
                        class="inline-flex items-center gap-1.5 py-1 px-2.5 rounded-full text-xs font-medium border"
                        :class="getActivityStatusClass(activity.status)"
                      >
                        <span
                          class="size-1.5 rounded-full"
                          :class="getActivityStatusDot(activity.status)"
                        ></span>
                        {{ getActivityStatusLabel(activity.status) }}
                      </span>
                    </td>
                    <td class="px-6 py-4">
                      <span class="text-xs text-text-secondary">{{
                        formatTimeAgo(activity.created_at)
                      }}</span>
                    </td>
                  </tr>
                </tbody>
              </table>
            </DataTableScroll>
          </div>
        </div>

        <!-- Right: Upcoming Schedule -->
        <div class="flex flex-col gap-4">
          <div class="flex items-center justify-between">
            <h2 class="text-text-main text-[20px] font-bold leading-tight">
              Jadwal Minggu Ini
            </h2>
          </div>
          <div
            class="bg-surface-light border border-border-light rounded-xl p-5 shadow-sm h-full"
          >
            <!-- Empty state -->
            <div
              v-if="jadwalMingguIni.length === 0"
              class="flex flex-col items-center justify-center py-12 text-text-secondary"
            >
              <span class="material-symbols-outlined text-4xl mb-2 opacity-40"
                >event_busy</span
              >
              <p class="text-sm">Tidak ada jadwal minggu ini</p>
            </div>

            <!-- Timeline -->
            <div
              v-else
              class="relative pl-4 border-l border-gray-200 space-y-8"
            >
              <div
                v-for="(item, idx) in jadwalMingguIni"
                :key="item.id"
                class="relative"
              >
                <span
                  class="absolute -left-[21px] top-1 size-3 rounded-full border-2 border-white"
                  :class="
                    idx === 0
                      ? 'bg-primary ring-2 ring-blue-100'
                      : 'bg-gray-300'
                  "
                ></span>
                <div class="flex flex-col gap-1">
                  <span
                    class="text-xs font-bold w-fit px-2 py-0.5 rounded uppercase tracking-wider"
                    :class="getJenisColorClass(item.jenis)"
                  >
                    {{ formatDateShort(item.tanggal) }}
                  </span>
                  <p class="text-sm font-bold text-text-main mt-1">
                    {{ getJenisLabel(item.jenis) }}:
                    {{ item.mahasiswa_nama }}
                  </p>
                  <div
                    class="flex items-center gap-1 text-text-secondary text-xs"
                  >
                    <span class="material-symbols-outlined text-[16px]"
                      >schedule</span
                    >
                    {{ formatTime(item.waktu) }}
                  </div>
                  <div
                    v-if="item.ruangan"
                    class="flex items-center gap-1 text-text-secondary text-xs"
                  >
                    <span class="material-symbols-outlined text-[16px]"
                      >location_on</span
                    >
                    {{ item.ruangan }}
                  </div>
                </div>
              </div>
            </div>
            <div class="mt-8 pt-4 border-t border-border-light">
              <router-link
                to="/dosen/jadwal"
                class="w-full py-2 text-center text-primary text-sm font-medium hover:bg-blue-50 rounded-lg transition-colors block"
              >
                Lihat Jadwal Lengkap
              </router-link>
            </div>
          </div>
        </div>
      </div>
    </template>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from "vue";
import dosenService from "../../services/dosenService";

const loading = ref(true);
const dashboardData = ref(null);

const stats = computed(
  () =>
    dashboardData.value?.stats || {
      total_bimbingan: 0,
      active_count: 0,
      inactive_count: 0,
      kuota_bimbingan: 0,
      pending_approvals: 0,
      pending_by_type: {},
    },
);

const upcomingSeminar = computed(
  () => dashboardData.value?.upcoming_seminar || null,
);
const recentActivities = computed(
  () => dashboardData.value?.recent_activities || [],
);
const jadwalMingguIni = computed(
  () => dashboardData.value?.jadwal_minggu_ini || [],
);

// Ujian request state
const ujianRequests = ref([]);
const processingUjian = ref(null);
const ujianToast = ref({ show: false, message: "", type: "success" });
const rejectModal = ref({
  show: false,
  skripsiId: null,
  mahasiswaNama: "",
  alasan: "",
});

const fetchUjianRequests = async () => {
  try {
    const res = await dosenService.getUjianRequests();
    if (res.success) {
      ujianRequests.value = res.data || [];
    }
  } catch (err) {
    console.error("Failed to fetch ujian requests:", err);
  }
};

const approveUjian = async (skripsiId) => {
  processingUjian.value = skripsiId;
  try {
    const res = await dosenService.respondUjianRequest(skripsiId, "approve");
    if (res.success) {
      ujianRequests.value = ujianRequests.value.filter(
        (r) => r.id !== skripsiId,
      );
      showToast("Pengajuan ujian berhasil disetujui!", "success");
    }
  } catch (err) {
    console.error("Failed to approve ujian:", err);
    showToast(
      err.response?.data?.message || "Gagal menyetujui pengajuan.",
      "error",
    );
  } finally {
    processingUjian.value = null;
  }
};

const openRejectModal = (req) => {
  rejectModal.value = {
    show: true,
    skripsiId: req.id,
    mahasiswaNama: req.mahasiswa?.nama || "-",
    alasan: "",
  };
};

const confirmRejectUjian = async () => {
  const { skripsiId, alasan } = rejectModal.value;
  processingUjian.value = skripsiId;
  try {
    const res = await dosenService.respondUjianRequest(
      skripsiId,
      "reject",
      alasan,
    );
    if (res.success) {
      ujianRequests.value = ujianRequests.value.filter(
        (r) => r.id !== skripsiId,
      );
      rejectModal.value.show = false;
      showToast("Pengajuan ujian ditolak.", "success");
    }
  } catch (err) {
    console.error("Failed to reject ujian:", err);
    showToast(
      err.response?.data?.message || "Gagal menolak pengajuan.",
      "error",
    );
  } finally {
    processingUjian.value = null;
  }
};

const showToast = (message, type = "success") => {
  ujianToast.value = { show: true, message, type };
  setTimeout(() => {
    ujianToast.value.show = false;
  }, 3000);
};

const dosenGreeting = computed(() => {
  const dosen = dashboardData.value?.dosen;
  if (!dosen) return "";
  const prefix = dosen.jenis_kelamin === "P" ? "Ibu" : "Bapak";
  const name = dosen.full_name || dosen.nama || "";
  // Use short name (first + last or just first)
  const parts = name.split(" ");
  const shortName =
    parts.length > 1 ? `${parts[0]} ${parts[parts.length - 1]}` : parts[0];
  return `${prefix} ${shortName}`;
});

const fetchDashboard = async () => {
  try {
    loading.value = true;
    const response = await dosenService.getDashboard();
    if (response.success) {
      dashboardData.value = response.data;
    }
  } catch (error) {
    console.error("Failed to fetch dashboard:", error);
  } finally {
    loading.value = false;
  }
};

// Helpers
const getInitials = (name) => {
  if (!name) return "?";
  return name
    .split(" ")
    .map((n) => n[0])
    .slice(0, 2)
    .join("")
    .toUpperCase();
};

const getAvatarColor = (name) => {
  if (!name) return "#6366f1";
  const colors = [
    "#3b82f6",
    "#8b5cf6",
    "#ec4899",
    "#f59e0b",
    "#10b981",
    "#6366f1",
    "#ef4444",
    "#14b8a6",
  ];
  let hash = 0;
  for (let i = 0; i < name.length; i++)
    hash = name.charCodeAt(i) + ((hash << 5) - hash);
  return colors[Math.abs(hash) % colors.length];
};

const getJenisLabel = (jenis) => {
  const map = {
    sempro: "Seminar Proposal",
    semhas: "Seminar Hasil",
    sidang: "Sidang Skripsi",
  };
  return map[jenis] || jenis || "Seminar";
};

const getJenisColorClass = (jenis) => {
  const map = {
    sempro: "text-blue-700 bg-blue-100",
    semhas: "text-cyan-700 bg-cyan-100",
    sidang: "text-primary bg-blue-50",
  };
  return map[jenis] || "text-gray-500 bg-gray-100";
};

const getActivityStatusLabel = (status) => {
  const map = {
    pending: "Menunggu Review",
    approved: "Disetujui",
    revision: "Perlu Revisi",
    rejected: "Ditolak",
  };
  return map[status] || status || "-";
};

const getActivityStatusClass = (status) => {
  const map = {
    pending: "bg-amber-100 text-amber-800 border-amber-300",
    approved: "bg-emerald-100 text-emerald-800 border-emerald-300",
    revision: "bg-rose-100 text-rose-700 border-rose-300",
    rejected: "bg-red-100 text-red-700 border-red-300",
  };
  return map[status] || "bg-gray-100 text-gray-700 border-gray-300";
};

const getActivityStatusDot = (status) => {
  const map = {
    pending: "bg-amber-500",
    approved: "bg-emerald-500",
    revision: "bg-rose-500",
    rejected: "bg-red-500",
  };
  return map[status] || "bg-gray-500";
};

const getPendingTypeLabel = (type) => {
  const map = {
    bab: "Bab",
    proposal: "Proposal",
    revisi: "Revisi",
    lainnya: "Lainnya",
  };
  return map[type] || type || "Lainnya";
};

const getPendingTypeClass = (type) => {
  const map = {
    bab: "bg-blue-50 text-blue-600 border-blue-200",
    proposal: "bg-red-50 text-red-600 border-red-200",
    revisi: "bg-orange-50 text-orange-600 border-orange-200",
    lainnya: "bg-gray-50 text-gray-600 border-gray-200",
  };
  return map[type] || "bg-gray-50 text-gray-600 border-gray-200";
};

const formatDate = (date) => {
  if (!date) return "-";
  return new Date(date).toLocaleDateString("id-ID", {
    weekday: "long",
    day: "numeric",
    month: "long",
  });
};

const formatDateShort = (date) => {
  if (!date) return "-";
  return new Date(date).toLocaleDateString("id-ID", {
    weekday: "long",
    day: "numeric",
    month: "short",
  });
};

const formatTime = (time) => {
  if (!time) return "-";
  return time.substring(0, 5) + " WIB";
};

const formatTimeAgo = (dateStr) => {
  if (!dateStr) return "-";
  const diff = Date.now() - new Date(dateStr).getTime();
  const mins = Math.floor(diff / 60000);
  if (mins < 1) return "Baru saja";
  if (mins < 60) return `${mins}m lalu`;
  const hours = Math.floor(mins / 60);
  if (hours < 24) return `${hours}j lalu`;
  const days = Math.floor(hours / 24);
  if (days < 7) return `${days}h lalu`;
  return new Date(dateStr).toLocaleDateString("id-ID", {
    day: "numeric",
    month: "short",
  });
};

onMounted(() => {
  fetchDashboard();
  fetchUjianRequests();
});
</script>

<style scoped>
.animate-fade-in {
  animation: fadeIn 0.3s ease-in-out;
}
@keyframes fadeIn {
  from {
    opacity: 0;
    transform: translateY(5px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}
.modal-fade-enter-active,
.modal-fade-leave-active {
  transition: opacity 0.2s ease;
}
.modal-fade-enter-from,
.modal-fade-leave-to {
  opacity: 0;
}
.toast-slide-enter-active {
  transition: all 0.3s ease-out;
}
.toast-slide-leave-active {
  transition: all 0.2s ease-in;
}
.toast-slide-enter-from,
.toast-slide-leave-to {
  transform: translateY(20px);
  opacity: 0;
}
</style>
