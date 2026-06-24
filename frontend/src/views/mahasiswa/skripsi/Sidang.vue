<template>
  <div class="flex flex-col gap-6 animate-fade-in">
    <!-- Info Banner -->
    <div
      class="flex items-start gap-3 p-4 bg-purple-50 dark:bg-purple-900/10 rounded-xl border border-purple-200 dark:border-purple-800"
    >
      <span class="material-symbols-outlined text-purple-500 mt-0.5"
        >info</span
      >
      <div>
        <p class="text-sm font-bold text-purple-700 dark:text-purple-300">
          Ujian Sidang Skripsi
        </p>
        <p class="text-xs text-purple-600/80 dark:text-purple-400/80 mt-0.5">
          Halaman ini menampilkan jadwal dan hasil ujian sidang skripsi Anda,
          termasuk seminar hasil (jika ada). Pastikan Anda hadir sesuai jadwal
          yang telah ditentukan.
        </p>
      </div>
    </div>

    <!-- ===== SEMINAR HASIL (if enabled) ===== -->
    <div v-if="authStore.semhasEnabled">
      <h3 class="font-bold text-lg mb-4 flex items-center gap-2">
        <span class="material-symbols-outlined text-primary">description</span>
        Seminar Hasil
      </h3>

      <div
        v-if="!semhasList.length"
        class="bg-surface-light rounded-xl shadow-sm border border-border-light p-8 text-center"
      >
        <span
          class="material-symbols-outlined text-4xl text-text-secondary opacity-40"
          >event_busy</span
        >
        <p class="text-text-secondary text-sm mt-2">
          Belum ada jadwal seminar hasil. Jadwal akan muncul setelah admin
          menjadwalkan seminar hasil Anda.
        </p>
      </div>

      <div v-else class="flex flex-col gap-4">
        <!-- Semhas Schedule -->
        <div
          v-for="s in semhasList"
          :key="'sh-' + s.id"
          class="bg-surface-light rounded-xl shadow-sm border border-border-light overflow-hidden"
        >
          <div class="flex flex-col xl:flex-row w-full">
            <div
              class="w-full xl:w-1/5 bg-gradient-to-br from-orange-500/10 to-orange-500/5 flex items-center justify-center p-6"
            >
              <div
                class="bg-white/90 dark:bg-white/10 p-4 rounded-xl text-center shadow-md"
              >
                <p
                  class="text-orange-600 text-xs font-bold uppercase tracking-widest"
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
            <div class="flex-1 p-6 flex flex-col gap-4">
              <div
                class="flex justify-between items-center flex-wrap gap-2"
              >
                <span class="font-bold text-text-main">Seminar Hasil</span>
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
                    <p class="font-semibold text-text-main">{{ s.ruangan }}</p>
                  </div>
                </div>
              </div>
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

        <!-- Semhas Nilai -->
        <div
          v-for="seminar in semhasWithNilai"
          :key="'shv-' + seminar.id"
          class="bg-surface-light rounded-xl shadow-sm border border-border-light overflow-hidden"
        >
          <div
            class="bg-gradient-to-r from-orange-50 to-amber-50 dark:from-orange-900/20 dark:to-amber-900/20 px-6 py-4 border-b border-border-light"
          >
            <div class="flex items-center justify-between flex-wrap gap-3">
              <div class="flex items-center gap-3">
                <div
                  class="bg-white dark:bg-white/5 p-2 rounded-lg text-orange-600 shadow-sm border border-orange-100 dark:border-orange-800"
                >
                  <span class="material-symbols-outlined">description</span>
                </div>
                <div>
                  <h3 class="text-lg font-bold text-text-main">
                    Nilai Seminar Hasil
                  </h3>
                  <p class="text-xs text-text-secondary">
                    {{ formatDate(seminar.tanggal) }}
                  </p>
                </div>
              </div>
              <NilaiStatusBadge :seminar="seminar" />
            </div>
          </div>
          <NilaiBody :seminar="seminar" />
        </div>
      </div>
    </div>

    <!-- ===== JADWAL SIDANG SKRIPSI ===== -->
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
        <p class="text-text-secondary text-sm mt-2">
          Belum ada jadwal sidang. Jadwal akan muncul setelah pengajuan sidang
          Anda disetujui dan admin menjadwalkan ujian.
        </p>
      </div>

      <div v-else class="flex flex-col gap-4">
        <div
          v-for="u in ujianList"
          :key="'uj-' + u.id"
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
                  {{ formatDateLong(u.tanggal) }}
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
            <h4 class="font-bold text-sm text-text-main mb-4">
              Dewan Penguji
            </h4>
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

    <!-- ===== NILAI SIDANG ===== -->
    <div>
      <h3 class="font-bold text-lg mb-4 flex items-center gap-2">
        <span class="material-symbols-outlined text-primary">analytics</span>
        Nilai Sidang Skripsi
      </h3>

      <div
        v-if="!ujianWithNilai.length"
        class="bg-surface-light rounded-xl shadow-sm border border-border-light p-8 text-center"
      >
        <span
          class="material-symbols-outlined text-4xl text-text-secondary opacity-40"
          >analytics</span
        >
        <p class="text-text-secondary text-sm mt-2">
          Nilai sidang belum tersedia. Nilai akan muncul setelah penguji
          memberikan penilaian pada saat ujian sidang skripsi.
        </p>
      </div>

      <div v-else class="flex flex-col gap-4">
        <div
          v-for="ujian in ujianWithNilai"
          :key="'ujv-' + ujian.id"
          class="bg-surface-light rounded-xl shadow-sm border border-border-light overflow-hidden"
        >
          <div
            class="bg-gradient-to-r from-primary/10 to-indigo-50 dark:from-primary/20 dark:to-indigo-900/20 px-6 py-4 border-b border-border-light"
          >
            <div class="flex items-center justify-between flex-wrap gap-3">
              <div class="flex items-center gap-3">
                <div
                  class="bg-white dark:bg-white/5 p-2 rounded-lg text-primary shadow-sm border border-primary/10"
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
              <span
                v-if="ujian.all_scored"
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
                {{ ujian.scored_count }}/{{ ujian.total_penguji }} Penguji
              </span>
            </div>
          </div>

          <div class="p-6">
            <!-- Final Grade Card -->
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

            <!-- Penguji Scores -->
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
                  <div
                    class="flex items-center justify-between px-4 py-3 bg-gray-50 dark:bg-white/5"
                  >
                    <div class="flex items-center gap-2">
                      <div
                        class="bg-orange-100 dark:bg-orange-900/30 size-7 rounded-full flex items-center justify-center text-orange-600 border border-orange-200 dark:border-orange-800"
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

                  <div v-if="p.nilai !== null" class="px-4 py-3">
                    <div class="grid grid-cols-4 gap-2 text-center">
                      <div
                        class="bg-gray-50 dark:bg-white/5 rounded-lg px-2 py-2"
                      >
                        <p
                          class="text-[10px] text-text-secondary font-medium"
                        >
                          Metodologi & Teknik
                        </p>
                        <p class="text-sm font-bold text-text-main">
                          {{ p.nilai_mt ?? "-" }}
                        </p>
                      </div>
                      <div
                        class="bg-gray-50 dark:bg-white/5 rounded-lg px-2 py-2"
                      >
                        <p
                          class="text-[10px] text-text-secondary font-medium"
                        >
                          Materi Skripsi
                        </p>
                        <p class="text-sm font-bold text-text-main">
                          {{ p.nilai_ms ?? "-" }}
                        </p>
                      </div>
                      <div
                        class="bg-gray-50 dark:bg-white/5 rounded-lg px-2 py-2"
                      >
                        <p
                          class="text-[10px] text-text-secondary font-medium"
                        >
                          Penampilan Mhs
                        </p>
                        <p class="text-sm font-bold text-text-main">
                          {{ p.nilai_pm ?? "-" }}
                        </p>
                      </div>
                      <div
                        class="bg-gray-50 dark:bg-white/5 rounded-lg px-2 py-2"
                      >
                        <p
                          class="text-[10px] text-text-secondary font-medium"
                        >
                          Penguasaan Isi
                        </p>
                        <p class="text-sm font-bold text-text-main">
                          {{ p.nilai_pi ?? "-" }}
                        </p>
                      </div>
                    </div>
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
      </div>
    </div>
  </div>
