<template>
  <div class="animate-fade-in">
    <div
      v-if="!mentorList.length"
      class="bg-surface-light rounded-xl shadow-sm border border-border-light p-12 flex flex-col items-center justify-center gap-3 text-center"
    >
      <span
        class="material-symbols-outlined text-5xl text-text-secondary opacity-40"
        >groups</span
      >
      <h3 class="text-lg font-bold text-text-main">Belum Ada Mentor</h3>
      <p class="text-text-secondary text-sm">
        Dosen mentor seminar proposal belum ditentukan.
      </p>
    </div>

    <div v-else>
      <h3 class="text-lg font-bold text-text-main mb-4 flex items-center gap-2">
        <span class="material-symbols-outlined text-primary">groups</span>
        Dosen Mentor Seminar Proposal
      </h3>
      <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div
          v-for="(item, index) in mentorList"
          :key="item.id || index"
          class="bg-surface-light rounded-xl p-6 shadow-sm border border-border-light flex items-start gap-4"
        >
          <div
            class="size-12 rounded-full flex items-center justify-center font-bold text-xl shrink-0"
            :class="
              item.jenis === 'mentor_1'
                ? 'bg-blue-100 dark:bg-blue-900/30 text-primary'
                : 'bg-indigo-100 dark:bg-indigo-900/30 text-indigo-600'
            "
          >
            {{ getInitials(getDosenName(item.dosen)) }}
          </div>
          <div class="flex-1 min-w-0">
            <h4 class="font-bold text-lg text-text-main truncate">
              {{ getDosenName(item.dosen) }}
            </h4>
            <p class="text-sm text-text-secondary mb-2">
              {{ getMentorLabel(item.jenis) }}
            </p>
            <span
              class="inline-flex px-2 py-0.5 text-xs rounded font-medium"
              :class="
                item.is_active === false
                  ? 'bg-gray-100 text-gray-600 dark:bg-gray-800 dark:text-gray-400'
                  : 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400'
              "
            >
              {{ item.is_active === false ? "Tidak Aktif" : "Aktif sampai Sempro" }}
            </span>
            <div class="mt-3 space-y-1 text-xs text-text-secondary">
              <p v-if="item.dosen?.nip">NIP: {{ item.dosen.nip }}</p>
              <p v-if="item.tanggal_penetapan">
                Ditetapkan: {{ formatDate(item.tanggal_penetapan) }}
              </p>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { computed, inject } from "vue";

const skripsi = inject("skripsi");

const mentorList = computed(() => {
  const mentors = skripsi.value?.mentor_sempro || skripsi.value?.mentorSempro || [];
  return [...mentors].sort((a, b) =>
    (a.jenis || "").localeCompare(b.jenis || ""),
  );
});

const getDosenName = (dosen) =>
  dosen?.full_name || dosen?.nama || "Dosen belum tersedia";

const getInitials = (name) => {
  if (!name) return "??";
  return name
    .split(" ")
    .filter(Boolean)
    .map((word) => word[0])
    .join("")
    .toUpperCase()
    .slice(0, 2);
};

const getMentorLabel = (jenis) => {
  const labels = {
    mentor_1: "Mentor Utama",
    mentor_2: "Mentor Pendamping",
  };
  return labels[jenis] || "Dosen Mentor";
};

const formatDate = (date) =>
  new Date(date).toLocaleDateString("id-ID", {
    day: "2-digit",
    month: "short",
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
