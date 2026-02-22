<template>
  <div class="flex flex-col gap-6">
    <!-- Loading State -->
    <div v-if="loading" class="flex flex-col gap-6 animate-pulse">
      <div class="h-10 w-80 bg-gray-200 dark:bg-gray-700 rounded-lg"></div>
      <div class="h-5 w-64 bg-gray-200 dark:bg-gray-700 rounded-lg"></div>
      <div class="h-56 bg-gray-200 dark:bg-gray-700 rounded-xl"></div>
      <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div
          class="lg:col-span-2 h-64 bg-gray-200 dark:bg-gray-700 rounded-xl"
        ></div>
        <div class="h-64 bg-gray-200 dark:bg-gray-700 rounded-xl"></div>
      </div>
    </div>

    <!-- No Skripsi State -->
    <template v-else-if="!skripsi">
      <section class="flex flex-col gap-1">
        <h2 class="text-3xl font-bold tracking-tight text-text-main">
          Selamat Datang, {{ mahasiswa?.nama || "Mahasiswa" }}
        </h2>
        <p class="text-text-secondary text-base">
          Pantau progres skripsi Anda dan selesaikan tahapan berikutnya.
        </p>
      </section>
      <div
        class="bg-surface-light rounded-xl shadow-sm border border-border-light p-12 flex flex-col items-center justify-center gap-4 text-center"
      >
        <span
          class="material-symbols-outlined text-6xl text-text-secondary opacity-40"
          >menu_book</span
        >
        <h3 class="text-xl font-bold text-text-main">
          Belum Ada Skripsi Aktif
        </h3>
        <p class="text-text-secondary max-w-md">
          Anda belum memiliki skripsi aktif. Silakan ajukan judul skripsi
          melalui halaman Skripsi Saya.
        </p>
        <router-link
          to="/mahasiswa/skripsi"
          class="mt-2 inline-flex items-center gap-2 bg-primary hover:bg-blue-600 text-white font-bold px-6 py-3 rounded-lg transition-all shadow-md shadow-primary/20"
        >
          <span class="material-symbols-outlined text-[20px]">add</span>
          Ajukan Skripsi
        </router-link>
      </div>
    </template>

    <!-- Main Dashboard Content -->
    <template v-else>
      <!-- Page Heading -->
      <section class="flex flex-col gap-1">
        <h2 class="text-3xl font-bold tracking-tight text-text-main">
          Selamat Datang, {{ mahasiswa?.nama || "Mahasiswa" }}
        </h2>
        <p class="text-text-secondary text-base">
          Pantau progres skripsi Anda dan selesaikan tahapan berikutnya.
        </p>
      </section>

      <!-- Thesis Summary Card -->
      <section
        class="bg-surface-light rounded-xl shadow-sm border border-border-light overflow-hidden transition-all hover:shadow-md"
      >
        <div class="p-6 md:p-8 flex flex-col md:flex-row gap-8">
          <!-- Left: Info & Title -->
          <div class="flex-1 flex flex-col gap-4">
            <div class="flex items-center gap-3">
              <span
                class="px-3 py-1 rounded-full bg-blue-50 dark:bg-blue-900/30 text-primary text-xs font-bold uppercase tracking-wide"
              >
                Skripsi Anda
              </span>
              <div class="flex items-center gap-1 text-text-secondary text-xs">
                <span class="material-symbols-outlined text-[16px]"
                  >schedule</span
                >
                <span>Terakhir update: {{ lastUpdated }}</span>
              </div>
            </div>
            <div>
              <h3 class="text-2xl font-bold leading-snug text-text-main mb-2">
                {{ skripsi.judul }}
              </h3>
              <p class="text-text-secondary text-sm" v-if="pembimbingNames">
                Pembimbing: {{ pembimbingNames }}
              </p>
            </div>
            <div class="mt-auto pt-4 flex items-center gap-2">
              <div
                :class="statusBadgeClass"
                class="inline-flex items-center gap-2 px-3 py-1.5 rounded-lg text-sm font-semibold"
              >
                <span
                  class="material-symbols-outlined text-[18px] filled"
                  :class="statusIconClass"
                  >{{ statusIcon }}</span
                >
                Status: {{ statusLabel }}
              </div>
            </div>
          </div>

          <!-- Right: Visual Progress -->
          <div
            class="flex-1 md:max-w-[320px] bg-sidebar-light rounded-xl p-5 flex flex-col justify-center gap-4 border border-border-light"
          >
            <div class="flex justify-between items-end mb-1">
              <span class="text-sm font-semibold text-text-main"
                >Progres Keseluruhan</span
              >
              <span class="text-2xl font-bold text-primary"
                >{{ progress }}%</span
              >
            </div>
            <!-- Progress Track -->
            <div
              class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2.5 mb-2 overflow-hidden"
            >
              <div
                class="bg-primary h-2.5 rounded-full transition-all duration-1000 ease-out"
                :style="{ width: progress + '%' }"
              ></div>
            </div>
            <!-- Milestones -->
            <div
              class="flex justify-between text-xs text-text-secondary font-medium"
            >
              <div
                v-for="milestone in milestones"
                :key="milestone.key"
                class="flex flex-col items-center gap-1"
                :class="{
                  'text-primary font-bold': milestone.isCurrent,
                  'opacity-50': !milestone.isCompleted && !milestone.isCurrent,
                }"
              >
                <div
                  class="w-2.5 h-2.5 rounded-full"
                  :class="
                    milestone.isCompleted || milestone.isCurrent
                      ? 'bg-primary ring-2 ring-blue-100 dark:ring-blue-900/30'
                      : 'bg-gray-300 dark:bg-gray-600'
                  "
                ></div>
                <span>{{ milestone.label }}</span>
              </div>
            </div>
          </div>
        </div>
      </section>

      <!-- Active Action / Next Steps -->
      <section class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Main Action Card -->
        <div
          class="lg:col-span-2 bg-surface-light rounded-xl shadow-sm border border-border-light p-6 md:p-8 flex flex-col justify-center relative overflow-hidden group hover:shadow-md transition-all"
        >
          <!-- Decorative gradient background -->
          <div
            class="absolute top-0 right-0 w-64 h-64 bg-gradient-to-br from-primary/5 to-transparent rounded-bl-full pointer-events-none"
          ></div>

          <div class="relative z-10 flex flex-col gap-6">
            <div class="flex items-start justify-between">
              <div class="flex flex-col gap-3">
                <div
                  class="flex items-center gap-2 text-primary font-bold text-sm uppercase tracking-wider"
                >
                  <span
                    class="material-symbols-outlined text-[20px] animate-pulse"
                    >priority_high</span
                  >
                  Perhatian Diperlukan
                </div>
                <h2 class="text-2xl font-bold text-text-main">
                  Informasi Tahap Aktif
                </h2>
              </div>
            </div>
            <div
              class="prose prose-blue max-w-none text-text-secondary leading-relaxed"
            >
              <p>
                {{ activeStageDescription }}
              </p>
              <!-- Upcoming schedule info -->
              <div
                v-if="upcomingSeminar"
                class="mt-4 flex items-center gap-3 p-4 bg-blue-50 dark:bg-blue-900/10 rounded-lg border-l-4 border-primary"
              >
                <span class="material-symbols-outlined text-primary"
                  >event</span
                >
                <p class="text-sm text-text-main">
                  <span class="font-bold">Jadwal Seminar:</span>
                  {{ formatDate(upcomingSeminar.tanggal) }}
                </p>
              </div>
              <div
                v-if="upcomingUjian"
                class="mt-4 flex items-center gap-3 p-4 bg-green-50 dark:bg-green-900/10 rounded-lg border-l-4 border-green-500"
              >
                <span class="material-symbols-outlined text-green-600"
                  >event</span
                >
                <p class="text-sm text-text-main">
                  <span class="font-bold">Jadwal Sidang:</span>
                  {{ formatDate(upcomingUjian.tanggal) }}
                </p>
              </div>
            </div>
            <div class="pt-2">
              <router-link
                to="/mahasiswa/skripsi/detail"
                class="inline-flex items-center justify-center h-12 px-6 rounded-lg bg-primary hover:bg-blue-600 text-white font-bold text-sm shadow-md shadow-primary/20 transition-all transform active:scale-95 group-hover:translate-x-1"
              >
                <span>Lihat Detail Skripsi</span>
                <span class="material-symbols-outlined ml-2 text-[20px]"
                  >arrow_forward</span
                >
              </router-link>
            </div>
          </div>
        </div>

        <!-- Side Widget: Quick Actions / Stats -->
        <div class="flex flex-col gap-4">
          <div
            class="bg-surface-light rounded-xl shadow-sm border border-border-light p-5 flex items-center gap-4 hover:shadow-md transition-all"
          >
            <div
              class="w-12 h-12 rounded-full bg-blue-50 dark:bg-blue-900/20 flex items-center justify-center text-primary"
            >
              <span class="material-symbols-outlined">description</span>
            </div>
            <div>
              <p class="text-sm text-text-secondary font-medium">
                Total Dokumen
              </p>
              <p class="text-xl font-bold text-text-main">
                {{ stats.total_dokumen ?? 0 }} Berkas
              </p>
            </div>
          </div>
          <div
            class="bg-surface-light rounded-xl shadow-sm border border-border-light p-5 flex items-center gap-4 hover:shadow-md transition-all"
          >
            <div
              class="w-12 h-12 rounded-full bg-orange-50 dark:bg-orange-900/20 flex items-center justify-center text-orange-500"
            >
              <span class="material-symbols-outlined">forum</span>
            </div>
            <div>
              <p class="text-sm text-text-secondary font-medium">Bimbingan</p>
              <p class="text-xl font-bold text-text-main">
                {{ stats.total_bimbingan ?? 0 }} Sesi
              </p>
            </div>
          </div>
          <div
            class="flex-1 bg-gradient-to-br from-gray-800 to-gray-900 rounded-xl shadow-sm p-6 text-white relative overflow-hidden flex flex-col justify-end group transition-all"
            data-alt="Abstract dark pattern background for aesthetic widget"
          >
            <div
              class="absolute top-0 left-0 w-full h-full opacity-10 group-hover:opacity-20 transition-opacity duration-700"
              style="
                background-image: radial-gradient(
                  circle at 2px 2px,
                  white 1px,
                  transparent 0
                );
                background-size: 20px 20px;
              "
            ></div>
            <div class="relative z-10">
              <p class="text-gray-300 text-sm mb-1 font-medium">
                Butuh Bantuan?
              </p>
              <p class="font-bold mb-4 text-lg">Hubungi Admin Prodi</p>
              <a
                href="mailto:admin@universitas.ac.id"
                class="text-xs font-bold bg-white/10 hover:bg-white/20 backdrop-blur-sm py-2.5 px-4 rounded-lg text-white transition-all border border-white/10 hover:border-white/30 flex items-center gap-2 w-fit"
              >
                <span class="material-symbols-outlined text-[16px]"
                  >support_agent</span
                >
                Kontak Admin
              </a>
            </div>
          </div>
        </div>
      </section>

      <!-- Dokumen Sempro Section -->
      <section
        v-if="
          semproSeminar &&
          (semproSeminar.penguji?.length || semproSeminar.berita_acara)
        "
        class="bg-surface-light rounded-xl shadow-sm border border-border-light overflow-hidden hover:shadow-md transition-all"
      >
        <div class="p-5 border-b border-border-light">
          <h3 class="text-lg font-bold text-text-main">
            Dokumen Seminar Proposal
          </h3>
          <p class="text-sm text-text-secondary">
            Dokumen resmi terkait seminar proposal Anda
          </p>
        </div>
        <div class="p-5 grid grid-cols-1 sm:grid-cols-2 gap-4">
          <!-- SK Penguji Sempro -->
          <div
            v-if="semproSeminar.penguji?.length"
            class="flex items-center gap-4 p-4 rounded-xl border border-border-light bg-white dark:bg-background"
          >
            <div
              class="size-12 rounded-xl flex items-center justify-center bg-red-50 text-red-500 shrink-0"
            >
              <span class="material-symbols-outlined">picture_as_pdf</span>
            </div>
            <div class="flex-1 min-w-0">
              <h4 class="text-sm font-bold text-text-main">
                SK Penguji Sempro
              </h4>
              <p class="text-xs text-text-secondary">
                {{ semproSeminar.penguji.length }} penguji ditugaskan
              </p>
            </div>
            <div class="flex items-center gap-1.5 shrink-0">
              <button
                @click="previewPdf('sk-penguji-sempro')"
                :disabled="previewingPdf === 'sk-penguji-sempro'"
                class="p-2 text-primary bg-primary/10 rounded-lg hover:bg-primary/20 transition-colors disabled:opacity-50"
                title="Lihat"
              >
                <span class="material-symbols-outlined text-[18px]">{{
                  previewingPdf === "sk-penguji-sempro"
                    ? "hourglass_top"
                    : "visibility"
                }}</span>
              </button>
              <button
                @click="downloadPdf('sk-penguji-sempro')"
                :disabled="downloadingPdf === 'sk-penguji-sempro'"
                class="p-2 text-white bg-primary rounded-lg hover:bg-primary/90 transition-colors disabled:opacity-50 shadow-sm"
                title="Unduh"
              >
                <span class="material-symbols-outlined text-[18px]">{{
                  downloadingPdf === "sk-penguji-sempro"
                    ? "hourglass_top"
                    : "download"
                }}</span>
              </button>
            </div>
          </div>

          <!-- Berita Acara Sempro -->
          <div
            v-if="semproSeminar.berita_acara"
            class="flex items-center gap-4 p-4 rounded-xl border border-border-light bg-white dark:bg-background"
          >
            <div
              class="size-12 rounded-xl flex items-center justify-center bg-blue-50 text-blue-500 shrink-0"
            >
              <span class="material-symbols-outlined">description</span>
            </div>
            <div class="flex-1 min-w-0">
              <h4 class="text-sm font-bold text-text-main">
                Berita Acara Sempro
              </h4>
              <p class="text-xs text-text-secondary">Seminar selesai</p>
            </div>
            <div class="flex items-center gap-1.5 shrink-0">
              <button
                @click="previewPdf('berita-acara-sempro')"
                :disabled="previewingPdf === 'berita-acara-sempro'"
                class="p-2 text-primary bg-primary/10 rounded-lg hover:bg-primary/20 transition-colors disabled:opacity-50"
                title="Lihat"
              >
                <span class="material-symbols-outlined text-[18px]">{{
                  previewingPdf === "berita-acara-sempro"
                    ? "hourglass_top"
                    : "visibility"
                }}</span>
              </button>
              <button
                @click="downloadPdf('berita-acara-sempro')"
                :disabled="downloadingPdf === 'berita-acara-sempro'"
                class="p-2 text-white bg-primary rounded-lg hover:bg-primary/90 transition-colors disabled:opacity-50 shadow-sm"
                title="Unduh"
              >
                <span class="material-symbols-outlined text-[18px]">{{
                  downloadingPdf === "berita-acara-sempro"
                    ? "hourglass_top"
                    : "download"
                }}</span>
              </button>
            </div>
          </div>
        </div>
      </section>

      <!-- Dokumen Semhas Section -->
      <section
        v-if="
          authStore.semhasEnabled &&
          semhasSeminar &&
          (semhasSeminar.penguji?.length || semhasSeminar.berita_acara)
        "
        class="bg-surface-light rounded-xl shadow-sm border border-border-light overflow-hidden hover:shadow-md transition-all"
      >
        <div class="p-5 border-b border-border-light">
          <h3 class="text-lg font-bold text-text-main">
            Dokumen Seminar Hasil
          </h3>
          <p class="text-sm text-text-secondary">
            Dokumen resmi terkait seminar hasil Anda
          </p>
        </div>
        <div class="p-5 grid grid-cols-1 sm:grid-cols-2 gap-4">
          <div
            v-if="semhasSeminar.penguji?.length"
            class="flex items-center gap-4 p-4 rounded-xl border border-border-light bg-white dark:bg-background"
          >
            <div
              class="size-12 rounded-xl flex items-center justify-center bg-orange-50 text-orange-500 shrink-0"
            >
              <span class="material-symbols-outlined">picture_as_pdf</span>
            </div>
            <div class="flex-1 min-w-0">
              <h4 class="text-sm font-bold text-text-main">
                SK Penguji Semhas
              </h4>
              <p class="text-xs text-text-secondary">
                {{ semhasSeminar.penguji.length }} penguji ditugaskan
              </p>
            </div>
            <div class="flex items-center gap-1.5 shrink-0">
              <button
                @click="previewPdf('sk-penguji-semhas')"
                :disabled="previewingPdf === 'sk-penguji-semhas'"
                class="p-2 text-primary bg-primary/10 rounded-lg hover:bg-primary/20 transition-colors disabled:opacity-50"
                title="Lihat"
              >
                <span class="material-symbols-outlined text-[18px]">{{
                  previewingPdf === "sk-penguji-semhas"
                    ? "hourglass_top"
                    : "visibility"
                }}</span>
              </button>
              <button
                @click="downloadPdf('sk-penguji-semhas')"
                :disabled="downloadingPdf === 'sk-penguji-semhas'"
                class="p-2 text-white bg-primary rounded-lg hover:bg-primary/90 transition-colors disabled:opacity-50 shadow-sm"
                title="Unduh"
              >
                <span class="material-symbols-outlined text-[18px]">{{
                  downloadingPdf === "sk-penguji-semhas"
                    ? "hourglass_top"
                    : "download"
                }}</span>
              </button>
            </div>
          </div>

          <div
            v-if="semhasSeminar.berita_acara"
            class="flex items-center gap-4 p-4 rounded-xl border border-border-light bg-white dark:bg-background"
          >
            <div
              class="size-12 rounded-xl flex items-center justify-center bg-teal-50 text-teal-500 shrink-0"
            >
              <span class="material-symbols-outlined">description</span>
            </div>
            <div class="flex-1 min-w-0">
              <h4 class="text-sm font-bold text-text-main">
                Berita Acara Semhas
              </h4>
              <p class="text-xs text-text-secondary">Seminar selesai</p>
            </div>
            <div class="flex items-center gap-1.5 shrink-0">
              <button
                @click="previewPdf('berita-acara-semhas')"
                :disabled="previewingPdf === 'berita-acara-semhas'"
                class="p-2 text-primary bg-primary/10 rounded-lg hover:bg-primary/20 transition-colors disabled:opacity-50"
                title="Lihat"
              >
                <span class="material-symbols-outlined text-[18px]">{{
                  previewingPdf === "berita-acara-semhas"
                    ? "hourglass_top"
                    : "visibility"
                }}</span>
              </button>
              <button
                @click="downloadPdf('berita-acara-semhas')"
                :disabled="downloadingPdf === 'berita-acara-semhas'"
                class="p-2 text-white bg-primary rounded-lg hover:bg-primary/90 transition-colors disabled:opacity-50 shadow-sm"
                title="Unduh"
              >
                <span class="material-symbols-outlined text-[18px]">{{
                  downloadingPdf === "berita-acara-semhas"
                    ? "hourglass_top"
                    : "download"
                }}</span>
              </button>
            </div>
          </div>
        </div>
      </section>

      <!-- Dokumen Sidang Skripsi Section -->
      <section
        v-if="
          sidangSeminar &&
          (sidangSeminar.penguji?.length || sidangSeminar.berita_acara)
        "
        class="bg-surface-light rounded-xl shadow-sm border border-border-light overflow-hidden hover:shadow-md transition-all"
      >
        <div class="p-5 border-b border-border-light">
          <h3 class="text-lg font-bold text-text-main">
            Dokumen Sidang Skripsi
          </h3>
          <p class="text-sm text-text-secondary">
            Dokumen resmi terkait sidang skripsi Anda
          </p>
        </div>
        <div class="p-5 grid grid-cols-1 sm:grid-cols-2 gap-4">
          <div
            v-if="sidangSeminar.penguji?.length"
            class="flex items-center gap-4 p-4 rounded-xl border border-border-light bg-white dark:bg-background"
          >
            <div
              class="size-12 rounded-xl flex items-center justify-center bg-indigo-50 text-indigo-500 shrink-0"
            >
              <span class="material-symbols-outlined">picture_as_pdf</span>
            </div>
            <div class="flex-1 min-w-0">
              <h4 class="text-sm font-bold text-text-main">
                SK Penguji Sidang
              </h4>
              <p class="text-xs text-text-secondary">
                {{ sidangSeminar.penguji.length }} penguji ditugaskan
              </p>
            </div>
            <div class="flex items-center gap-1.5 shrink-0">
              <button
                @click="previewPdf('sk-penguji-sidang')"
                :disabled="previewingPdf === 'sk-penguji-sidang'"
                class="p-2 text-primary bg-primary/10 rounded-lg hover:bg-primary/20 transition-colors disabled:opacity-50"
                title="Lihat"
              >
                <span class="material-symbols-outlined text-[18px]">{{
                  previewingPdf === "sk-penguji-sidang"
                    ? "hourglass_top"
                    : "visibility"
                }}</span>
              </button>
              <button
                @click="downloadPdf('sk-penguji-sidang')"
                :disabled="downloadingPdf === 'sk-penguji-sidang'"
                class="p-2 text-white bg-primary rounded-lg hover:bg-primary/90 transition-colors disabled:opacity-50 shadow-sm"
                title="Unduh"
              >
                <span class="material-symbols-outlined text-[18px]">{{
                  downloadingPdf === "sk-penguji-sidang"
                    ? "hourglass_top"
                    : "download"
                }}</span>
              </button>
            </div>
          </div>

          <div
            v-if="sidangSeminar.berita_acara"
            class="flex items-center gap-4 p-4 rounded-xl border border-border-light bg-white dark:bg-background"
          >
            <div
              class="size-12 rounded-xl flex items-center justify-center bg-rose-50 text-rose-500 shrink-0"
            >
              <span class="material-symbols-outlined">description</span>
            </div>
            <div class="flex-1 min-w-0">
              <h4 class="text-sm font-bold text-text-main">
                Berita Acara Sidang
              </h4>
              <p class="text-xs text-text-secondary">Sidang selesai</p>
            </div>
            <div class="flex items-center gap-1.5 shrink-0">
              <button
                @click="previewPdf('berita-acara-sidang')"
                :disabled="previewingPdf === 'berita-acara-sidang'"
                class="p-2 text-primary bg-primary/10 rounded-lg hover:bg-primary/20 transition-colors disabled:opacity-50"
                title="Lihat"
              >
                <span class="material-symbols-outlined text-[18px]">{{
                  previewingPdf === "berita-acara-sidang"
                    ? "hourglass_top"
                    : "visibility"
                }}</span>
              </button>
              <button
                @click="downloadPdf('berita-acara-sidang')"
                :disabled="downloadingPdf === 'berita-acara-sidang'"
                class="p-2 text-white bg-primary rounded-lg hover:bg-primary/90 transition-colors disabled:opacity-50 shadow-sm"
                title="Unduh"
              >
                <span class="material-symbols-outlined text-[18px]">{{
                  downloadingPdf === "berita-acara-sidang"
                    ? "hourglass_top"
                    : "download"
                }}</span>
              </button>
            </div>
          </div>
        </div>
      </section>

      <!-- SK Yudisium Section -->
      <section
        v-if="skYudisium"
        class="bg-surface-light rounded-xl shadow-sm border border-border-light overflow-hidden hover:shadow-md transition-all"
      >
        <div class="p-5 border-b border-border-light">
          <h3 class="text-lg font-bold text-text-main">SK Yudisium</h3>
          <p class="text-sm text-text-secondary">
            Surat keputusan kelulusan Anda
          </p>
        </div>
        <div class="p-5">
          <div
            class="flex items-center gap-4 p-4 rounded-xl border border-border-light bg-white dark:bg-background"
          >
            <div
              class="size-12 rounded-xl flex items-center justify-center bg-emerald-50 text-emerald-500 shrink-0"
            >
              <span class="material-symbols-outlined">school</span>
            </div>
            <div class="flex-1 min-w-0">
              <h4 class="text-sm font-bold text-text-main">SK Yudisium</h4>
              <p class="text-xs text-text-secondary">Dinyatakan lulus</p>
            </div>
            <div class="flex items-center gap-1.5 shrink-0">
              <button
                @click="previewPdf('sk-yudisium')"
                :disabled="previewingPdf === 'sk-yudisium'"
                class="p-2 text-primary bg-primary/10 rounded-lg hover:bg-primary/20 transition-colors disabled:opacity-50"
                title="Lihat"
              >
                <span class="material-symbols-outlined text-[18px]">{{
                  previewingPdf === "sk-yudisium"
                    ? "hourglass_top"
                    : "visibility"
                }}</span>
              </button>
              <button
                @click="downloadPdf('sk-yudisium')"
                :disabled="downloadingPdf === 'sk-yudisium'"
                class="p-2 text-white bg-primary rounded-lg hover:bg-primary/90 transition-colors disabled:opacity-50 shadow-sm"
                title="Unduh"
              >
                <span class="material-symbols-outlined text-[18px]">{{
                  downloadingPdf === "sk-yudisium"
                    ? "hourglass_top"
                    : "download"
                }}</span>
              </button>
            </div>
          </div>
        </div>
      </section>
    </template>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from "vue";
