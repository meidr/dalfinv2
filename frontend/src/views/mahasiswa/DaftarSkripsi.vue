<template>
  <div class="flex flex-col gap-6">
    <!-- Breadcrumbs -->
    <div class="flex flex-wrap items-center gap-2 text-sm">
      <router-link
        to="/mahasiswa/dashboard"
        class="text-text-secondary hover:text-primary font-medium"
        >Home</router-link
      >
      <span class="material-symbols-outlined text-text-secondary text-sm"
        >chevron_right</span
      >
      <span class="text-text-main font-bold">Skripsi Saya</span>
    </div>

    <!-- Page Heading -->
    <div
      class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4"
    >
      <div>
        <h2 class="text-3xl font-bold tracking-tight text-text-main mb-2">
          Skripsi Saya
        </h2>
        <p class="text-text-secondary text-base">
          Kelola progres skripsi dan lihat riwayat judul Anda.
        </p>
      </div>
      <button
        v-if="!loading && !activeSkripsi"
        @click="showPengajuanModal = true"
        class="inline-flex items-center gap-2 bg-primary hover:bg-blue-600 text-white font-bold px-5 py-2.5 rounded-lg transition-all shadow-md shadow-primary/20 text-sm"
      >
        <span class="material-symbols-outlined text-[20px]">add</span>
        Ajukan Skripsi
      </button>
    </div>

    <!-- Loading State -->
    <div v-if="loading" class="flex flex-col gap-6 animate-pulse">
      <div class="h-6 w-40 bg-gray-200 dark:bg-gray-700 rounded"></div>
      <div class="h-64 bg-gray-200 dark:bg-gray-700 rounded-xl"></div>
      <div class="h-6 w-56 bg-gray-200 dark:bg-gray-700 rounded"></div>
      <div class="h-32 bg-gray-200 dark:bg-gray-700 rounded-xl"></div>
    </div>

    <template v-else>
      <!-- Active Thesis Card -->
      <div class="w-full" v-if="activeSkripsi">
        <h3 class="text-lg font-bold text-text-main mb-4">Skripsi Aktif</h3>
        <div
          class="bg-surface-light rounded-xl shadow-sm border border-border-light overflow-hidden group hover:border-primary/50 transition-all duration-300"
        >
          <!-- Status Strip -->
          <div class="h-1.5 bg-primary w-full"></div>
          <div class="p-6 md:p-8">
            <div class="flex flex-col gap-6">
              <!-- Header -->
              <div class="flex justify-between items-start">
                <span
                  :class="getStatusBadgeClass(activeSkripsi.status)"
                  class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold border"
                >
                  <span
                    class="size-2 rounded-full animate-pulse"
                    :class="
                      activeSkripsi.status === 'ditolak'
                        ? 'bg-red-500'
                        : 'bg-primary'
                    "
                  ></span>
                  {{ getStatusLabel(activeSkripsi.status) }}
                </span>
              </div>

              <!-- Title -->
              <div>
                <h3
                  class="text-xl md:text-2xl font-bold text-text-main leading-tight mb-3 group-hover:text-primary transition-colors"
                >
                  {{ activeSkripsi.judul }}
                </h3>
                <p
                  class="text-sm text-text-secondary"
                  v-if="getPembimbingNames(activeSkripsi)"
                >
                  Pembimbing:
                  <span class="font-medium text-text-main dark:text-gray-200">{{
                    getPembimbingNames(activeSkripsi)
                  }}</span>
                </p>
              </div>

              <!-- Metadata -->
              <div
                class="grid grid-cols-1 sm:grid-cols-2 gap-4 py-4 border-t border-b border-border-light"
              >
                <div class="flex items-center gap-3">
                  <div
                    class="size-10 rounded-lg bg-sidebar-light flex items-center justify-center text-text-secondary"
                  >
                    <span class="material-symbols-outlined"
                      >calendar_today</span
                    >
                  </div>
                  <div class="flex flex-col">
                    <span class="text-xs text-text-secondary font-medium"
                      >Tanggal Daftar</span
                    >
                    <span class="text-sm font-semibold text-text-main">{{
                      formatDate(activeSkripsi.tanggal_daftar)
                    }}</span>
                  </div>
                </div>
                <div
                  class="flex items-center gap-3"
                  v-if="activeSkripsi.semester_daftar"
                >
                  <div
                    class="size-10 rounded-lg bg-sidebar-light flex items-center justify-center text-text-secondary"
                  >
                    <span class="material-symbols-outlined">school</span>
                  </div>
                  <div class="flex flex-col">
                    <span class="text-xs text-text-secondary font-medium"
                      >Semester</span
                    >
                    <span class="text-sm font-semibold text-text-main">{{
                      activeSkripsi.semester_daftar
                    }}</span>
                  </div>
                </div>
                <div
                  class="flex items-center gap-3"
                  v-if="activeSkripsi.tahun_akademik"
                >
                  <div
                    class="size-10 rounded-lg bg-sidebar-light flex items-center justify-center text-text-secondary"
                  >
                    <span class="material-symbols-outlined">date_range</span>
                  </div>
                  <div class="flex flex-col">
                    <span class="text-xs text-text-secondary font-medium"
                      >Tahun Akademik</span
                    >
                    <span class="text-sm font-semibold text-text-main">{{
                      activeSkripsi.tahun_akademik?.name || "-"
                    }}</span>
                  </div>
                </div>
              </div>

              <!-- Progress -->
              <div class="flex flex-col gap-3">
                <div class="flex justify-between items-end">
                  <span class="text-sm font-medium text-text-main"
                    >Progres Keseluruhan</span
                  >
                  <span class="text-sm font-bold text-primary"
                    >{{ activeSkripsi.progress_percentage ?? 0 }}%</span
                  >
                </div>
                <div
                  class="w-full bg-sidebar-light rounded-full h-2.5 overflow-hidden"
                >
                  <div
                    class="bg-primary h-2.5 rounded-full transition-all duration-500"
                    :style="{
                      width: (activeSkripsi.progress_percentage ?? 0) + '%',
                    }"
                  ></div>
                </div>
                <p class="text-xs text-text-secondary mt-1">
                  Saat ini di Tahap:
                  {{ getStatusLabel(activeSkripsi.status) }}
                </p>
              </div>

              <!-- Action -->
              <div class="flex justify-end pt-2">
                <router-link
                  to="/mahasiswa/skripsi/detail"
                  class="w-full sm:w-auto bg-primary hover:bg-blue-600 text-white px-6 py-2.5 rounded-lg text-sm font-bold tracking-wide transition-all shadow-sm flex items-center justify-center gap-2 group/btn"
                >
                  <span>Lihat Detail</span>
                  <span
                    class="material-symbols-outlined text-[18px] group-hover/btn:translate-x-1 transition-transform"
                    >arrow_forward</span
                  >
                </router-link>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- No Active Skripsi -->
      <div
        v-else
        class="bg-surface-light rounded-xl shadow-sm border border-border-light p-12 flex flex-col items-center justify-center gap-4 text-center"
      >
        <span
          class="material-symbols-outlined text-6xl text-text-secondary opacity-40"
          >menu_book</span
        >
        <h3 class="text-xl font-bold text-text-main">
          Belum Ada Skripsi Aktif
        </h3>
        <p class="text-text-secondary max-w-md">
          Anda belum memiliki skripsi aktif. Silakan ajukan judul skripsi baru
          untuk memulai proses skripsi.
        </p>
        <button
          @click="showPengajuanModal = true"
          class="mt-2 inline-flex items-center gap-2 bg-primary hover:bg-blue-600 text-white font-bold px-6 py-3 rounded-lg transition-all shadow-md shadow-primary/20"
        >
          <span class="material-symbols-outlined text-[20px]">add</span>
          Ajukan Skripsi
        </button>
      </div>

      <!-- History / Old Thesis List -->
      <div class="mt-4" v-if="inactiveSkripsi.length > 0">
        <h3 class="text-lg font-bold text-text-main mb-4">
          Riwayat Judul Skripsi
        </h3>
        <div class="grid grid-cols-1 gap-4">
          <div
            v-for="item in inactiveSkripsi"
            :key="item.id"
            class="bg-surface-light p-6 rounded-xl border border-border-light opacity-75 hover:opacity-100 transition-opacity"
          >
            <div class="flex justify-between items-start mb-2">
              <span
                :class="getStatusBadgeClass(item.status)"
                class="inline-flex items-center px-2.5 py-0.5 rounded text-xs font-bold border"
              >
                {{ getStatusLabel(item.status) }}
              </span>
              <span
                class="text-xs text-text-secondary"
                v-if="item.semester_daftar"
                >{{ item.semester_daftar }}</span
              >
            </div>
            <h4 class="text-base font-bold text-text-main mb-2">
              {{ item.judul }}
            </h4>
            <p
              class="text-sm text-text-secondary mb-3"
              v-if="item.catatan_admin"
            >
              Alasan: {{ item.catatan_admin }}
            </p>
            <!-- Progress bar mini -->
            <div class="mb-4">
              <div class="flex justify-between items-center mb-1">
                <span class="text-xs text-text-secondary font-medium"
                  >Progress</span
                >
                <span class="text-xs font-bold text-primary"
                  >{{ item.progress_percentage ?? 0 }}%</span
                >
              </div>
              <div
                class="w-full bg-sidebar-light rounded-full h-1.5 overflow-hidden"
              >
                <div
                  class="bg-primary h-1.5 rounded-full transition-all"
                  :style="{ width: (item.progress_percentage ?? 0) + '%' }"
                ></div>
              </div>
            </div>
            <router-link
              :to="`/mahasiswa/skripsi/history/${item.id}`"
              class="inline-flex items-center gap-1.5 text-sm font-bold text-primary hover:text-blue-600 transition-colors"
            >
              <span class="material-symbols-outlined text-[18px]"
                >visibility</span
              >
              Lihat Detail
            </router-link>
          </div>
        </div>
      </div>
    </template>

    <!-- Pengajuan Skripsi Modal -->
    <Transition name="modal-fade">
      <div
        v-if="showPengajuanModal"
        class="fixed inset-0 z-50 flex items-center justify-center p-4"
      >
        <div
          class="absolute inset-0 bg-black/50 backdrop-blur-sm"
          @click="closePengajuanModal"
        ></div>
        <div
          class="relative bg-surface-light rounded-xl shadow-xl border border-border-light w-full max-w-lg"
        >
          <!-- Header -->
          <div
            class="p-6 border-b border-border-light flex justify-between items-center"
          >
            <div class="flex items-center gap-3">
              <div class="p-2 bg-primary/10 rounded-lg text-primary">
                <span class="material-symbols-outlined">edit_document</span>
              </div>
              <div>
                <h3 class="text-lg font-bold text-text-main">
                  Pengajuan Skripsi
                </h3>
                <p class="text-xs text-text-secondary">
                  Isi form di bawah untuk mengajukan judul skripsi baru
                </p>
              </div>
            </div>
            <button
              @click="closePengajuanModal"
              class="p-1 hover:bg-gray-100 dark:hover:bg-gray-800 rounded-lg transition-colors"
            >
              <span class="material-symbols-outlined text-text-secondary"
                >close</span
              >
            </button>
          </div>

          <!-- Form -->
          <form
            @submit.prevent="submitPengajuan"
            class="p-6 flex flex-col gap-5"
          >
            <!-- Error -->
            <div
              v-if="pengajuanError"
              class="flex gap-2 bg-red-50 p-3 rounded-lg border border-red-100 text-sm text-red-700"
            >
              <span class="material-symbols-outlined text-red-500 text-[18px]"
                >error</span
              >
              {{ pengajuanError }}
            </div>

            <!-- Judul -->
            <div class="flex flex-col gap-2">
              <label class="text-sm font-bold text-text-main"
                >Judul Skripsi
                <span class="text-red-500">*</span>
              </label>
              <input
                v-model="pengajuanForm.judul"
                type="text"
                required
                maxlength="500"
                placeholder="Masukkan judul skripsi Anda"
                class="px-4 py-2.5 rounded-lg border border-border-light bg-background-light focus:ring-2 focus:ring-primary/20 focus:border-primary outline-none transition-all text-sm"
              />
              <p class="text-xs text-text-secondary">
                {{ pengajuanForm.judul.length }}/500 karakter
              </p>
            </div>

            <!-- Tahun Akademik -->
            <div class="flex flex-col gap-2">
              <label class="text-sm font-bold text-text-main"
                >Tahun Akademik
                <span class="font-normal text-text-secondary">(opsional)</span>
              </label>
              <select
                v-model="pengajuanForm.th_akademik_id"
                class="px-4 py-2.5 rounded-lg border border-border-light bg-background-light focus:ring-2 focus:ring-primary/20 focus:border-primary outline-none transition-all text-sm"
              >
                <option value="">Pilih Tahun Akademik</option>
                <option v-for="t in tahunList" :key="t.id" :value="t.id">
                  {{ t.name }}
                </option>
              </select>
            </div>

            <!-- Abstrak -->
            <div class="flex flex-col gap-2">
              <label class="text-sm font-bold text-text-main"
                >Abstrak
                <span class="font-normal text-text-secondary">(opsional)</span>
              </label>
              <textarea
                v-model="pengajuanForm.abstrak"
                rows="4"
                placeholder="Tuliskan deskripsi singkat tentang topik skripsi Anda"
                class="px-4 py-2.5 rounded-lg border border-border-light bg-background-light focus:ring-2 focus:ring-primary/20 focus:border-primary outline-none transition-all text-sm resize-none"
              ></textarea>
            </div>

            <!-- Kata Kunci -->
            <div class="flex flex-col gap-2">
              <label class="text-sm font-bold text-text-main"
                >Kata Kunci
                <span class="font-normal text-text-secondary">(opsional)</span>
              </label>
              <input
                v-model="pengajuanForm.kata_kunci"
                type="text"
                placeholder="Contoh: Machine Learning, Web, IoT"
                class="px-4 py-2.5 rounded-lg border border-border-light bg-background-light focus:ring-2 focus:ring-primary/20 focus:border-primary outline-none transition-all text-sm"
              />
              <p class="text-xs text-text-secondary">Pisahkan dengan koma</p>
            </div>

            <!-- Actions -->
            <div
              class="flex justify-end gap-3 pt-4 border-t border-border-light"
            >
              <button
                type="button"
                @click="closePengajuanModal"
                class="px-5 py-2.5 rounded-lg text-text-secondary font-bold hover:bg-sidebar-light transition-colors text-sm"
              >
                Batal
              </button>
              <button
                type="submit"
                :disabled="submitting || !pengajuanForm.judul.trim()"
                class="px-5 py-2.5 rounded-lg bg-primary text-white font-bold hover:bg-blue-600 transition-colors shadow-sm text-sm flex items-center gap-2 disabled:opacity-50"
              >
                <span
                  v-if="submitting"
                  class="animate-spin rounded-full h-4 w-4 border-b-2 border-white"
                ></span>
                <span v-else class="material-symbols-outlined text-[18px]"
                  >send</span
                >
                {{ submitting ? "Mengirim..." : "Ajukan Skripsi" }}
              </button>
            </div>
          </form>
        </div>
      </div>
    </Transition>

    <!-- Success Toast -->
    <Transition name="toast-slide">
      <div
        v-if="showSuccessToast"
        class="fixed bottom-6 right-6 z-50 flex items-center gap-3 bg-green-600 text-white px-5 py-3.5 rounded-xl shadow-lg shadow-green-600/30"
      >
        <span class="material-symbols-outlined text-[20px]">check_circle</span>
        <span class="text-sm font-bold">{{ successMessage }}</span>
      </div>
    </Transition>
  </div>
