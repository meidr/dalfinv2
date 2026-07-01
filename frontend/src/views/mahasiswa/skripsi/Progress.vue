<template>
  <div class="flex flex-col gap-6 animate-fade-in">
    <!-- Workflow Stepper -->
    <div
      class="bg-surface-light rounded-xl shadow-sm border border-border-light p-6 overflow-x-auto"
    >
      <h3 class="text-lg font-bold text-text-main mb-6">Workflow Progress</h3>
      <div class="min-w-[800px]">
        <ol class="flex items-start w-full relative justify-between">
          <li
            v-for="(step, i) in steps"
            :key="step.key"
            class="relative flex flex-col items-center group flex-1 cursor-pointer transition-transform hover:scale-105"
            @click="navigateToStep(step)"
          >
            <div class="flex items-center w-full">
              <!-- Left connector -->
              <div
                class="flex-auto border-t-4"
                :class="
                  i === 0
                    ? 'border-transparent'
                    : steps[i - 1]?.state === 'done'
                      ? 'border-green-500'
                      : steps[i - 1]?.state === 'active'
                        ? 'border-primary/40'
                        : 'border-gray-200 dark:border-gray-700'
                "
              ></div>
              <!-- Icon -->
              <div
                class="flex items-center justify-center size-10 rounded-full shrink-0 z-10 border-2"
                :class="getStepIconClass(step.state)"
              >
                <span
                  v-if="step.state === 'done'"
                  class="material-symbols-outlined text-green-600 text-xl font-bold"
                  >check</span
                >
                <span
                  v-else-if="step.state === 'active'"
                  class="material-symbols-outlined text-primary text-xl font-bold animate-pulse"
                  >radio_button_checked</span
                >
                <span
                  v-else
                  class="material-symbols-outlined text-gray-400 text-xl"
                  >circle</span
                >
              </div>
              <!-- Right connector -->
              <div
                class="flex-auto border-t-4"
                :class="
                  i === steps.length - 1
                    ? 'border-transparent'
                    : step.state === 'done'
                      ? 'border-green-500'
                      : step.state === 'active'
                        ? 'border-primary/40'
                        : 'border-gray-200 dark:border-gray-700'
                "
              ></div>
            </div>
            <div class="mt-3 text-center px-1 w-max max-w-[120px]">
              <h4
                class="text-sm font-bold leading-tight mb-1"
                :class="
                  step.state === 'done'
                    ? 'text-green-700 dark:text-green-400'
                    : step.state === 'active'
                      ? 'text-primary'
                      : 'text-gray-400'
                "
                :title="step.tooltip || ''"
              >
                {{ step.label }}
              </h4>
              <p class="text-xs text-text-secondary">
                {{
                  step.state === "done"
                    ? "Selesai"
                    : step.state === "active"
                      ? "Saat Ini"
                      : "Belum"
                }}
              </p>
            </div>
          </li>
        </ol>
      </div>
    </div>

    <!-- Current Status Details -->
    <div
      class="bg-surface-light rounded-xl shadow-sm border border-border-light p-6"
    >
      <div class="flex items-center gap-3 mb-4">
        <div class="p-2 bg-primary/10 rounded-lg text-primary">
          <span class="material-symbols-outlined">info</span>
        </div>
        <h3 class="text-lg font-bold text-text-main">Status Saat Ini</h3>
      </div>
      <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div class="flex flex-col gap-1 p-3 bg-sidebar-light rounded-lg">
          <span class="text-xs text-text-secondary font-semibold uppercase"
            >Status</span
          >
          <span class="font-bold text-text-main">{{ currentStatusLabel }}</span>
        </div>
        <div class="flex flex-col gap-1 p-3 bg-sidebar-light rounded-lg">
          <span class="text-xs text-text-secondary font-semibold uppercase"
            >Progress</span
          >
          <span class="font-bold text-text-main"
            >{{ skripsi?.progress_percentage ?? 0 }}%</span
          >
        </div>
        <div
          v-if="skripsi?.tanggal_daftar"
          class="flex flex-col gap-1 p-3 bg-sidebar-light rounded-lg"
        >
          <span class="text-xs text-text-secondary font-semibold uppercase"
            >Tanggal Daftar</span
          >
          <span class="font-bold text-text-main">{{
            formatDate(skripsi.tanggal_daftar)
          }}</span>
        </div>
        <div class="flex flex-col gap-1 p-3 bg-sidebar-light rounded-lg">
          <span class="text-xs text-text-secondary font-semibold uppercase"
            >Terakhir Update</span
          >
          <span class="font-bold text-text-main">{{
            formatDate(skripsi?.updated_at)
          }}</span>
        </div>
      </div>
    </div>

    <!-- Upload Proposal Instructions (only when status is proposal) -->
    <div
      v-if="skripsi?.status === 'proposal'"
      class="bg-surface-light rounded-xl shadow-sm border border-blue-200 dark:border-blue-800 p-6"
    >
      <div class="flex items-center gap-3 mb-4">
        <div
          class="p-2 bg-blue-100 dark:bg-blue-900/30 rounded-lg text-primary"
        >
          <span class="material-symbols-outlined">upload_file</span>
        </div>
        <div>
          <h3 class="text-lg font-bold text-text-main">
            Upload Dokumen Proposal
          </h3>
          <p class="text-sm text-text-secondary">
            Silakan upload dokumen proposal skripsi Anda
          </p>
        </div>
      </div>
      <div
        class="bg-blue-50/50 dark:bg-blue-900/10 border border-blue-100 dark:border-blue-800 rounded-xl p-4 mb-4"
      >
        <p class="text-sm font-bold text-text-main mb-3">
          <span
            class="material-symbols-outlined text-[16px] align-text-bottom mr-1 text-primary"
            >info</span
          >
          Tata Cara Upload
        </p>
        <ol
          class="text-sm text-text-secondary space-y-1.5 list-decimal list-inside"
        >
          <li>
            Klik tombol
            <span class="font-semibold text-text-main">Upload Berkas</span> di
            bawah
          </li>
          <li>
            Pilih jenis berkas
            <span class="font-semibold text-text-main">Proposal</span>
          </li>
          <li>Pilih file yang akan diupload</li>
          <li>
            Berikan catatan jika diperlukan
            <span class="italic">(opsional)</span>
          </li>
        </ol>
      </div>
      <router-link
        :to="{ name: 'SkripsiDokumen' }"
        class="inline-flex items-center gap-2 px-5 py-2.5 bg-primary text-white rounded-lg text-sm font-bold hover:bg-blue-600 transition-colors shadow-sm"
      >
        <span class="material-symbols-outlined text-[18px]">upload_file</span>
        Upload Berkas
      </router-link>
    </div>

    <!-- Progress Bar -->
    <div
      class="bg-surface-light rounded-xl shadow-sm border border-border-light p-6"
    >
      <div class="flex justify-between items-end mb-3">
        <span class="text-sm font-medium text-text-main"
          >Progres Keseluruhan</span
        >
        <span class="text-sm font-bold text-primary"
          >{{ skripsi?.progress_percentage ?? 0 }}%</span
        >
      </div>
      <div class="w-full bg-sidebar-light rounded-full h-3 overflow-hidden">
        <div
          class="bg-linear-to-r from-blue-500 to-primary h-3 rounded-full transition-all duration-700"
          :style="{ width: (skripsi?.progress_percentage ?? 0) + '%' }"
        ></div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { inject, computed } from "vue";
