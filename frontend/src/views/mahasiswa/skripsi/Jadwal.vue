<template>
  <div class="flex flex-col gap-8 animate-fade-in">
    <!-- Seminar Section -->
    <div>
      <h3 class="font-bold text-lg mb-4 flex items-center gap-2">
        <span class="material-symbols-outlined text-primary">event</span>
        Jadwal Seminar
      </h3>

      <div
        v-if="!seminarList.length"
        class="bg-surface-light rounded-xl shadow-sm border border-border-light p-8 text-center"
      >
        <span
          class="material-symbols-outlined text-4xl text-text-secondary opacity-40"
          >event_busy</span
        >
        <p class="text-text-secondary text-sm mt-2">
          Belum ada jadwal seminar.
        </p>
      </div>

      <div v-else class="flex flex-col gap-4">
        <div
          v-for="s in seminarList"
          :key="s.id"
          class="bg-surface-light rounded-xl shadow-sm border border-border-light overflow-hidden"
        >
          <div class="flex flex-col xl:flex-row w-full">
            <!-- Date Badge -->
            <div
              class="w-full xl:w-1/5 bg-gradient-to-br from-primary/10 to-primary/5 flex items-center justify-center p-6"
            >
              <div
                class="bg-white/90 dark:bg-white/10 p-4 rounded-xl text-center shadow-md"
              >
                <p
                  class="text-primary text-xs font-bold uppercase tracking-widest"
                >
                  {{ getMonth(s.tanggal) }}
                </p>
                <p
                  class="text-text-main dark:text-white text-4xl font-black leading-tight"
                >
                  {{ getDay(s.tanggal) }}
                </p>
                <p class="text-text-secondary text-sm font-medium">
                  {{ getYear(s.tanggal) }}
                </p>
              </div>
            </div>

            <!-- Details -->
            <div class="flex-1 p-6 flex flex-col gap-4">
              <div class="flex justify-between items-center flex-wrap gap-2">
                <span class="font-bold text-text-main">
                  {{
                    s.jenis === "sempro"
                      ? "Seminar Proposal"
                      : s.jenis === "semhas"
                        ? "Seminar Hasil"
                        : s.jenis || "Seminar"
                  }}
                </span>
                <span
                  :class="getJadwalStatusClass(s.status)"
                  class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold"
                >
                  <span class="material-symbols-outlined text-[14px]">{{
                    getJadwalStatusIcon(s.status)
                  }}</span>
                  {{ getJadwalStatusLabel(s.status) }}
                </span>
              </div>

              <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="flex items-start gap-3" v-if="s.waktu_mulai">
                  <div
                    class="flex items-center justify-center size-10 rounded-lg bg-sidebar-light text-primary"
                  >
                    <span class="material-symbols-outlined">schedule</span>
                  </div>
                  <div>
                    <p
                      class="text-xs text-text-secondary font-medium uppercase"
                    >
                      Waktu
                    </p>
                    <p class="font-semibold text-text-main">
                      {{ s.waktu_mulai
                      }}{{ s.waktu_selesai ? " - " + s.waktu_selesai : "" }} WIB
                    </p>
                  </div>
                </div>
                <div class="flex items-start gap-3" v-if="s.ruangan">
                  <div
                    class="flex items-center justify-center size-10 rounded-lg bg-sidebar-light text-primary"
                  >
                    <span class="material-symbols-outlined">location_on</span>
                  </div>
                  <div>
                    <p
                      class="text-xs text-text-secondary font-medium uppercase"
                    >
                      Lokasi
                    </p>
                    <p class="font-semibold text-text-main">
                      {{ s.ruangan }}
                    </p>
                  </div>
                </div>
              </div>

              <!-- Penguji -->
              <div
                v-if="s.penguji?.length"
                class="pt-4 border-t border-border-light"
              >
                <h4 class="font-bold text-sm text-text-main mb-3">
                  Dewan Penguji
                </h4>
                <div class="flex flex-wrap gap-3">
                  <div
                    v-for="p in s.penguji"
                    :key="p.id"
                    class="flex items-center gap-3 bg-sidebar-light px-4 py-2 rounded-lg border border-border-light"
                  >
                    <div
                      class="size-8 rounded-full bg-primary/10 flex items-center justify-center text-primary font-bold text-xs"
                    >
                      {{ getInitials(p.dosen?.nama) }}
                    </div>
                    <div>
                      <p class="text-xs font-bold text-text-main">
                        {{ p.dosen?.nama || "Unknown" }}
                      </p>
                      <p class="text-[10px] text-text-secondary">
                        {{ getPeranLabel(p.peran) }}
                      </p>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Sidang Skripsi Section -->
    <div>
      <h3 class="font-bold text-lg mb-4 flex items-center gap-2">
        <span class="material-symbols-outlined text-primary">school</span>
        Jadwal Sidang Skripsi
      </h3>

      <div
        v-if="!ujianList.length"
        class="bg-surface-light rounded-xl shadow-sm border border-border-light p-8 text-center"
      >
        <span
          class="material-symbols-outlined text-4xl text-text-secondary opacity-40"
          >event_busy</span
        >
        <p class="text-text-secondary text-sm mt-2">Belum ada jadwal sidang.</p>
      </div>

      <div v-else class="flex flex-col gap-4">
        <div
          v-for="u in ujianList"
          :key="u.id"
          class="bg-surface-light rounded-xl shadow-sm border border-border-light overflow-hidden p-6"
        >
          <div class="flex items-center gap-3 mb-4">
            <span
              :class="getJadwalStatusClass(u.status)"
              class="px-3 py-1 text-xs font-bold rounded-full uppercase tracking-wider"
              >{{ getJadwalStatusLabel(u.status) }}</span
            >
            <span class="text-text-secondary">•</span>
            <span class="text-sm text-text-secondary">Sidang Akhir</span>
          </div>

          <div class="grid grid-cols-1 sm:grid-cols-2 gap-y-6 gap-x-12">
            <div class="flex items-start gap-3" v-if="u.tanggal">
              <div class="p-2 bg-sidebar-light rounded-lg text-primary">
                <span class="material-symbols-outlined">calendar_today</span>
              </div>
              <div>
                <p
                  class="text-xs text-text-secondary uppercase font-bold tracking-wide"
                >
                  Tanggal
                </p>
                <p class="font-semibold text-text-main">
                  {{ formatDate(u.tanggal) }}
                </p>
              </div>
            </div>
            <div class="flex items-start gap-3" v-if="u.waktu_mulai">
              <div class="p-2 bg-sidebar-light rounded-lg text-primary">
                <span class="material-symbols-outlined">schedule</span>
              </div>
              <div>
                <p
                  class="text-xs text-text-secondary uppercase font-bold tracking-wide"
                >
                  Waktu
                </p>
                <p class="font-semibold text-text-main">
                  {{ u.waktu_mulai
                  }}{{ u.waktu_selesai ? " - " + u.waktu_selesai : "" }} WIB
                </p>
              </div>
            </div>
            <div class="flex items-start gap-3" v-if="u.ruangan">
              <div class="p-2 bg-sidebar-light rounded-lg text-primary">
                <span class="material-symbols-outlined">location_on</span>
              </div>
              <div>
                <p
                  class="text-xs text-text-secondary uppercase font-bold tracking-wide"
                >
                  Ruangan
                </p>
                <p class="font-semibold text-text-main">{{ u.ruangan }}</p>
              </div>
            </div>
          </div>

          <!-- Tim Penguji -->
          <div
            v-if="u.penguji?.length"
            class="mt-8 pt-6 border-t border-border-light"
          >
            <h4 class="font-bold text-sm text-text-main mb-4">Dewan Penguji</h4>
            <div class="flex flex-wrap gap-4">
              <div
                v-for="p in u.penguji"
                :key="p.id"
                class="flex items-center gap-3 bg-sidebar-light px-4 py-2 rounded-lg border border-border-light"
              >
                <div
                  class="size-8 rounded-full bg-primary/10 flex items-center justify-center text-primary font-bold text-xs"
                >
                  {{ getInitials(p.dosen?.nama) }}
                </div>
                <div>
                  <p class="text-xs font-bold text-text-main">
                    {{ p.dosen?.nama || "Unknown" }}
                  </p>
                  <p class="text-[10px] text-text-secondary">
                    {{ getPeranLabel(p.peran) }}
                  </p>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { inject, computed } from "vue";