</template>

<script setup>
import { inject, computed } from "vue";
import { useAuthStore } from "../../../stores/auth";

const authStore = useAuthStore();
const skripsi = inject("skripsi");

// Inline sub-components
const NilaiStatusBadge = {
  props: ["seminar"],
  template: `
    <span v-if="seminar.all_scored" class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-xs font-bold bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-400 border border-green-200 dark:border-green-800">
      <span class="material-symbols-outlined text-sm">check_circle</span> Selesai
    </span>
    <span v-else class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-xs font-bold bg-amber-100 dark:bg-amber-900/30 text-amber-700 dark:text-amber-400 border border-amber-200 dark:border-amber-800">
      <span class="material-symbols-outlined text-sm">hourglass_top</span>
      {{ seminar.scored_count }}/{{ seminar.total_penguji }} Penguji
    </span>
  `,
};

const NilaiBody = {
  props: ["seminar"],
  setup(props) {
    const getPeranLabel = (peran) => {
      const map = { ketua: "Ketua", penguji_1: "Penguji 1", penguji_2: "Penguji 2", pembimbing_1: "Pembimbing 1", pembimbing_2: "Pembimbing 2" };
      return map[peran] || peran || "Penguji";
    };
    return { getPeranLabel };
  },
  template: `
    <div class="p-6">
      <div v-if="seminar.penguji?.length" class="space-y-2">
        <p class="text-xs font-bold uppercase tracking-wider text-text-secondary mb-2">Nilai Penguji</p>
        <div v-for="p in seminar.penguji" :key="p.id" class="flex items-center justify-between bg-gray-50 dark:bg-white/5 rounded-lg px-4 py-2.5 border border-border-light">
          <div class="flex items-center gap-2">
            <div class="bg-orange-100 dark:bg-orange-900/30 size-6 rounded-full flex items-center justify-center text-orange-600">
              <span class="material-symbols-outlined text-[12px]">school</span>
            </div>
            <span class="text-sm font-medium text-text-main">{{ p.dosen?.full_name || p.dosen?.nama || '-' }}</span>
            <span class="text-[9px] px-1.5 py-0.5 rounded bg-gray-200 dark:bg-gray-700 text-text-secondary">{{ getPeranLabel(p.peran) }}</span>
          </div>
          <span v-if="p.nilai !== null" class="text-sm font-black text-primary">{{ p.nilai }}</span>
          <span v-else class="text-xs text-text-secondary italic">Belum dinilai</span>
        </div>
      </div>
    </div>
  `,
};

