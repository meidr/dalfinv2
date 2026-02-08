<template>
  <div class="flex flex-col gap-6">
    <!-- Page Heading -->
    <section class="flex flex-col gap-1">
      <h2 class="text-3xl font-bold tracking-tight text-text-main">
        Selamat Datang, Budi Santoso
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
              <span>Terakhir update: 2 Hari yang lalu</span>
            </div>
          </div>
          <div>
            <h3 class="text-2xl font-bold leading-snug text-text-main mb-2">
              Analisis Penerapan Algoritma Machine Learning pada Sistem Prediksi
              Cuaca Berbasis IoT
            </h3>
            <p class="text-text-secondary text-sm">
              Pembimbing: Dr. Rina Kurniawati, M.Kom
            </p>
          </div>
          <div class="mt-auto pt-4 flex items-center gap-2">
            <div
              class="inline-flex items-center gap-2 px-3 py-1.5 rounded-lg bg-green-50 dark:bg-green-900/20 border border-green-100 dark:border-green-800 text-green-700 dark:text-green-400 text-sm font-semibold"
            >
              <span
                class="material-symbols-outlined text-[18px] filled text-green-600 dark:text-green-400"
                >check_circle</span
              >
              Status: Sudah Sempro
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
            <span class="text-2xl font-bold text-primary">50%</span>
          </div>
          <!-- Progress Track -->
          <div
            class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2.5 mb-2 overflow-hidden"
          >
            <div
              class="bg-primary h-2.5 rounded-full transition-all duration-1000 ease-out"
              style="width: 50%"
            ></div>
          </div>
          <!-- Milestones -->
          <div
            class="flex justify-between text-xs text-text-secondary font-medium"
          >
            <div class="flex flex-col items-center gap-1">
              <div
                class="w-2.5 h-2.5 rounded-full bg-primary ring-2 ring-blue-100 dark:ring-blue-900/30"
              ></div>
              <span>Proposal</span>
            </div>
            <div
              class="flex flex-col items-center gap-1 text-primary font-bold"
            >
              <div
                class="w-2.5 h-2.5 rounded-full bg-primary ring-2 ring-blue-100 dark:ring-blue-900/30"
              ></div>
              <span>Sempro</span>
            </div>
            <div class="flex flex-col items-center gap-1 opacity-50">
              <div
                class="w-2.5 h-2.5 rounded-full bg-gray-300 dark:bg-gray-600"
              ></div>
              <span>Semhas</span>
            </div>
            <div class="flex flex-col items-center gap-1 opacity-50">
              <div
                class="w-2.5 h-2.5 rounded-full bg-gray-300 dark:bg-gray-600"
              ></div>
              <span>Sidang</span>
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
              Selamat! Anda telah menyelesaikan Seminar Proposal. Langkah
              selanjutnya yang harus Anda lakukan adalah melakukan
              <strong class="text-text-main">Revisi Pasca Sempro</strong> sesuai
              dengan catatan dari dosen penguji.
            </p>
            <div
              class="mt-4 flex items-center gap-3 p-4 bg-blue-50 dark:bg-blue-900/10 rounded-lg border-l-4 border-primary"
            >
              <span class="material-symbols-outlined text-primary">event</span>
              <p class="text-sm text-text-main">
                <span class="font-bold">Deadline Revisi:</span> 2 Minggu lagi
                (24 Oktober 2023)
              </p>
            </div>
          </div>
          <div class="pt-2">
            <button
              class="inline-flex items-center justify-center h-12 px-6 rounded-lg bg-primary hover:bg-blue-600 text-white font-bold text-sm shadow-md shadow-primary/20 transition-all transform active:scale-95 group-hover:translate-x-1"
            >
              <span>Lihat Detail Skripsi</span>
              <span class="material-symbols-outlined ml-2 text-[20px]"
                >arrow_forward</span
              >
            </button>
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
            <p class="text-sm text-text-secondary font-medium">Total Dokumen</p>
            <p class="text-xl font-bold text-text-main">12 Berkas</p>
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
            <p class="text-xl font-bold text-text-main">8 Sesi</p>
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
            <p class="text-gray-300 text-sm mb-1 font-medium">Butuh Bantuan?</p>
            <p class="font-bold mb-4 text-lg">Hubungi Admin Prodi</p>
            <button
              class="text-xs font-bold bg-white/10 hover:bg-white/20 backdrop-blur-sm py-2.5 px-4 rounded-lg text-white transition-all border border-white/10 hover:border-white/30 flex items-center gap-2 w-fit"
            >
              <span class="material-symbols-outlined text-[16px]"
                >support_agent</span
              >
              Kontak Admin
            </button>
          </div>
        </div>
      </div>
    </section>
  </div>
</template>