import { mahasiswaService } from "../../services/mahasiswaService";
import { useAuthStore } from "../../stores/auth";

const authStore = useAuthStore();
const loading = ref(true);
const mahasiswa = ref(null);
const skripsi = ref(null);
const stats = ref({});
const upcomingSeminar = ref(null);
const upcomingUjian = ref(null);
const semproSeminar = ref(null);
const semhasSeminar = ref(null);
const sidangSeminar = ref(null);
const skYudisium = ref(null);
const downloadingPdf = ref(null);
const previewingPdf = ref(null);

// Status mapping
const statusMap = {
  draft: { label: "Draft", icon: "edit_note", color: "gray" },
  pengajuan: { label: "Pengajuan", icon: "hourglass_top", color: "yellow" },
  disetujui: { label: "Disetujui", icon: "check_circle", color: "green" },
  ditolak: { label: "Ditolak", icon: "cancel", color: "red" },
  proposal: { label: "Tahap Proposal", icon: "description", color: "blue" },
  sempro: { label: "Sudah Sempro", icon: "check_circle", color: "green" },
  bimbingan: {
    label: "Proses Bimbingan",
    icon: "auto_stories",
    color: "blue",
  },
  semhas: { label: "Seminar Hasil", icon: "record_voice_over", color: "blue" },
  sidang: { label: "Sidang", icon: "school", color: "blue" },
  revisi: { label: "Revisi", icon: "edit", color: "orange" },
  lulus: { label: "Lulus", icon: "celebration", color: "green" },
};