// Data
const semhasList = computed(() =>
  (skripsi.value?.seminar || []).filter((s) => s.jenis === "semhas"),
);

const semhasWithNilai = computed(() =>
  semhasList.value.filter(
    (s) =>
      (s.penguji?.length > 0 && s.scored_count > 0) || s.status === "selesai",
  ),
);

const ujianFromSeminar = computed(() =>
  (skripsi.value?.seminar || []).filter((s) => s.jenis === "sidang"),
);
const ujianList = computed(() => [
  ...ujianFromSeminar.value,
  ...(skripsi.value?.ujian || []),
]);

const ujianWithNilai = computed(() => {
  const ujians = skripsi.value?.ujian || [];
  return ujians.filter(
    (u) =>
      (u.penguji?.length > 0 && u.scored_count > 0) || u.status === "selesai",
  );
});

// Helpers
const getInitials = (nama) => {
  if (!nama) return "??";
  return nama.split(" ").map((w) => w[0]).join("").toUpperCase().slice(0, 2);
};

const getPeranLabel = (peran) => {
  const map = {
    ketua: "Ketua Penguji", sekretaris: "Sekretaris", penguji: "Penguji",
    penguji_1: "Penguji 1", penguji_2: "Penguji 2",
    pembimbing_1: "Pembimbing 1", pembimbing_2: "Pembimbing 2",
  };
  return map[peran] || peran || "Penguji";
};

