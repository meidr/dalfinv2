<template>
  <div class="flex flex-col gap-6 animate-fade-in">
    <!-- Info Banner -->
    <div
      class="flex items-start gap-3 p-4 bg-indigo-50 dark:bg-indigo-900/10 rounded-xl border border-indigo-200 dark:border-indigo-800"
    >
      <span class="material-symbols-outlined text-indigo-500 mt-0.5"
        >info</span
      >
      <div>
        <p class="text-sm font-bold text-indigo-700 dark:text-indigo-300">
          Dosen Pembimbing Skripsi
        </p>
        <p class="text-xs text-indigo-600/80 dark:text-indigo-400/80 mt-0.5">
          Dosen pembimbing akan membimbing Anda selama proses penulisan skripsi
          hingga sidang. Hubungi pembimbing untuk konsultasi dan bimbingan
          rutin.
        </p>
      </div>
    </div>

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
      <p class="text-text-secondary text-sm max-w-md">
        Dosen pembimbing skripsi belum ditentukan. Pembimbing akan ditetapkan
        oleh koordinator setelah judul skripsi Anda disetujui dan seminar
        proposal selesai.
      </p>
    </div>

    <!-- Pembimbing Cards -->
    <div v-else class="grid grid-cols-1 md:grid-cols-2 gap-6">
      <div
        v-for="(item, idx) in pembimbingList"
        :key="item.id || idx"
        class="bg-surface-light rounded-xl shadow-sm border border-border-light overflow-hidden"
      >
        <!-- Card Header -->
        <div
          class="px-6 py-4 border-b border-border-light"
          :class="
            idx === 0
              ? 'bg-gradient-to-r from-blue-50 to-indigo-50 dark:from-blue-900/20 dark:to-indigo-900/20'
              : 'bg-gradient-to-r from-orange-50 to-amber-50 dark:from-orange-900/20 dark:to-amber-900/20'
          "
        >
          <div class="flex items-center gap-3">
            <div
              class="size-12 rounded-full flex items-center justify-center font-bold text-xl shrink-0"
              :class="
                idx === 0
                  ? 'bg-blue-100 dark:bg-blue-900/30 text-primary'
                  : 'bg-orange-100 dark:bg-orange-900/30 text-orange-600'
              "
            >
              {{ getInitials(item.dosen?.nama) }}
            </div>
            <div class="min-w-0">
              <h4 class="font-bold text-lg text-text-main truncate">
                {{ item.dosen?.nama || "Unknown" }}
              </h4>
              <p class="text-sm text-text-secondary">
                {{ getPeranLabel(item.peran) }}
              </p>
            </div>
          </div>
        </div>

        <!-- Card Body -->
        <div class="px-6 py-4 space-y-3">
          <div class="flex items-center justify-between">
            <span class="text-xs text-text-secondary font-medium">Status</span>
            <span
              :class="getStatusClass(item.status)"
              class="px-2.5 py-0.5 text-xs rounded-full font-bold"
            >
              {{ getStatusLabel(item.status) }}
            </span>
          </div>
          <div v-if="item.dosen?.nip" class="flex items-center justify-between">
            <span class="text-xs text-text-secondary font-medium">NIP</span>
            <span class="text-sm font-medium text-text-main">{{
              item.dosen.nip
            }}</span>
          </div>
          <div
            v-if="item.tanggal_penetapan"
            class="flex items-center justify-between"
          >
            <span class="text-xs text-text-secondary font-medium"
              >Ditetapkan</span
            >
            <span class="text-sm font-medium text-text-main">{{
              formatDate(item.tanggal_penetapan)
            }}</span>
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
    disetujui: "Aktif",
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

const formatDate = (date) =>
  new Date(date).toLocaleDateString("id-ID", {
    day: "2-digit",
    month: "long",
    year: "numeric",
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
</style>
