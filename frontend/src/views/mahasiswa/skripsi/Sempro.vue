<template>
  <div class="flex flex-col gap-6 animate-fade-in">
    <!-- Info Banner -->
    <div
      class="flex items-start gap-3 p-4 bg-blue-50 dark:bg-blue-900/10 rounded-xl border border-blue-200 dark:border-blue-800"
    >
      <span class="material-symbols-outlined text-blue-500 mt-0.5">info</span>
      <div>
        <p class="text-sm font-bold text-blue-700 dark:text-blue-300">
          Seminar Proposal (Sempro)
        </p>
        <p class="text-xs text-blue-600/80 dark:text-blue-400/80 mt-0.5">
          Seminar proposal adalah tahap presentasi proposal skripsi di hadapan
          dewan penguji. Di sini Anda dapat melihat jadwal sempro dan hasil
          nilai dari penguji.
        </p>
      </div>
    </div>

    <!-- ===== JADWAL SEMPRO ===== -->
    <div>
      <h3 class="font-bold text-lg mb-4 flex items-center gap-2">
        <span class="material-symbols-outlined text-primary">event</span>
        Jadwal Seminar Proposal
      </h3>

      <div
        v-if="!semproList.length"
        class="bg-surface-light rounded-xl shadow-sm border border-border-light p-8 text-center"
      >
        <span
          class="material-symbols-outlined text-4xl text-text-secondary opacity-40"
          >event_busy</span
        >
        <p class="text-text-secondary text-sm mt-2">
          Belum ada jadwal seminar proposal. Jadwal akan muncul setelah admin
          menjadwalkan sempro Anda.
        </p>
      </div>

      <div v-else class="flex flex-col gap-4">
        <div
          v-for="s in semproList"
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
              <div
                class="flex justify-between items-center flex-wrap gap-2"
              >
                <span class="font-bold text-text-main">
                  Seminar Proposal
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
                      }}{{ s.waktu_selesai ? " - " + s.waktu_selesai : "" }}
                      WIB
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

    <!-- ===== NILAI SEMPRO ===== -->
    <div>
      <h3 class="font-bold text-lg mb-4 flex items-center gap-2">
        <span class="material-symbols-outlined text-primary">analytics</span>
        Nilai Seminar Proposal
      </h3>

      <div
        v-if="!semproWithNilai.length"
        class="bg-surface-light rounded-xl shadow-sm border border-border-light p-8 text-center"
      >
        <span
          class="material-symbols-outlined text-4xl text-text-secondary opacity-40"
          >analytics</span
        >
        <p class="text-text-secondary text-sm mt-2">
          Nilai sempro belum tersedia. Nilai akan muncul setelah penguji selesai
          memberikan penilaian.
        </p>
      </div>

      <div v-else class="flex flex-col gap-4">
        <div
          v-for="seminar in semproWithNilai"
          :key="'sem-' + seminar.id"
          class="bg-surface-light rounded-xl shadow-sm border border-border-light overflow-hidden"
        >
          <!-- Header -->
          <div
            class="bg-gradient-to-r from-blue-50 to-indigo-50 dark:from-blue-900/20 dark:to-indigo-900/20 px-6 py-4 border-b border-border-light"
          >
            <div
              class="flex items-center justify-between flex-wrap gap-3"
            >
              <div class="flex items-center gap-3">
                <div
                  class="bg-white dark:bg-white/5 p-2 rounded-lg text-primary shadow-sm border border-primary/10"
                >
                  <span class="material-symbols-outlined">description</span>
                </div>
                <div>
                  <h3 class="text-lg font-bold text-text-main">
                    Nilai Seminar Proposal
                  </h3>
                  <p class="text-xs text-text-secondary">
                    {{ formatDate(seminar.tanggal) }}
                  </p>
                </div>
              </div>
              <span
                v-if="seminar.all_scored"
                class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-xs font-bold bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-400 border border-green-200 dark:border-green-800"
              >
                <span class="material-symbols-outlined text-sm"
                  >check_circle</span
                >
                Selesai
              </span>
              <span
                v-else
                class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-xs font-bold bg-amber-100 dark:bg-amber-900/30 text-amber-700 dark:text-amber-400 border border-amber-200 dark:border-amber-800"
              >
                <span class="material-symbols-outlined text-sm"
                  >hourglass_top</span
                >
                {{ seminar.scored_count }}/{{ seminar.total_penguji }} Penguji
              </span>
            </div>
          </div>

          <div class="p-6">
            <!-- Final result -->
            <div
              v-if="seminar.all_scored && seminar.nilai"
              class="bg-gradient-to-r from-slate-50 to-blue-50 dark:from-slate-800/50 dark:to-blue-900/30 rounded-xl p-4 border border-blue-100 dark:border-blue-800 mb-4"
            >
              <div class="flex items-center justify-around text-center">
                <div>
                  <p class="text-xs text-text-secondary font-medium">
                    Nilai Rata-rata
                  </p>
                  <p class="text-2xl font-black text-text-main">
                    {{ seminar.nilai }}
                  </p>
                </div>
                <div>
                  <p class="text-xs text-text-secondary font-medium">Grade</p>
                  <span
                    class="inline-flex items-center justify-center size-10 rounded-xl text-base font-black"
                    :class="getGradeClass(seminar.grade)"
                    >{{ seminar.grade }}</span
                  >
                </div>
                <div v-if="seminar.hasil">
                  <p class="text-xs text-text-secondary font-medium">Hasil</p>
                  <span
                    class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-bold"
                    :class="getHasilClass(seminar.hasil)"
                  >
                    {{ getHasilLabel(seminar.hasil) }}
                  </span>
                </div>
              </div>
            </div>

            <!-- Penguji scores -->
            <div v-if="seminar.penguji?.length" class="space-y-2">
              <p
                class="text-xs font-bold uppercase tracking-wider text-text-secondary mb-2"
              >
                Nilai Per Penguji
              </p>
              <div
                v-for="p in seminar.penguji"
                :key="p.id"
                class="flex items-center justify-between bg-gray-50 dark:bg-white/5 rounded-lg px-4 py-2.5 border border-border-light"
              >
                <div class="flex items-center gap-2">
                  <div
                    class="bg-orange-100 dark:bg-orange-900/30 size-6 rounded-full flex items-center justify-center text-orange-600"
                  >
                    <span class="material-symbols-outlined text-[12px]"
                      >school</span
                    >
                  </div>
                  <span class="text-sm font-medium text-text-main">{{
                    p.dosen?.full_name || p.dosen?.nama || "-"
                  }}</span>
                  <span
                    class="text-[9px] px-1.5 py-0.5 rounded bg-gray-200 dark:bg-gray-700 text-text-secondary"
                    >{{ getPeranLabel(p.peran) }}</span
                  >
                </div>
                <span
                  v-if="p.nilai !== null"
                  class="text-sm font-black text-primary"
                  >{{ p.nilai }}</span
                >
                <span v-else class="text-xs text-text-secondary italic"
                  >Belum dinilai</span
                >
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

