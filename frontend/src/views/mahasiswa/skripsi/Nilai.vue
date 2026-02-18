<template>
  <div class="flex flex-col gap-6 animate-fade-in">
    <!-- Empty state -->
    <div
      v-if="!seminarsWithNilai.length && !ujianWithNilai.length"
      class="bg-surface-light rounded-xl shadow-sm border border-border-light p-12 flex flex-col items-center justify-center gap-3 text-center"
    >
      <span
        class="material-symbols-outlined text-5xl text-text-secondary opacity-40"
        >analytics</span
      >
      <h3 class="text-lg font-bold text-text-main">Belum Ada Nilai</h3>
      <p class="text-text-secondary text-sm">
        Nilai skripsi Anda belum tersedia. Nilai akan muncul setelah penguji
        memberikan penilaian.
      </p>
    </div>

    <template v-else>
      <!-- Sidang Skripsi Results -->
      <div
        v-for="ujian in ujianWithNilai"
        :key="'uj-' + ujian.id"
        class="bg-surface-light rounded-xl shadow-sm border border-border-light overflow-hidden"
      >
        <!-- Header -->
        <div
          class="bg-gradient-to-r from-primary/10 to-indigo-50 dark:from-primary/20 dark:to-indigo-900/20 px-6 py-4 border-b border-border-light"
        >
          <div class="flex items-center justify-between flex-wrap gap-3">
            <div class="flex items-center gap-3">
              <div
                class="bg-white dark:bg-surface-dark p-2 rounded-lg text-primary shadow-sm border border-primary/10"
              >
                <span class="material-symbols-outlined">school</span>
              </div>
              <div>
                <h3 class="text-lg font-bold text-text-main">
                  Nilai Sidang Skripsi
                </h3>
                <p class="text-xs text-text-secondary">
                  {{ formatDate(ujian.tanggal) }}
                </p>
              </div>
            </div>
            <div class="flex items-center gap-2">
              <span
                v-if="ujian.all_scored"
                class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-xs font-bold bg-green-100 text-green-700 border border-green-200"
              >
                <span class="material-symbols-outlined text-sm"
                  >check_circle</span
                >
                Selesai
              </span>
              <span
                v-else
                class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-xs font-bold bg-amber-100 text-amber-700 border border-amber-200"
              >
                <span class="material-symbols-outlined text-sm"
                  >hourglass_top</span
                >
                {{ ujian.scored_count }}/{{ ujian.total_penguji }} Penguji
              </span>
            </div>
          </div>
        </div>

        <div class="p-6">
          <!-- Final Grade Card (show when all scored) -->
          <div
            v-if="ujian.all_scored && ujian.nilai"
            class="bg-gradient-to-r from-slate-50 to-indigo-50 dark:from-slate-800/50 dark:to-indigo-900/30 rounded-xl p-6 border border-indigo-100 dark:border-indigo-800 mb-6"
          >
            <div class="flex items-center justify-around text-center">
              <div>
                <p
                  class="text-xs font-semibold uppercase tracking-wider text-text-secondary mb-1"
                >
                  Nilai Akhir
                </p>
                <p class="text-4xl font-black text-text-main">
                  {{ ujian.nilai }}
                </p>
              </div>
              <div>
                <p
                  class="text-xs font-semibold uppercase tracking-wider text-text-secondary mb-1"
                >
                  Grade
                </p>
                <span
                  class="inline-flex items-center justify-center size-14 rounded-xl text-2xl font-black"
                  :class="getGradeClass(ujian.grade)"
                  >{{ ujian.grade }}</span
                >
              </div>
              <div>
                <p
                  class="text-xs font-semibold uppercase tracking-wider text-text-secondary mb-1"
                >
                  Hasil
                </p>
                <span
                  class="inline-flex items-center gap-1 px-3 py-1.5 rounded-full text-sm font-bold"
                  :class="getHasilClass(ujian.hasil)"
                >
                  <span class="material-symbols-outlined text-base">{{
                    ujian.hasil === "lulus" || ujian.hasil === "lulus_revisi"
                      ? "verified"
                      : "cancel"
                  }}</span>
                  {{ getHasilLabel(ujian.hasil) }}
                </span>
              </div>
            </div>
          </div>

          <!-- Waiting state -->
          <div
            v-else-if="!ujian.all_scored"
            class="bg-amber-50 dark:bg-amber-900/20 rounded-xl p-4 border border-amber-200 dark:border-amber-800 mb-6 text-center"
          >
            <p class="text-sm text-amber-700 dark:text-amber-300 font-medium">
              <span
                class="material-symbols-outlined text-base align-middle mr-1"
                >hourglass_top</span
              >
              Menunggu penilaian dari semua penguji ({{ ujian.scored_count }}/{{
                ujian.total_penguji
              }}
              selesai)
            </p>
          </div>

          <!-- Penguji Scores Breakdown -->
          <div>
            <p
              class="text-xs font-bold uppercase tracking-wider text-text-secondary mb-3"
            >
              Nilai Per Penguji
            </p>
            <div class="space-y-3">
              <div
                v-for="p in ujian.penguji"
                :key="p.id"
                class="rounded-lg border border-border-light overflow-hidden"
              >
                <!-- Penguji Header -->
                <div
                  class="flex items-center justify-between px-4 py-3 bg-gray-50 dark:bg-surface-dark"
                >
                  <div class="flex items-center gap-2">
                    <div
                      class="bg-orange-100 size-7 rounded-full flex items-center justify-center text-orange-600 border border-orange-200"
                    >
                      <span class="material-symbols-outlined text-[14px]"
                        >school</span
                      >
                    </div>
                    <div>
                      <p class="text-sm font-bold text-text-main">
                        {{ p.dosen?.full_name || p.dosen?.nama || "-" }}
                      </p>
                      <p class="text-[10px] text-text-secondary">
                        {{ getPeranLabel(p.peran) }}
                      </p>
                    </div>
                  </div>
                  <div v-if="p.nilai !== null" class="text-right">
                    <p class="text-xs text-text-secondary">Rata-rata</p>
                    <p class="text-lg font-black text-primary">
                      {{ p.nilai }}
                    </p>
                  </div>
                  <span v-else class="text-xs text-text-secondary italic"
                    >Belum dinilai</span
                  >
                </div>

                <!-- Component Scores -->
                <div v-if="p.nilai !== null" class="px-4 py-3">
                  <div class="grid grid-cols-4 gap-2 text-center">
                    <div
                      class="bg-gray-50 dark:bg-surface-dark rounded-lg px-2 py-2"
                    >
                      <p class="text-[10px] text-text-secondary font-medium">
                        Metodologi & Teknik
                      </p>
                      <p class="text-sm font-bold text-text-main">
                        {{ p.nilai_mt ?? "-" }}
                      </p>
                    </div>
                    <div
                      class="bg-gray-50 dark:bg-surface-dark rounded-lg px-2 py-2"
                    >
                      <p class="text-[10px] text-text-secondary font-medium">
                        Materi Skripsi
                      </p>
                      <p class="text-sm font-bold text-text-main">
                        {{ p.nilai_ms ?? "-" }}
                      </p>
                    </div>
                    <div
                      class="bg-gray-50 dark:bg-surface-dark rounded-lg px-2 py-2"
                    >
                      <p class="text-[10px] text-text-secondary font-medium">
                        Penampilan Mhs
                      </p>
                      <p class="text-sm font-bold text-text-main">
                        {{ p.nilai_pm ?? "-" }}
                      </p>
                    </div>
                    <div
                      class="bg-gray-50 dark:bg-surface-dark rounded-lg px-2 py-2"
                    >
                      <p class="text-[10px] text-text-secondary font-medium">
                        Penguasaan Isi
                      </p>
                      <p class="text-sm font-bold text-text-main">
                        {{ p.nilai_pi ?? "-" }}
                      </p>
                    </div>
                  </div>
                  <!-- Catatan -->
                  <div
                    v-if="p.catatan"
                    class="mt-2 bg-blue-50 dark:bg-blue-900/20 rounded-lg px-3 py-2 border border-blue-100 dark:border-blue-800"
                  >
                    <p class="text-xs text-text-secondary font-medium mb-0.5">
                      Catatan:
                    </p>
                    <p class="text-sm text-text-main">{{ p.catatan }}</p>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Seminar Proposal / Seminar Hasil -->
      <div
        v-for="seminar in seminarsWithNilai"
        :key="'sem-' + seminar.id"
        class="bg-surface-light rounded-xl shadow-sm border border-border-light overflow-hidden"
      >
        <div
          class="bg-gradient-to-r from-blue-50 to-indigo-50 dark:from-blue-900/20 dark:to-indigo-900/20 px-6 py-4 border-b border-border-light"
        >
          <div class="flex items-center justify-between flex-wrap gap-3">
            <div class="flex items-center gap-3">
              <div
                class="bg-white dark:bg-surface-dark p-2 rounded-lg text-primary shadow-sm border border-primary/10"
              >
                <span class="material-symbols-outlined">description</span>
              </div>
              <div>
                <h3 class="text-lg font-bold text-text-main">
                  Nilai {{ getSeminarLabel(seminar.jenis) }}
                </h3>
                <p class="text-xs text-text-secondary">
                  {{ formatDate(seminar.tanggal) }}
                </p>
              </div>
            </div>
            <span
              v-if="seminar.all_scored"
              class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-xs font-bold bg-green-100 text-green-700 border border-green-200"
            >
              <span class="material-symbols-outlined text-sm"
                >check_circle</span
              >
              Selesai
            </span>
            <span
              v-else
              class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-xs font-bold bg-amber-100 text-amber-700 border border-amber-200"
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
              Nilai Penguji
            </p>
            <div
              v-for="p in seminar.penguji"
              :key="p.id"
              class="flex items-center justify-between bg-gray-50 dark:bg-surface-dark rounded-lg px-4 py-2.5 border border-border-light"
            >
              <div class="flex items-center gap-2">
                <div
                  class="bg-orange-100 size-6 rounded-full flex items-center justify-center text-orange-600"
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
    </template>
  </div>