const statusLabel = computed(() => {
  if (!skripsi.value) return "";
  return statusMap[skripsi.value.status]?.label || skripsi.value.status;
});

const statusIcon = computed(() => {
  if (!skripsi.value) return "info";
  return statusMap[skripsi.value.status]?.icon || "info";
});

const statusBadgeClass = computed(() => {
  if (!skripsi.value) return "";
  const color = statusMap[skripsi.value.status]?.color || "gray";
  const classes = {
    green:
      "bg-green-50 dark:bg-green-900/20 border border-green-100 dark:border-green-800 text-green-700 dark:text-green-400",
    blue: "bg-blue-50 dark:bg-blue-900/20 border border-blue-100 dark:border-blue-800 text-blue-700 dark:text-blue-400",
    yellow:
      "bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-100 dark:border-yellow-800 text-yellow-700 dark:text-yellow-400",
    red: "bg-red-50 dark:bg-red-900/20 border border-red-100 dark:border-red-800 text-red-700 dark:text-red-400",
    orange:
      "bg-orange-50 dark:bg-orange-900/20 border border-orange-100 dark:border-orange-800 text-orange-700 dark:text-orange-400",
    gray: "bg-gray-50 dark:bg-gray-900/20 border border-gray-100 dark:border-gray-800 text-gray-700 dark:text-gray-400",
  };
  return classes[color] || classes.gray;
});