</template>

<script setup>
import { ref, reactive, computed, onMounted } from "vue";
import { mahasiswaService } from "../../services/mahasiswaService";
import { useRouter } from "vue-router";

const router = useRouter();
const loading = ref(true);
const skripsiList = ref([]);

// Pengajuan modal state
const showPengajuanModal = ref(false);
const submitting = ref(false);
const pengajuanError = ref("");
const pengajuanForm = reactive({
  judul: "",
  abstrak: "",
  kata_kunci: "",
  th_akademik_id: "",
});

// Tahun list
const tahunList = ref([]);

// Toast state
const showSuccessToast = ref(false);
const successMessage = ref("");

const statusMap = {
  draft: "Draft",
  pengajuan: "Pengajuan",
  disetujui: "Disetujui",
  ditolak: "Ditolak / Batal",
  proposal: "Tahap Proposal",
  sempro: "Sudah Sempro",
  bimbingan: "Proses Bimbingan",
  pengajuan_sidang: "Pengajuan Sidang",
  pengajuan_sidang_acc: "Sidang Disetujui",
  pengajuan_sidang_tolak: "Sidang Ditolak",
  semhas: "Seminar Hasil",
  sidang: "Sidang",
  revisi: "Revisi",
  lulus: "Lulus",
};

const activeSkripsi = computed(() =>
  skripsiList.value.find((s) => s.is_active),
);