const skripsi = inject("skripsi");

const semproList = computed(() =>
  (skripsi.value?.seminar || []).filter((s) => s.jenis === "sempro"),
);

const semproWithNilai = computed(() =>
  semproList.value.filter(
    (s) =>
      (s.penguji?.length > 0 && s.scored_count > 0) || s.status === "selesai",
  ),
);

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

const getGradeClass = (grade) => {
  const classes = {
    A: "bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-400 border border-green-200 dark:border-green-800",
    "B+":
      "bg-emerald-100 dark:bg-emerald-900/30 text-emerald-700 dark:text-emerald-400 border border-emerald-200 dark:border-emerald-800",
    B: "bg-blue-100 dark:bg-blue-900/30 text-blue-700 dark:text-blue-400 border border-blue-200 dark:border-blue-800",
    "C+":
      "bg-yellow-100 dark:bg-yellow-900/30 text-yellow-700 dark:text-yellow-400 border border-yellow-200 dark:border-yellow-800",
    C: "bg-orange-100 dark:bg-orange-900/30 text-orange-700 dark:text-orange-400 border border-orange-200 dark:border-orange-800",
    D: "bg-red-100 dark:bg-red-900/30 text-red-700 dark:text-red-400 border border-red-200 dark:border-red-800",
  };
  return (
    classes[grade] ||
    "bg-gray-100 dark:bg-gray-800 text-gray-600 dark:text-gray-400"
  );
};

const getHasilClass = (hasil) => {
  const map = {
    lulus:
      "bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-400",
    lulus_revisi:
      "bg-yellow-100 dark:bg-yellow-900/30 text-yellow-700 dark:text-yellow-400",
    lulus_bersyarat:
      "bg-yellow-100 dark:bg-yellow-900/30 text-yellow-700 dark:text-yellow-400",
    tidak_lulus:
      "bg-red-100 dark:bg-red-900/30 text-red-700 dark:text-red-400",
  };
  return (
    map[hasil] ||
    "bg-gray-100 dark:bg-gray-800 text-gray-600 dark:text-gray-400"
  );
};

const getHasilLabel = (hasil) => {
  const map = {
    lulus: "Lulus",
    lulus_revisi: "Lulus (Revisi)",
    lulus_bersyarat: "Lulus Bersyarat",
    tidak_lulus: "Tidak Lulus",
  };
  return map[hasil] || hasil || "-";
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