const getJadwalStatusLabel = (status) => {
  const map = { terjadwal: "Terjadwal", selesai: "Selesai", batal: "Dibatalkan", menunggu_nilai: "Menunggu Nilai" };
  return map[status] || status || "Terjadwal";
};

const getJadwalStatusIcon = (status) => {
  const map = { terjadwal: "event", selesai: "check_circle", batal: "cancel", menunggu_nilai: "hourglass_top" };
  return map[status] || "event";
};

const getJadwalStatusClass = (status) => {
  const map = {
    terjadwal: "bg-blue-100 text-primary dark:bg-blue-900/30 dark:text-blue-300",
    selesai: "bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400",
    batal: "bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400",
    menunggu_nilai: "bg-yellow-100 text-yellow-700 dark:bg-yellow-900/30 dark:text-yellow-400",
  };
  return map[status] || "bg-blue-100 text-primary dark:bg-blue-900/30 dark:text-blue-300";
};

const getGradeClass = (grade) => {
  const classes = {
    A: "bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-400 border border-green-200 dark:border-green-800",
    "B+": "bg-emerald-100 dark:bg-emerald-900/30 text-emerald-700 dark:text-emerald-400 border border-emerald-200 dark:border-emerald-800",
    B: "bg-blue-100 dark:bg-blue-900/30 text-blue-700 dark:text-blue-400 border border-blue-200 dark:border-blue-800",
    "C+": "bg-yellow-100 dark:bg-yellow-900/30 text-yellow-700 dark:text-yellow-400 border border-yellow-200 dark:border-yellow-800",
    C: "bg-orange-100 dark:bg-orange-900/30 text-orange-700 dark:text-orange-400 border border-orange-200 dark:border-orange-800",
    D: "bg-red-100 dark:bg-red-900/30 text-red-700 dark:text-red-400 border border-red-200 dark:border-red-800",
  };
  return classes[grade] || "bg-gray-100 dark:bg-gray-800 text-gray-600 dark:text-gray-400";
};

const getHasilClass = (hasil) => {
  const map = {
    lulus: "bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-400",
    lulus_revisi: "bg-yellow-100 dark:bg-yellow-900/30 text-yellow-700 dark:text-yellow-400",
    tidak_lulus: "bg-red-100 dark:bg-red-900/30 text-red-700 dark:text-red-400",
  };
  return map[hasil] || "bg-gray-100 dark:bg-gray-800 text-gray-600 dark:text-gray-400";
};

const getHasilLabel = (hasil) => {
  const map = { lulus: "Lulus", lulus_revisi: "Lulus (Revisi)", tidak_lulus: "Tidak Lulus" };
  return map[hasil] || hasil || "-";
};

const getMonth = (dateStr) => {
  if (!dateStr) return "-";
  return new Date(dateStr).toLocaleDateString("id-ID", { month: "short" });
};
const getDay = (dateStr) => (!dateStr ? "-" : new Date(dateStr).getDate());
const getYear = (dateStr) => (!dateStr ? "-" : new Date(dateStr).getFullYear());

const formatDate = (dateStr) => {
  if (!dateStr) return "-";
  return new Date(dateStr).toLocaleDateString("id-ID", { day: "numeric", month: "long", year: "numeric" });
};

const formatDateLong = (dateStr) => {
  if (!dateStr) return "-";
  return new Date(dateStr).toLocaleDateString("id-ID", { weekday: "long", day: "numeric", month: "long", year: "numeric" });
};
</script>

<style scoped>
.animate-fade-in {
  animation: fadeIn 0.3s ease-in-out;
}
@keyframes fadeIn {
  from { opacity: 0; transform: translateY(5px); }
  to { opacity: 1; transform: translateY(0); }
}
</style>