</template>

<script setup>
import { inject, computed } from "vue";

const skripsi = inject("skripsi");

// Ujian (Sidang Skripsi) with any scoring activity
const ujianWithNilai = computed(() => {
  const ujians = skripsi.value?.ujian || [];
  return ujians.filter(
    (u) =>
      (u.penguji?.length > 0 && u.scored_count > 0) || u.status === "selesai",
  );
});

// Seminars (Sempro + Semhas) with any scoring activity
const seminarsWithNilai = computed(() => {
  const seminars = skripsi.value?.seminar || [];
  return seminars.filter(
    (s) =>
      (s.penguji?.length > 0 && s.scored_count > 0) || s.status === "selesai",
  );
});

const getPeranLabel = (peran) => {
  const map = {
    ketua: "Ketua Penguji",
    penguji_1: "Penguji 1",
    penguji_2: "Penguji 2",
    pembimbing_1: "Pembimbing 1",
    pembimbing_2: "Pembimbing 2",
  };
  return map[peran] || peran || "Penguji";
};

const getSeminarLabel = (jenis) => {
  const map = {
    sempro: "Seminar Proposal",
    semhas: "Seminar Hasil",
  };
  return map[jenis] || jenis || "Seminar";
};

const getGradeClass = (grade) => {
  const classes = {
    A: "bg-green-100 text-green-700 border border-green-200",
    "B+": "bg-emerald-100 text-emerald-700 border border-emerald-200",
    B: "bg-blue-100 text-blue-700 border border-blue-200",
    "C+": "bg-yellow-100 text-yellow-700 border border-yellow-200",
    C: "bg-orange-100 text-orange-700 border border-orange-200",
    D: "bg-red-100 text-red-700 border border-red-200",
  };
  return classes[grade] || "bg-gray-100 text-gray-600";
};

const getHasilClass = (hasil) => {
  const map = {
    lulus: "bg-green-100 text-green-700",
    lulus_revisi: "bg-yellow-100 text-yellow-700",
    tidak_lulus: "bg-red-100 text-red-700",
  };
  return map[hasil] || "bg-gray-100 text-gray-600";
};

const getHasilLabel = (hasil) => {
  const map = {
    lulus: "Lulus",
    lulus_revisi: "Lulus (Revisi)",
    tidak_lulus: "Tidak Lulus",
  };
  return map[hasil] || hasil || "-";
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
