<template>
  <div class="animate-fade-in">
    <!-- Empty State -->
    <div
      v-if="!pembimbingList.length"
      class="bg-surface-light rounded-xl shadow-sm border border-border-light p-12 flex flex-col items-center justify-center gap-3 text-center"
    >
      <span
        class="material-symbols-outlined text-5xl text-text-secondary opacity-40"
        >group</span
      >
      <h3 class="text-lg font-bold text-text-main">Belum Ada Pembimbing</h3>
      <p class="text-text-secondary text-sm">
        Pembimbing skripsi belum ditentukan.
      </p>
    </div>

    <!-- Mentor Sempro Cards -->
    <div v-if="mentorList.length" class="mb-8">
      <h3 class="text-lg font-bold text-text-main mb-4 flex items-center gap-2">
        <span class="material-symbols-outlined text-primary">groups</span>
        Mentor Seminar Proposal
      </h3>
      <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div
          v-for="(item, idx) in mentorList"
          :key="'m-'+(item.id || idx)"
          class="bg-surface-light rounded-xl p-6 shadow-sm border border-border-light flex items-start gap-4"
        >
          <div
            class="size-12 rounded-full flex items-center justify-center font-bold text-xl"
            :class="
              item.jenis === 'mentor_1'
                ? 'bg-blue-100 dark:bg-blue-900/30 text-primary'
                : 'bg-indigo-100 dark:bg-indigo-900/30 text-indigo-600'
            "
          >
            {{ getInitials(item.dosen?.nama) }}
          </div>
          <div class="flex-1">
            <h4 class="font-bold text-lg text-text-main">
              {{ item.dosen?.nama || "Unknown" }}
            </h4>
            <p class="text-sm text-text-secondary mb-2">
              Mentor {{ item.jenis === 'mentor_1' ? 'Utama' : 'Pendamping' }}
            </p>
            <div class="mt-3 text-xs text-text-secondary" v-if="item.dosen?.nip">
              NIP: {{ item.dosen.nip }}
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Pembimbing Cards -->
    <div v-if="pembimbingList.length">
      <h3 class="text-lg font-bold text-text-main mb-4 flex items-center gap-2">
        <span class="material-symbols-outlined text-orange-500">supervisor_account</span>
        Dosen Pembimbing Skripsi
      </h3>
      <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
      <div
        v-for="(item, idx) in pembimbingList"
        :key="item.id || idx"
        class="bg-surface-light rounded-xl p-6 shadow-sm border border-border-light flex items-start gap-4"
      >
        <div
          class="size-12 rounded-full flex items-center justify-center font-bold text-xl"
          :class="
            idx === 0
              ? 'bg-blue-100 dark:bg-blue-900/30 text-primary'
              : 'bg-orange-100 dark:bg-orange-900/30 text-orange-600'
          "
        >
          {{ getInitials(item.dosen?.nama) }}
        </div>
        <div class="flex-1">
          <h4 class="font-bold text-lg text-text-main">
            {{ item.dosen?.nama || "Unknown" }}
          </h4>
          <p class="text-sm text-text-secondary mb-2">
            {{ getPeranLabel(item.peran) }}
          </p>
          <div class="flex gap-2">
            <span
              :class="getStatusClass(item.status)"
              class="px-2 py-0.5 text-xs rounded font-medium"
            >
              {{ getStatusLabel(item.status) }}
            </span>
          </div>
          <div class="mt-3 text-xs text-text-secondary" v-if="item.dosen?.nip">
            NIP: {{ item.dosen.nip }}
          </div>
        </div>
      </div>
    </div>
    </div>
  </div>
</template>

<script setup>
import { inject, computed } from "vue";

const skripsi = inject("skripsi");

const pembimbingList = computed(() => skripsi.value?.pembimbing || []);
const mentorList = computed(() => skripsi.value?.mentorSempro || []);

const getInitials = (nama) => {
  if (!nama) return "??";
  return nama
    .split(" ")
    .map((w) => w[0])
    .join("")
    .toUpperCase()
    .slice(0, 2);
};

const getPeranLabel = (peran) => {
  const map = {
    pembimbing_1: "Pembimbing Utama",
    pembimbing_2: "Pembimbing Pendamping",
    utama: "Pembimbing Utama",
    pendamping: "Pembimbing Pendamping",
  };
  return map[peran] || peran || "Pembimbing";
};

const getStatusLabel = (status) => {
  const map = {
    disetujui: "Disetujui",
    pending: "Menunggu",
    ditolak: "Ditolak",
    aktif: "Aktif",
  };
  return map[status] || status || "Aktif";
};

const getStatusClass = (status) => {
  const map = {
    disetujui:
      "bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400",
    pending:
      "bg-yellow-100 text-yellow-700 dark:bg-yellow-900/30 dark:text-yellow-400",
    ditolak: "bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400",
    aktif:
      "bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400",
  };
  return (
    map[status] ||
    "bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400"
  );
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
