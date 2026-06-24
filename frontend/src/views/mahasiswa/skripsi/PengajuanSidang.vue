<template>
  <div class="flex flex-col gap-6 animate-fade-in">
    <!-- Info Banner -->
    <div
      class="flex items-start gap-3 p-4 bg-amber-50 dark:bg-amber-900/10 rounded-xl border border-amber-200 dark:border-amber-800"
    >
      <span class="material-symbols-outlined text-amber-500 mt-0.5"
        >info</span
      >
      <div>
        <p class="text-sm font-bold text-amber-700 dark:text-amber-300">
          Pengajuan Ujian Skripsi
        </p>
        <p class="text-xs text-amber-600/80 dark:text-amber-400/80 mt-0.5">
          Setelah menyelesaikan bimbingan dan memenuhi persyaratan, Anda dapat
          mengajukan ujian sidang skripsi. Pengajuan akan diverifikasi oleh
          dosen pembimbing utama sebelum dijadwalkan oleh admin.
        </p>
      </div>
    </div>

    <!-- Status: Not eligible yet -->
    <div
      v-if="!isEligiblePhase"
      class="bg-surface-light rounded-xl shadow-sm border border-border-light p-12 flex flex-col items-center justify-center gap-3 text-center"
    >
      <span
        class="material-symbols-outlined text-5xl text-text-secondary opacity-40"
        >lock</span
      >
      <h3 class="text-lg font-bold text-text-main">Belum Dapat Mengajukan</h3>
      <p class="text-text-secondary text-sm max-w-md">
        Anda belum dapat mengajukan ujian skripsi. Selesaikan terlebih dahulu
        tahap bimbingan dengan dosen pembimbing Anda. Pastikan jumlah bimbingan
        sudah mencukupi sebelum mengajukan sidang.
      </p>
    </div>

    <template v-else>
      <!-- Already submitted - waiting -->
      <div
        v-if="skripsi?.status === 'pengajuan_sidang'"
        class="bg-surface-light rounded-xl shadow-sm border border-border-light overflow-hidden"
      >
        <div
          class="p-5 border-b border-border-light flex items-center justify-between"
        >
          <div>
            <h3 class="text-lg font-bold text-text-main">
              Status Pengajuan
            </h3>
            <p class="text-sm text-text-secondary">
              Pengajuan ujian skripsi Anda sedang diproses
            </p>
          </div>
          <span
            class="px-3 py-1 rounded-full text-xs font-bold bg-yellow-50 text-yellow-700 border border-yellow-200 dark:bg-yellow-900/30 dark:text-yellow-300 dark:border-yellow-800"
          >
            Menunggu Persetujuan
          </span>
        </div>
        <div class="p-5">
          <div
            class="flex items-center gap-3 p-4 bg-yellow-50 dark:bg-yellow-900/10 rounded-lg border-l-4 border-yellow-400"
          >
            <span class="material-symbols-outlined text-yellow-600"
              >pending_actions</span
            >
            <div>
              <p class="text-sm font-bold text-text-main">
                Menunggu Persetujuan Dosen Pembimbing
              </p>
              <p class="text-xs text-text-secondary mt-0.5">
                Pengajuan ujian skripsi Anda sedang menunggu persetujuan dari
                dosen pembimbing utama. Anda akan mendapat notifikasi setelah
                disetujui.
              </p>
            </div>
          </div>
        </div>
      </div>

      <!-- Approved -->
      <div
        v-else-if="skripsi?.status === 'pengajuan_sidang_acc'"
        class="bg-surface-light rounded-xl shadow-sm border border-border-light overflow-hidden"
      >
        <div
          class="p-5 border-b border-border-light flex items-center justify-between"
        >
          <div>
            <h3 class="text-lg font-bold text-text-main">
              Status Pengajuan
            </h3>
            <p class="text-sm text-text-secondary">
              Pengajuan Anda telah diproses
            </p>
          </div>
          <span
            class="px-3 py-1 rounded-full text-xs font-bold bg-green-50 text-green-700 border border-green-200 dark:bg-green-900/30 dark:text-green-400 dark:border-green-800"
          >
            Disetujui
          </span>
        </div>
        <div class="p-5">
          <div
            class="flex items-center gap-3 p-4 bg-green-50 dark:bg-green-900/10 rounded-lg border-l-4 border-green-400"
          >
            <span class="material-symbols-outlined text-green-600"
              >check_circle</span
            >
            <div>
              <p class="text-sm font-bold text-text-main">
                Pengajuan Disetujui!
              </p>
              <p class="text-xs text-text-secondary mt-0.5">
                Pengajuan ujian skripsi Anda telah
                <strong>disetujui</strong> oleh dosen pembimbing. Menunggu admin
                menjadwalkan ujian Anda. Silakan cek tab
                <strong>Sidang</strong> secara berkala.
              </p>
            </div>
          </div>
        </div>
      </div>

      <!-- Rejected -->
      <div
        v-else-if="skripsi?.status === 'pengajuan_sidang_tolak'"
        class="bg-surface-light rounded-xl shadow-sm border border-border-light overflow-hidden"
      >
        <div
          class="p-5 border-b border-border-light flex items-center justify-between"
        >
          <div>
            <h3 class="text-lg font-bold text-text-main">
              Status Pengajuan
            </h3>
            <p class="text-sm text-text-secondary">
              Pengajuan Anda telah diproses
            </p>
          </div>
          <span
            class="px-3 py-1 rounded-full text-xs font-bold bg-red-50 text-red-700 border border-red-200 dark:bg-red-900/30 dark:text-red-400 dark:border-red-800"
          >
            Ditolak
          </span>
        </div>
        <div class="p-5 flex flex-col gap-4">
          <div
            class="flex items-start gap-3 p-4 bg-red-50 dark:bg-red-900/10 rounded-lg border-l-4 border-red-400"
          >
            <span class="material-symbols-outlined text-red-600 mt-0.5"
              >error</span
            >
            <div>
              <p class="text-sm font-bold text-red-700 dark:text-red-400 mb-1">
                Pengajuan Ujian Ditolak
              </p>
              <p
                v-if="skripsi?.alasan_tolak_sidang"
                class="text-sm text-text-main"
              >
                <strong>Alasan:</strong> {{ skripsi.alasan_tolak_sidang }}
              </p>
              <p v-else class="text-sm text-text-secondary italic">
                Tidak ada alasan yang diberikan.
              </p>
            </div>
          </div>
          <div class="flex justify-end">
            <button
              @click="openUjianRequestModal"
              :disabled="submittingUjian"
              class="inline-flex items-center gap-2 bg-primary hover:bg-blue-600 text-white font-bold px-6 py-3 rounded-lg transition-all shadow-md shadow-primary/20 text-sm"
            >
              <span
                v-if="submittingUjian"
                class="animate-spin rounded-full h-4 w-4 border-b-2 border-white"
              ></span>
              <span v-else class="material-symbols-outlined text-[20px]"
                >refresh</span
              >
              {{ submittingUjian ? "Mengirim..." : "Ajukan Ulang" }}
            </button>
          </div>
        </div>
      </div>

      <!-- Eligibility Check (status = bimbingan) -->
      <div
        v-else
        class="bg-surface-light rounded-xl shadow-sm border border-border-light overflow-hidden"
      >
        <div
          class="p-5 border-b border-border-light"
        >
          <h3 class="text-lg font-bold text-text-main">
            Persyaratan Pengajuan
          </h3>
          <p class="text-sm text-text-secondary">
            Pastikan semua persyaratan terpenuhi sebelum mengajukan ujian
          </p>
        </div>
        <div class="p-5">
          <div
            v-if="ujianEligibility === null"
            class="flex items-center gap-3 text-text-secondary py-4"
          >
            <span
              class="animate-spin rounded-full h-5 w-5 border-b-2 border-primary"
            ></span>
            <span class="text-sm">Memeriksa kelayakan...</span>
          </div>
          <div v-else class="flex flex-col gap-4">
            <!-- Checklist -->
            <div class="space-y-3">
              <div
                v-for="(item, idx) in ujianChecklist"
                :key="idx"
                class="flex items-center gap-3 p-3 rounded-lg border"
                :class="
                  item.met
                    ? 'bg-green-50 dark:bg-green-900/10 border-green-200 dark:border-green-800'
                    : 'bg-red-50 dark:bg-red-900/10 border-red-200 dark:border-red-800'
                "
              >
                <span
                  class="material-symbols-outlined text-[20px]"
                  :class="item.met ? 'text-green-600' : 'text-red-500'"
                  >{{ item.met ? "check_circle" : "cancel" }}</span
                >
                <div class="flex-1">
                  <p class="text-sm font-medium text-text-main">
                    {{ item.label }}
                  </p>
                  <p class="text-xs text-text-secondary">{{ item.detail }}</p>
                </div>
              </div>
            </div>

            <!-- Submit Button -->
            <div class="flex justify-end pt-2">
              <button
                v-if="ujianEligibility?.eligible"
                @click="openUjianRequestModal"
                class="inline-flex items-center gap-2 bg-primary hover:bg-blue-600 text-white font-bold px-6 py-3 rounded-lg transition-all shadow-md shadow-primary/20 text-sm"
              >
                <span class="material-symbols-outlined text-[20px]"
                  >school</span
                >
                Ajukan Ujian Skripsi
              </button>
              <p v-else class="text-sm text-red-500 font-medium">
                Anda belum memenuhi semua syarat untuk mengajukan ujian
                skripsi.
              </p>
            </div>
          </div>
        </div>
      </div>
    </template>

    <!-- ===== MODAL: KONFIRMASI UJIAN ===== -->
    <div
      v-if="showUjianConfirmModal"
      class="fixed inset-0 z-50 flex items-center justify-center p-4"
    >
      <div
        class="absolute inset-0 bg-black/50 backdrop-blur-sm"
        @click="closeUjianRequestModal"
      ></div>
      <div
        class="relative bg-surface-light rounded-xl shadow-xl border border-border-light w-full max-w-sm max-h-[90vh] overflow-y-auto"
      >
        <div class="p-6 flex flex-col items-center gap-4 text-center">
          <div
            class="size-14 rounded-full flex items-center justify-center bg-blue-100 dark:bg-blue-900/30 text-primary"
          >
            <span class="material-symbols-outlined text-[28px]">school</span>
          </div>
          <div>
            <h3 class="text-lg font-bold text-text-main">
              Ajukan Ujian Skripsi?
            </h3>
            <p class="text-sm text-text-secondary mt-1">
              Pengajuan akan dikirim ke dosen pembimbing utama untuk disetujui.
            </p>
          </div>
          <div
            class="w-full flex items-start gap-2.5 p-3 rounded-lg bg-amber-50 dark:bg-amber-900/10 border border-amber-200 dark:border-amber-800 text-left"
          >
            <span
              class="material-symbols-outlined text-amber-600 text-[18px] mt-0.5"
              >info</span
            >
            <p class="text-xs text-amber-800 dark:text-amber-300">
              Belum mempunyai SK 6? Silakan mengurusnya ke staff prodi
              masing-masing sebelum mengajukan sidang.
            </p>
          </div>
          <div class="w-full text-left">
            <label class="block text-sm font-bold text-text-main mb-1.5">
              Lampiran SK 6 <span class="text-red-500">*</span>
            </label>
            <label
              class="flex items-center gap-3 px-3 py-3 rounded-lg border border-dashed border-border-light hover:border-primary hover:bg-primary/5 cursor-pointer transition-colors"
            >
              <span class="material-symbols-outlined text-primary"
                >upload_file</span
              >
              <span class="min-w-0 flex-1">
                <span class="block text-sm font-medium text-text-main truncate">
                  {{ sk6File?.name || "Pilih file SK 6" }}
                </span>
                <span class="block text-xs text-text-secondary">
                  PDF, DOC, atau DOCX, maksimal 20 MB
                </span>
              </span>
              <input
                type="file"
                class="sr-only"
                accept=".pdf,.doc,.docx,application/pdf,application/msword,application/vnd.openxmlformats-officedocument.wordprocessingml.document"
                @change="handleSk6File"
              />
            </label>
            <p v-if="sk6Error" class="text-xs text-red-600 mt-1.5">
              {{ sk6Error }}
            </p>
          </div>
          <div class="flex gap-3 w-full">
            <button
              @click="closeUjianRequestModal"
              class="flex-1 px-4 py-2.5 rounded-lg text-text-secondary font-bold hover:bg-sidebar-light transition-colors text-sm border border-border-light"
            >
              Batal
            </button>
            <button
              @click="submitUjianRequest"
              :disabled="submittingUjian"
              class="flex-1 px-4 py-2.5 rounded-lg bg-primary text-white font-bold hover:bg-blue-600 transition-colors shadow-sm text-sm flex items-center justify-center gap-2 disabled:opacity-50"
            >
              <span
                v-if="submittingUjian"
                class="animate-spin rounded-full h-4 w-4 border-b-2 border-white"
              ></span>
              {{ submittingUjian ? "Mengirim..." : "Ya, Ajukan" }}
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- Toast -->
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
  </div>