import { useAuthStore } from "../../../stores/auth";

const authStore = useAuthStore();
const skripsi = inject("skripsi");

const seminarList = computed(() => {
  const types = ["sempro"];
  if (authStore.semhasEnabled) types.push("semhas");
  return (skripsi.value?.seminar || []).filter((s) => types.includes(s.jenis));
});
const ujianFromSeminar = computed(() =>
  (skripsi.value?.seminar || []).filter((s) => s.jenis === "sidang"),
);
const ujianList = computed(() => [
  ...ujianFromSeminar.value,
  ...(skripsi.value?.ujian || []),
]);

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
    ketua: "Ketua Penguji",
    sekretaris: "Sekretaris",
    penguji: "Penguji",
    penguji_1: "Penguji 1",
    penguji_2: "Penguji 2",
    pembimbing_1: "Pembimbing 1",
    pembimbing_2: "Pembimbing 2",
  };
  return map[peran] || peran || "Penguji";
};

const getJadwalStatusLabel = (status) => {
  const map = {
    terjadwal: "Terjadwal",
    selesai: "Selesai",
    batal: "Dibatalkan",
    menunggu_nilai: "Menunggu Nilai",
  };
  return map[status] || status || "Terjadwal";
};

const getJadwalStatusIcon = (status) => {
  const map = {
    terjadwal: "event",
    selesai: "check_circle",
    batal: "cancel",
    menunggu_nilai: "hourglass_top",
  };
  return map[status] || "event";
};

const getJadwalStatusClass = (status) => {
  const map = {
    terjadwal:
      "bg-blue-100 text-primary dark:bg-blue-900/30 dark:text-blue-300",
    selesai:
      "bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400",
    batal: "bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400",
    menunggu_nilai:
      "bg-yellow-100 text-yellow-700 dark:bg-yellow-900/30 dark:text-yellow-400",
  };
  return (
    map[status] ||
    "bg-blue-100 text-primary dark:bg-blue-900/30 dark:text-blue-300"
  );
};

const getMonth = (dateStr) => {
  if (!dateStr) return "-";
  return new Date(dateStr).toLocaleDateString("id-ID", { month: "short" });
};

const getDay = (dateStr) => {
  if (!dateStr) return "-";
  return new Date(dateStr).getDate();
};

const getYear = (dateStr) => {
  if (!dateStr) return "-";
  return new Date(dateStr).getFullYear();
};

const formatDate = (dateStr) => {
  if (!dateStr) return "-";
  return new Date(dateStr).toLocaleDateString("id-ID", {
    weekday: "long",
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