const statusIconClass = computed(() => {
  if (!skripsi.value) return "";
  const color = statusMap[skripsi.value.status]?.color || "gray";
  const classes = {
    green: "text-green-600 dark:text-green-400",
    blue: "text-blue-600 dark:text-blue-400",
    yellow: "text-yellow-600 dark:text-yellow-400",
    red: "text-red-600 dark:text-red-400",
    orange: "text-orange-600 dark:text-orange-400",
    gray: "text-gray-600 dark:text-gray-400",
  };
  return classes[color] || classes.gray;
});

const progress = computed(() => stats.value.progress ?? 0);

// Pembimbing names
const pembimbingNames = computed(() => {
  if (!skripsi.value?.pembimbing?.length) return "";
  return skripsi.value.pembimbing
    .map((p) => p.dosen?.nama || "Unknown")
    .join(", ");
});

// Last updated relative time
const lastUpdated = computed(() => {
  if (!skripsi.value?.updated_at) return "-";
  const now = new Date();
  const updated = new Date(skripsi.value.updated_at);
  const diffMs = now - updated;
  const diffMinutes = Math.floor(diffMs / 60000);
  const diffHours = Math.floor(diffMinutes / 60);
  const diffDays = Math.floor(diffHours / 24);

  if (diffMinutes < 1) return "Baru saja";
  if (diffMinutes < 60) return `${diffMinutes} menit yang lalu`;
  if (diffHours < 24) return `${diffHours} jam yang lalu`;
  if (diffDays < 30) return `${diffDays} hari yang lalu`;
  return formatDate(skripsi.value.updated_at);
});