const inactiveSkripsi = computed(() =>
  skripsiList.value.filter((s) => !s.is_active),
);

const getStatusLabel = (status) => statusMap[status] || status;

const getStatusBadgeClass = (status) => {
  const map = {
    draft:
      "bg-gray-100 text-gray-600 dark:bg-gray-800 dark:text-gray-400 border-gray-200 dark:border-gray-700",
    pengajuan:
      "bg-yellow-50 text-yellow-700 dark:bg-yellow-900/30 dark:text-yellow-300 border-yellow-100 dark:border-yellow-800",
    disetujui:
      "bg-green-50 text-green-700 dark:bg-green-900/30 dark:text-green-300 border-green-100 dark:border-green-800",
    ditolak:
      "bg-red-50 text-red-700 dark:bg-red-900/30 dark:text-red-300 border-red-100 dark:border-red-800",
    proposal:
      "bg-blue-50 text-primary dark:bg-blue-900/30 dark:text-blue-300 border-blue-100 dark:border-blue-800",
    sempro:
      "bg-green-50 text-green-700 dark:bg-green-900/30 dark:text-green-300 border-green-100 dark:border-green-800",
    bimbingan:
      "bg-blue-50 text-primary dark:bg-blue-900/30 dark:text-blue-300 border-blue-100 dark:border-blue-800",
    pengajuan_sidang:
      "bg-yellow-50 text-yellow-700 dark:bg-yellow-900/30 dark:text-yellow-300 border-yellow-100 dark:border-yellow-800",
    pengajuan_sidang_acc:
      "bg-green-50 text-green-700 dark:bg-green-900/30 dark:text-green-300 border-green-100 dark:border-green-800",
    pengajuan_sidang_tolak:
      "bg-red-50 text-red-700 dark:bg-red-900/30 dark:text-red-300 border-red-100 dark:border-red-800",
    semhas:
      "bg-blue-50 text-primary dark:bg-blue-900/30 dark:text-blue-300 border-blue-100 dark:border-blue-800",
    sidang:
      "bg-blue-50 text-primary dark:bg-blue-900/30 dark:text-blue-300 border-blue-100 dark:border-blue-800",
    revisi:
      "bg-orange-50 text-orange-700 dark:bg-orange-900/30 dark:text-orange-300 border-orange-100 dark:border-orange-800",
    lulus:
      "bg-green-50 text-green-700 dark:bg-green-900/30 dark:text-green-300 border-green-100 dark:border-green-800",
  };
  return map[status] || map.draft;
};