import { useRouter } from "vue-router";
import { useAuthStore } from "../../../stores/auth";

const router = useRouter();

const authStore = useAuthStore();
const skripsi = inject("skripsi");

const statusMap = {
  draft: "Draft",
  pengajuan: "Pengajuan Judul",
  disetujui: "Judul Disetujui",
  penentuan_mentor: "Penentuan Mentor",
  mentor: "Mentor Ditentukan",
  ditolak: "Ditolak",
  proposal: "Tahap Proposal",
  sempro: "Sudah Sempro",
  penentuan_dospem: "Penentuan Dospem",
  dospem: "Dospem Ditentukan",
  bimbingan: "Proses Bimbingan",
  pengajuan_sidang: "Pengajuan Sidang",
  pengajuan_sidang_acc: "Sidang Disetujui",
  pengajuan_sidang_tolak: "Sidang Ditolak",
  semhas: "Seminar Hasil",
  sidang: "Sidang Skripsi",
  revisi: "Revisi",
  lulus: "Lulus",
};

const currentStatusLabel = computed(
  () => statusMap[skripsi.value?.status] || skripsi.value?.status || "-",
);

// Define workflow steps and derive their state from skripsi status
const statusOrder = [
  "draft",
  "pengajuan",
  "ditolak",
  "disetujui",
  "penentuan_mentor",
  "mentor",
  "proposal",
  "sempro",
  "penentuan_dospem",
  "dospem",
  "bimbingan",
  "pengajuan_sidang",
  "pengajuan_sidang_tolak",
  "pengajuan_sidang_acc",
  "semhas",
  "ujian",
  "sidang",
  "revisi",
  "lulus",
];