</template>

<script setup>
import { ref, inject, computed, onMounted } from "vue";
import { mahasiswaService } from "../../../services/mahasiswaService";

const skripsi = inject("skripsi");

const isEligiblePhase = computed(() =>
  [
    "bimbingan",
    "pengajuan_sidang",
    "pengajuan_sidang_tolak",
    "pengajuan_sidang_acc",
  ].includes(skripsi.value?.status),
);

// Eligibility
const ujianEligibility = ref(null);
const ujianChecklist = computed(() => {
  if (!ujianEligibility.value) return [];
  const e = ujianEligibility.value;
  return [
    {
      label: "Bimbingan Minimal Terpenuhi",
      detail: `${e.approved_count || 0} dari ${e.min_bimbingan || 8} bimbingan disetujui`,
      met: e.bimbingan_met,
    },
    {
      label: "Pembimbing Telah Ditentukan",
      detail: e.pembimbing_met
        ? "Pembimbing sudah ditetapkan"
        : "Pembimbing belum ditetapkan",
      met: e.pembimbing_met,
    },
    {
      label: "Seminar Proposal Selesai",
      detail: e.sempro_met
        ? "Sempro sudah selesai"
        : "Sempro belum selesai",
      met: e.sempro_met,
    },
  ];
});

// Modal & submission
const showUjianConfirmModal = ref(false);
const submittingUjian = ref(false);
const sk6File = ref(null);
const sk6Error = ref("");
const ujianToast = ref({ show: false, message: "", type: "success" });