const getPembimbingNames = (skripsi) => {
  if (!skripsi?.pembimbing?.length) return "";
  return skripsi.pembimbing.map((p) => p.dosen?.nama || "Unknown").join(", ");
};

const formatDate = (dateStr) => {
  if (!dateStr) return "-";
  return new Date(dateStr).toLocaleDateString("id-ID", {
    day: "numeric",
    month: "short",
    year: "numeric",
  });
};

const closePengajuanModal = () => {
  if (submitting.value) return;
  showPengajuanModal.value = false;
  pengajuanError.value = "";
  pengajuanForm.judul = "";
  pengajuanForm.abstrak = "";
  pengajuanForm.kata_kunci = "";
  pengajuanForm.th_akademik_id = "";
};

const submitPengajuan = async () => {
  if (!pengajuanForm.judul.trim()) return;

  submitting.value = true;
  pengajuanError.value = "";

  try {
    const payload = {
      judul: pengajuanForm.judul.trim(),
    };
    if (pengajuanForm.abstrak.trim()) {
      payload.abstrak = pengajuanForm.abstrak.trim();
    }
    if (pengajuanForm.kata_kunci.trim()) {
      payload.kata_kunci = pengajuanForm.kata_kunci.trim();
    }
    if (pengajuanForm.th_akademik_id) {
      payload.th_akademik_id = pengajuanForm.th_akademik_id;
    }

    const res = await mahasiswaService.createSkripsi(payload);
    if (res.success) {
      showPengajuanModal.value = false;

      // Show success toast
      successMessage.value = "Pengajuan skripsi berhasil dikirim!";
      showSuccessToast.value = true;
      setTimeout(() => {
        showSuccessToast.value = false;
      }, 3000);

      // Refresh list
      const listRes = await mahasiswaService.getSkripsiList();
      if (listRes.success) {
        skripsiList.value = listRes.data;
      }

      // Reset form
      pengajuanForm.judul = "";
      pengajuanForm.abstrak = "";
      pengajuanForm.kata_kunci = "";
      pengajuanForm.th_akademik_id = "";
    }
  } catch (err) {
    console.error("Failed to submit pengajuan:", err);
    const msg =
      err.response?.data?.message ||
      err.response?.data?.errors?.judul?.[0] ||
      "Gagal mengajukan skripsi. Silakan coba lagi.";
    pengajuanError.value = msg;
  } finally {
    submitting.value = false;
  }
};

onMounted(async () => {
  try {
    const [res, tahunRes] = await Promise.all([
      mahasiswaService.getSkripsiList(),
      mahasiswaService.getTahunAkademik(),
    ]);
    if (res.success) {
      skripsiList.value = res.data;
    }
    if (tahunRes.success) {
      tahunList.value = tahunRes.data || [];
    }
  } catch (err) {
    console.error("Failed to load data:", err);
  } finally {
    loading.value = false;
  }
});
</script>

<style scoped>
/* Modal fade */
.modal-fade-enter-active,
.modal-fade-leave-active {
  transition: opacity 0.2s ease;
}
.modal-fade-enter-from,
.modal-fade-leave-to {
  opacity: 0;
}

/* Toast slide */
.toast-slide-enter-active {
  transition: all 0.3s ease-out;
}
.toast-slide-leave-active {
  transition: all 0.2s ease-in;
}
.toast-slide-enter-from {
  transform: translateY(20px);
  opacity: 0;
}
.toast-slide-leave-to {
  transform: translateY(20px);
  opacity: 0;
}
</style>