const stepDefs = computed(() => {
  const allSteps = [
    {
      key: "judul",
      label: "Judul",
      after: ["draft", "pengajuan", "ditolak", "disetujui"],
      routeName: "SkripsiProfil",
    },
    { key: "mentor", label: "Mentor", after: ["penentuan_mentor", "mentor"], routeName: "SkripsiMentor" },
    { key: "sempro", label: "Sempro", after: ["proposal", "sempro"], routeName: "SkripsiSempro" },
    {
      key: "penentuan_dospem",
      label: "Dospem",
      tooltip: "Penentuan Dosen Pembimbing",
      after: ["penentuan_dospem", "dospem"],
      routeName: "SkripsiDospem",
    },
    {
      key: "bimbingan",
      label: "Bimbingan",
      after: ["bimbingan"],
      routeName: "SkripsiBimbingan",
    },
    {
      key: "pengajuan_sidang",
      label: "Pengajuan Sidang",
      after: [
        "pengajuan_sidang",
        "pengajuan_sidang_acc",
        "pengajuan_sidang_tolak",
      ],
      routeName: "SkripsiPengajuanSidang",
    },
    { key: "semhas", label: "Semhas", after: ["semhas"], routeName: "SkripsiSidang" },
    { key: "sidang", label: "Sidang", after: ["ujian", "sidang"], routeName: "SkripsiSidang" },
    { key: "revisi", label: "Revisi", after: ["revisi"], routeName: "SkripsiRevisi" },
    { key: "lulus", label: "Lulus", after: ["lulus"], routeName: "SkripsiProgress" },
  ];
  if (!authStore.semhasEnabled) {
    return allSteps.filter((s) => s.key !== "semhas");
  }
  return allSteps;
});

const steps = computed(() => {
  const status = skripsi.value?.status || "draft";
  let currentIdx = statusOrder.indexOf(status);

  // Auto-correct currentIdx if admin bypassed the workflow (e.g., scheduled sidang while status is mentor)
  if (skripsi.value?.status === 'lulus' || skripsi.value?.status === 'revisi') {
    // keep as is
  } else if (skripsi.value?.ujian?.length > 0) {
    currentIdx = Math.max(currentIdx, statusOrder.indexOf("sidang"));
  } else if (skripsi.value?.seminar?.some((s) => s.jenis === "semhas")) {
    currentIdx = Math.max(currentIdx, statusOrder.indexOf("semhas"));
  } else if (skripsi.value?.bimbingan?.length > 0) {
    currentIdx = Math.max(currentIdx, statusOrder.indexOf("bimbingan"));
  } else if (skripsi.value?.pembimbing?.length > 0) {
    currentIdx = Math.max(currentIdx, statusOrder.indexOf("dospem"));
  } else if (skripsi.value?.seminar?.some((s) => s.jenis === "sempro")) {
    currentIdx = Math.max(currentIdx, statusOrder.indexOf("sempro"));
  }

  return stepDefs.value.map((step) => {
    const indices = step.after.map((s) => statusOrder.indexOf(s));
    const minIdx = Math.min(...indices);
    const maxIdx = Math.max(...indices);
    let state = "pending";
    if (currentIdx > maxIdx) state = "done";
    else if (currentIdx >= minIdx && currentIdx <= maxIdx) state = "active";
    return { ...step, state };
  });
});

const navigateToStep = (step) => {
  if (step.routeName) {
    router.push({ name: step.routeName });
  }
};

const getStepIconClass = (state) => {
  if (state === "done")
    return "bg-green-100 border-green-500 dark:bg-green-900/30";
  if (state === "active")
    return "bg-blue-100 border-primary dark:bg-blue-900/30";
  return "bg-gray-100 border-gray-300 dark:bg-gray-800 dark:border-gray-600";
};

const formatDate = (dateStr) => {
  if (!dateStr) return "-";
  return new Date(dateStr).toLocaleDateString("id-ID", {
    day: "numeric",
    month: "long",
    year: "numeric",
  });
};
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
</style>
