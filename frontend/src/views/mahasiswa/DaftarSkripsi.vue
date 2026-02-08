<template>
  <div class="flex flex-col gap-6">
    <!-- Breadcrumbs -->
    <div class="flex flex-wrap items-center gap-2 text-sm">
      <router-link
        to="/mahasiswa/dashboard"
        class="text-text-secondary hover:text-primary font-medium"
        >Home</router-link
      >
      <span class="material-symbols-outlined text-text-secondary text-sm"
        >chevron_right</span
      >
      <span class="text-text-main font-bold">Skripsi Saya</span>
    </div>

    <!-- Page Heading -->
    <div
      class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4"
    >
      <div>
        <h2 class="text-3xl font-bold tracking-tight text-text-main mb-2">
          Skripsi Saya
        </h2>
        <p class="text-text-secondary text-base">
          Kelola progres skripsi dan lihat riwayat judul Anda.
        </p>
      </div>
    </div>

    <!-- Active Thesis Card -->
    <div class="w-full">
      <h3 class="text-lg font-bold text-text-main mb-4">Skripsi Aktif</h3>
      <div
        class="bg-surface-light rounded-xl shadow-sm border border-border-light overflow-hidden group hover:border-primary/50 transition-all duration-300"
      >
        <!-- Status Strip -->
        <div class="h-1.5 bg-primary w-full"></div>
        <div class="p-6 md:p-8">
          <div class="flex flex-col gap-6">
            <!-- Header -->
            <div class="flex justify-between items-start">
              <span
                class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-blue-50 dark:bg-blue-900/30 text-primary dark:text-blue-300 border border-blue-100 dark:border-blue-800"
              >
                <span
                  class="size-2 rounded-full bg-primary animate-pulse"
                ></span>
                Proses Bimbingan
              </span>
              <button
                class="text-text-secondary hover:text-text-main transition-colors"
              >
                <span class="material-symbols-outlined">more_horiz</span>
              </button>
            </div>

            <!-- Title -->
            <div>
              <h3
                class="text-xl md:text-2xl font-bold text-text-main leading-tight mb-3 group-hover:text-primary transition-colors"
              >
                Analisis Penerapan Algoritma Machine Learning pada Sistem
                Prediksi Cuaca Berbasis IoT
              </h3>
              <p class="text-sm text-text-secondary">
                Pembimbing:
                <span class="font-medium text-text-main dark:text-gray-200"
                  >Dr. Rina Kurniawati, M.Kom</span
                >
              </p>
            </div>

            <!-- Metadata -->
            <div
              class="grid grid-cols-1 sm:grid-cols-2 gap-4 py-4 border-t border-b border-border-light"
            >
              <div class="flex items-center gap-3">
                <div
                  class="size-10 rounded-lg bg-sidebar-light flex items-center justify-center text-text-secondary"
                >
                  <span class="material-symbols-outlined">calendar_today</span>
                </div>
                <div class="flex flex-col">
                  <span class="text-xs text-text-secondary font-medium"
                    >Tanggal Daftar</span
                  >
                  <span class="text-sm font-semibold text-text-main"
                    >12 Okt 2023</span
                  >
                </div>
              </div>
              <div class="flex items-center gap-3">
                <div
                  class="size-10 rounded-lg bg-sidebar-light flex items-center justify-center text-text-secondary"
                >
                  <span class="material-symbols-outlined">school</span>
                </div>
                <div class="flex flex-col">
                  <span class="text-xs text-text-secondary font-medium"
                    >Semester</span
                  >
                  <span class="text-sm font-semibold text-text-main"
                    >Ganjil 2023/2024</span
                  >
                </div>
              </div>
            </div>

            <!-- Progress -->
            <div class="flex flex-col gap-3">
              <div class="flex justify-between items-end">
                <span class="text-sm font-medium text-text-main"
                  >Progres Keseluruhan</span
                >
                <span class="text-sm font-bold text-primary">40%</span>
              </div>
              <div
                class="w-full bg-sidebar-light rounded-full h-2.5 overflow-hidden"
              >
                <div
                  class="bg-primary h-2.5 rounded-full transition-all duration-500"
                  style="width: 40%"
                ></div>
              </div>
              <p class="text-xs text-text-secondary mt-1">
                Saat ini di Tahap: Pengumpulan Data & Preprocessing
              </p>
            </div>

            <!-- Action -->
            <div class="flex justify-end pt-2">
              <router-link
                to="/mahasiswa/skripsi/detail"
                class="w-full sm:w-auto bg-primary hover:bg-blue-600 text-white px-6 py-2.5 rounded-lg text-sm font-bold tracking-wide transition-all shadow-sm flex items-center justify-center gap-2 group/btn"
              >
                <span>Lihat Detail</span>
                <span
                  class="material-symbols-outlined text-[18px] group-hover/btn:translate-x-1 transition-transform"
                  >arrow_forward</span
                >
              </router-link>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- History / Old Thesis List -->
    <div class="mt-4">
      <h3 class="text-lg font-bold text-text-main mb-4">
        Riwayat Judul Skripsi
      </h3>
      <div class="grid grid-cols-1 gap-4">
        <!-- Inactive Card 1 -->
        <div
          class="bg-surface-light p-6 rounded-xl border border-border-light opacity-75 hover:opacity-100 transition-opacity"
        >
          <div class="flex justify-between items-start mb-2">
            <span
              class="inline-flex items-center px-2.5 py-0.5 rounded text-xs font-bold bg-gray-100 text-gray-600 dark:bg-gray-800 dark:text-gray-400 border border-gray-200 dark:border-gray-700"
            >
              Ditolak / Batal
            </span>
            <span class="text-xs text-text-secondary"
              >Semester Genap 2022/2023</span
            >
          </div>
          <h4 class="text-base font-bold text-text-main mb-2">
            Rancang Bangun Aplikasi E-Commerce Berbasis Blockchain untuk UMKM
          </h4>
          <p class="text-sm text-text-secondary mb-4">
            Alasan: Judul terlalu luas dan tidak sesuai dengan konsentrasi
            studi.
          </p>
          <div class="flex justify-end">
            <button
              class="text-xs font-bold text-text-secondary hover:text-primary transition-colors flex items-center gap-1"
            >
              Lihat Arsip
              <span class="material-symbols-outlined text-[14px]"
                >arrow_forward</span
              >
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
// Script logic if needed
</script>