// Milestones for progress bar
const milestoneSteps = computed(() => {
  const allSteps = [
    {
      key: "proposal",
      label: "Proposal",
      statuses: ["proposal", "pengajuan", "disetujui"],
    },
    { key: "sempro", label: "Sempro", statuses: ["sempro"] },
    { key: "semhas", label: "Semhas", statuses: ["semhas", "bimbingan"] },
    { key: "sidang", label: "Sidang", statuses: ["sidang", "revisi", "lulus"] },
  ];
  if (!authStore.semhasEnabled) {
    return allSteps.filter((s) => s.key !== "semhas");
  }
  return allSteps;
});

const statusOrder = [
  "draft",
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

const milestones = computed(() => {
  if (!skripsi.value) return milestoneSteps.value;
  const currentIdx = statusOrder.indexOf(skripsi.value.status);
  return milestoneSteps.value.map((m) => {
    const mStatuses = m.statuses;
    const mMaxIdx = Math.max(...mStatuses.map((s) => statusOrder.indexOf(s)));
    const mMinIdx = Math.min(...mStatuses.map((s) => statusOrder.indexOf(s)));
    return {
      ...m,
      isCompleted: currentIdx > mMaxIdx,
      isCurrent: currentIdx >= mMinIdx && currentIdx <= mMaxIdx,
    };
  });
});

// Active stage description
const activeStageDescription = computed(() => {
  if (!skripsi.value) return "";
  const descriptions = {
    draft:
      "Skripsi Anda masih dalam status draft. Silakan lengkapi dan ajukan judul skripsi Anda.",
    pengajuan:
      "Judul skripsi Anda sedang dalam proses review oleh admin. Mohon tunggu konfirmasi persetujuan.",
    disetujui:
      "Selamat! Judul skripsi Anda telah disetujui. Langkah selanjutnya adalah melakukan bimbingan dengan dosen pembimbing.",
    proposal:
      "Anda sedang dalam tahap proposal. Persiapkan materi proposal Anda untuk seminar proposal.",
    sempro:
      "Selamat! Anda telah menyelesaikan Seminar Proposal. Langkah selanjutnya adalah melakukan Revisi Pasca Sempro sesuai dengan catatan dari dosen penguji.",
    bimbingan:
      "Anda sedang dalam tahap bimbingan. Lakukan bimbingan secara rutin dengan dosen pembimbing untuk menyelesaikan skripsi.",
    semhas:
      "Anda sudah memasuki tahap Seminar Hasil. Persiapkan presentasi dan dokumen yang diperlukan.",
    sidang:
      "Anda sudah memasuki tahap Sidang Skripsi. Persiapkan diri dengan baik untuk ujian sidang.",
    revisi:
      "Anda sedang dalam tahap revisi pasca sidang. Selesaikan revisi sesuai catatan dari dosen penguji.",
    lulus:
      "Selamat! Anda telah dinyatakan lulus. Pastikan semua dokumen akhir sudah diselesaikan.",
    ditolak:
      "Maaf, judul skripsi Anda ditolak. Silakan perbaiki dan ajukan kembali judul baru.",
  };
  return (
    descriptions[skripsi.value.status] ||
    "Pantau terus progres skripsi Anda di halaman ini."
  );
});

const formatDate = (dateStr) => {
  if (!dateStr) return "-";
  return new Date(dateStr).toLocaleDateString("id-ID", {
    day: "numeric",
    month: "long",
    year: "numeric",
  });
};

const pdfFileNames = {
  "sk-penguji-sempro": "SK_Penguji_Sempro.pdf",
  "berita-acara-sempro": "Berita_Acara_Sempro.pdf",
  "sk-penguji-semhas": "SK_Penguji_Semhas.pdf",
  "berita-acara-semhas": "Berita_Acara_Semhas.pdf",
  "sk-penguji-sidang": "SK_Penguji_Sidang.pdf",
  "berita-acara-sidang": "Berita_Acara_Sidang.pdf",
  "sk-yudisium": "SK_Yudisium.pdf",
};

const downloadPdf = async (type) => {
  downloadingPdf.value = type;
  try {
    const response = await mahasiswaService.downloadOfficialPdf(type);
    const blob = new Blob([response.data], { type: "application/pdf" });
    const url = window.URL.createObjectURL(blob);
    const a = document.createElement("a");
    a.href = url;
    a.download = pdfFileNames[type] || `${type}.pdf`;
    a.click();
    window.URL.revokeObjectURL(url);
  } catch (err) {
    console.error("Failed to download PDF:", err);
    alert(
      "Gagal mengunduh dokumen: " +
        (err.response?.data?.message || err.message),
    );
  } finally {
    downloadingPdf.value = null;
  }
};

const previewPdf = async (type) => {
  previewingPdf.value = type;
  try {
    const response = await mahasiswaService.downloadOfficialPdf(type);
    const blob = new Blob([response.data], { type: "application/pdf" });
    const url = window.URL.createObjectURL(blob);
    window.open(url, "_blank");
  } catch (err) {
    console.error("Failed to preview PDF:", err);
    alert(
      "Gagal menampilkan dokumen: " +
        (err.response?.data?.message || err.message),
    );
  } finally {
    previewingPdf.value = null;
  }
};

onMounted(async () => {
  try {
    const res = await mahasiswaService.getDashboard();
    if (res.success) {
      mahasiswa.value = res.data.mahasiswa;
      skripsi.value = res.data.skripsi;
      stats.value = res.data.stats || {};
      upcomingSeminar.value = res.data.upcoming_seminar;
      upcomingUjian.value = res.data.upcoming_ujian;
      semproSeminar.value = res.data.sempro_seminar;
      semhasSeminar.value = res.data.semhas_seminar;
      sidangSeminar.value = res.data.sidang_seminar;
      skYudisium.value = res.data.sk_yudisium;
    }
  } catch (err) {
    console.error("Failed to load dashboard:", err);
  } finally {
    loading.value = false;
  }
});
</script>
