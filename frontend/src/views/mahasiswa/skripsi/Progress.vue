<template>
  <div class="flex flex-col gap-6 animate-fade-in">
    <!-- Workflow Stepper -->
    <div
      class="bg-surface-light rounded-xl shadow-sm border border-border-light p-6 overflow-x-auto"
    >
      <h3 class="text-lg font-bold text-text-main mb-6">Workflow Progress</h3>
      <div class="min-w-[800px]">
        <ol class="flex items-center w-full relative justify-between">
          <li
            v-for="(step, i) in steps"
            :key="step.key"
            class="relative flex flex-col items-center group w-full"
          >
            <div class="flex items-center w-full">
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
              <!-- Connector line (not on last item) -->
              <div
                v-if="i < steps.length - 1"
                class="flex-auto border-t-4"
                :class="
                  step.state === 'done'
                    ? 'border-green-500'
                    : step.state === 'active'
                      ? 'border-primary/40'
                      : 'border-gray-200 dark:border-gray-700'
                "
              ></div>
            </div>
            <div class="mt-3 text-center pr-6">
              <h4
                class="text-sm font-bold"
                :class="
                  step.state === 'done'
                    ? 'text-green-700 dark:text-green-400'
                    : step.state === 'active'
                      ? 'text-primary'
                      : 'text-gray-400'
                "
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
          class="bg-gradient-to-r from-blue-500 to-primary h-3 rounded-full transition-all duration-700"
          :style="{ width: (skripsi?.progress_percentage ?? 0) + '%' }"
        ></div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { inject, computed } from "vue";

const skripsi = inject("skripsi");

const statusMap = {
  draft: "Draft",
  pengajuan: "Pengajuan Judul",
  disetujui: "Judul Disetujui",
  ditolak: "Ditolak",
  proposal: "Tahap Proposal",
  sempro: "Sudah Sempro",
  bimbingan: "Proses Bimbingan",
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
  "pengajuan",
  "disetujui",
  "proposal",
  "sempro",
  "bimbingan",
  "semhas",
  "sidang",
  "revisi",
  "lulus",
];

const stepDefs = [
  { key: "judul", label: "Judul", after: ["pengajuan", "disetujui"] },
  { key: "dospem", label: "Dospem", after: ["disetujui"] },
  { key: "proposal", label: "Proposal", after: ["proposal"] },
  { key: "sempro", label: "Sempro", after: ["sempro"] },
  { key: "bimbingan", label: "Bimbingan", after: ["bimbingan"] },
  { key: "semhas", label: "Semhas", after: ["semhas"] },
  { key: "sidang", label: "Sidang", after: ["sidang"] },
  { key: "revisi", label: "Revisi", after: ["revisi"] },
  { key: "lulus", label: "Lulus", after: ["lulus"] },
];

const steps = computed(() => {
  const status = skripsi.value?.status || "draft";
  const currentIdx = statusOrder.indexOf(status);

  return stepDefs.map((step) => {
    const stepIdx = statusOrder.indexOf(step.after[step.after.length - 1]);
    let state = "pending";
    if (stepIdx < currentIdx) state = "done";
    else if (stepIdx === currentIdx) state = "active";
    return { ...step, state };
  });
});

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