const openUjianRequestModal = () => {
  sk6File.value = null;
  sk6Error.value = "";
  showUjianConfirmModal.value = true;
};

const closeUjianRequestModal = () => {
  showUjianConfirmModal.value = false;
  sk6File.value = null;
  sk6Error.value = "";
};

const handleSk6File = (event) => {
  const file = event.target.files?.[0] || null;
  sk6Error.value = "";

  if (!file) {
    sk6File.value = null;
    return;
  }

  const extension = file.name.split(".").pop()?.toLowerCase();
  if (!["pdf", "doc", "docx"].includes(extension)) {
    sk6File.value = null;
    sk6Error.value = "Format SK 6 harus PDF, DOC, atau DOCX.";
    event.target.value = "";
    return;
  }

  if (file.size > 20 * 1024 * 1024) {
    sk6File.value = null;
    sk6Error.value = "Ukuran file SK 6 maksimal 20 MB.";
    event.target.value = "";
    return;
  }

  sk6File.value = file;
};

const submitUjianRequest = async () => {
  if (!sk6File.value) {
    sk6Error.value = "File SK 6 wajib dilampirkan.";
    return;
  }

  submittingUjian.value = true;
  try {
    const formData = new FormData();
    formData.append("file_sk6", sk6File.value);
    const res = await mahasiswaService.requestUjian(formData);
    if (res.success) {
      closeUjianRequestModal();
      if (skripsi.value) {
        skripsi.value.status = "pengajuan_sidang";
        skripsi.value.progress_percentage = 60;
      }
      ujianToast.value = {
        show: true,
        message: "Pengajuan ujian skripsi berhasil dikirim!",
        type: "success",
      };
      setTimeout(() => {
        ujianToast.value.show = false;
      }, 3000);
    }
  } catch (err) {
    console.error("Failed to submit ujian request:", err);
    const msg =
      err.response?.data?.message || "Gagal mengajukan ujian skripsi.";
    ujianToast.value = { show: true, message: msg, type: "error" };
    setTimeout(() => {
      ujianToast.value.show = false;
    }, 4000);
  } finally {
    submittingUjian.value = false;
  }
};

const checkUjianEligibility = async () => {
  if (!isEligiblePhase.value) return;
  if (skripsi.value?.status !== "bimbingan") return;
  try {
    const res = await mahasiswaService.checkUjianEligibility();
    if (res.success) {
      ujianEligibility.value = res.data;
    }
  } catch (err) {
    console.error("Failed to check ujian eligibility:", err);
  }
};

onMounted(checkUjianEligibility);
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
.toast-slide-enter-active {
  transition: all 0.3s ease;
}
.toast-slide-leave-active {
  transition: all 0.3s ease;
}
.toast-slide-enter-from,
.toast-slide-leave-to {
  opacity: 0;
  transform: translateY(20px);
}
</style>
