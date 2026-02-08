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
      <span class="text-text-main font-bold">Jadwal Ujian Skripsi</span>
    </div>

    <!-- Page Heading -->
    <div class="flex flex-wrap justify-between items-end gap-4">
      <div class="flex flex-col gap-1">
        <h1 class="text-3xl font-bold tracking-tight text-text-main">
          Jadwal Ujian Skripsi
        </h1>
        <p class="text-text-secondary text-base">
          Pastikan Anda hadir 30 menit sebelum jadwal dimulai.
        </p>
      </div>
      <button
        class="flex items-center gap-2 px-4 py-2 bg-primary text-white text-sm font-bold rounded-lg hover:bg-blue-600 transition-colors shadow-sm"
      >
        <span class="material-symbols-outlined">download</span>
        Unduh Undangan
      </button>
    </div>

    <!-- Main Exam Card -->
    <div
      class="bg-surface-light rounded-xl shadow-sm border border-border-light overflow-hidden p-8"
    >
      <div class="flex flex-col lg:flex-row gap-8">
        <div class="flex-1">
          <div class="flex items-center gap-3 mb-4">
            <span
              class="px-3 py-1 bg-blue-100 text-primary dark:bg-blue-900/30 dark:text-blue-300 text-xs font-bold rounded-full uppercase tracking-wider"
              >Terjadwal</span
            >
            <span class="text-text-secondary">•</span>
            <span class="text-sm text-text-secondary"
              >Sidang Akhir (Skripsi)</span
            >
          </div>
          <h2 class="text-2xl font-bold text-text-main mb-6 leading-tight">
            Analisis Perbandingan Performa Framework React dan Vue dalam
            Pembangunan Progressive Web Apps (PWA)
          </h2>

          <div class="grid grid-cols-1 sm:grid-cols-2 gap-y-6 gap-x-12">
            <div class="flex items-start gap-3">
              <div
                class="p-2 bg-gray-50 dark:bg-gray-800 rounded-lg text-primary"
              >
                <span class="material-symbols-outlined">calendar_today</span>
              </div>
              <div>
                <p
                  class="text-xs text-text-secondary uppercase font-bold tracking-wide"
                >
                  Tanggal Pelaksanaan
                </p>
                <p class="font-semibold text-text-main">
                  Senin, 25 November 2024
                </p>
              </div>
            </div>
            <div class="flex items-start gap-3">
              <div
                class="p-2 bg-gray-50 dark:bg-gray-800 rounded-lg text-primary"
              >
                <span class="material-symbols-outlined">schedule</span>
              </div>
              <div>
                <p
                  class="text-xs text-text-secondary uppercase font-bold tracking-wide"
                >
                  Waktu Sidang
                </p>
                <p class="font-semibold text-text-main">10:00 - 12:00 WIB</p>
              </div>
            </div>
            <div class="flex items-start gap-3">
              <div
                class="p-2 bg-gray-50 dark:bg-gray-800 rounded-lg text-primary"
              >
                <span class="material-symbols-outlined">location_on</span>
              </div>
              <div>
                <p
                  class="text-xs text-text-secondary uppercase font-bold tracking-wide"
                >
                  Tempat / Ruangan
                </p>
                <p class="font-semibold text-text-main">
                  Ruang Sidang Utama - Gedung B Lt. 3
                </p>
              </div>
            </div>
            <div class="flex items-start gap-3">
              <div
                class="p-2 bg-gray-50 dark:bg-gray-800 rounded-lg text-primary"
              >
                <span class="material-symbols-outlined">person</span>
              </div>
              <div>
                <p
                  class="text-xs text-text-secondary uppercase font-bold tracking-wide"
                >
                  Dosen Pembimbing
                </p>
                <p class="font-semibold text-text-main">
                  Dr. Ir. Budi Santoso, M.Kom.
                </p>
              </div>
            </div>
          </div>
        </div>

        <!-- Result Sidebar -->
        <div class="w-full lg:w-72">
          <div
            class="bg-sidebar-light rounded-xl p-6 border border-border-light h-full flex flex-col"
          >
            <h3
              class="text-sm font-bold text-text-main mb-4 flex items-center gap-2"
            >
              <span class="material-symbols-outlined text-primary text-sm"
                >info</span
              >
              Informasi Hasil
            </h3>
            <div class="flex flex-col items-center justify-center flex-1 py-4">
              <p class="text-xs text-text-secondary text-center mb-2">
                Nilai Akhir belum tersedia hingga sidang selesai.
              </p>
              <div class="text-4xl font-black text-gray-300 dark:text-gray-600">
                --
              </div>
              <p
                class="text-sm font-bold text-gray-400 dark:text-gray-500 mt-1"
              >
                Pending Approval
              </p>
            </div>
            <button
              class="w-full py-2 bg-surface-light border border-border-light text-text-secondary font-bold text-xs rounded hover:bg-sidebar-light transition-colors mt-auto"
            >
              Lihat Berita Acara
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- Dewan Penguji -->
    <div>
      <div class="flex items-center gap-2 mb-4">
        <span class="material-symbols-outlined text-primary">groups</span>
        <h2 class="text-xl font-bold text-text-main">Dewan Penguji</h2>
      </div>
      <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <!-- Examiner 1 -->
        <div
          class="bg-surface-light p-6 rounded-xl border border-border-light shadow-sm flex flex-col items-center text-center"
        >
          <div
            class="size-20 bg-primary/10 rounded-full flex items-center justify-center mb-4 overflow-hidden"
          >
            <img
              src="https://lh3.googleusercontent.com/aida-public/AB6AXuBt6Ugoce89YqIKeqUUE0h9UUv4Dr6C5reecypPAokAqIwfeQxw1c_BFq8wQz_CMF9G0it1-HvKNC9SpsHNkGZ11Eea_-Q23cLtiev84Nlea8fEYJ6by1ba_NvZf4_7ivxdNsmFX3LVqpQhJVHrAiZvZozSEtIe7iXyRpwZf4sQju-8SCEkbNDxHV-nQIRSfC-Nla6l4pX0li9ZpQDyrJA9FQKRjEOnlYpHCtBkHrMq3d8_IhWqmvo-aGC8ABef9ejgyLtwr2-dAMhd"
              alt="Ketua Penguji"
              class="w-full h-full object-cover"
            />
          </div>
          <h4
            class="text-primary text-xs font-bold uppercase tracking-widest mb-1"
          >
            Ketua Penguji
          </h4>
          <p class="font-bold text-text-main">
            Prof. Dr. Ahmad Dahlan, S.T., M.T.
          </p>
          <p class="text-xs text-text-secondary mt-1">
            NIP: 197805122003121002
          </p>
        </div>
        <!-- Examiner 2 -->
        <div
          class="bg-surface-light p-6 rounded-xl border border-border-light shadow-sm flex flex-col items-center text-center"
        >
          <div
            class="size-20 bg-primary/10 rounded-full flex items-center justify-center mb-4 overflow-hidden"
          >
            <img
              src="https://lh3.googleusercontent.com/aida-public/AB6AXuDSirXrK5myhxTaZVlzyMEicjvqhuYzYi352hWo3h96qDdpe57kkr68aTvMtFYhjaKYFlJg9bvFZ7vvCOLCXkHc-zVgAf7dnFzUY9J1DqPH6WE-AL-68kZOzhaMC4u8iM09GH4qJuqlTRO2KQydoGDNjV5udGJQv8scOTP6mI9dmNF2LBy92ugR1zJvNLDPoBceQEZjBPve5bWQdmCeeOxyH_njxkYUieoN_d_3CSj7R-0vQwxwsnJcIoMT6l1s3u-uQhmYsFvvvdUT"
              alt="Sekretaris Penguji"
              class="w-full h-full object-cover"
            />
          </div>
          <h4
            class="text-primary text-xs font-bold uppercase tracking-widest mb-1"
          >
            Sekretaris Penguji
          </h4>
          <p class="font-bold text-text-main">Siti Aminah, M.Cs.</p>
          <p class="text-xs text-text-secondary mt-1">
            NIP: 198511202010122005
          </p>
        </div>
        <!-- Examiner 3 -->
        <div
          class="bg-surface-light p-6 rounded-xl border border-border-light shadow-sm flex flex-col items-center text-center"
        >
          <div
            class="size-20 bg-primary/10 rounded-full flex items-center justify-center mb-4 overflow-hidden"
          >
            <img
              src="https://lh3.googleusercontent.com/aida-public/AB6AXuBYVVY8I1h6qBMSMkOlYqn8kw0_p1d-YVo0p9WJrVkxaW6_oHSPR1p8aQCHVmaHPnJv4dT6X0sMRCUPS37fgHUyK2nV_Lyr2ZQ7oCy_AhCXA8QTAirDR5HItpWl0Euqo9KhZv0Etym_mLLddkXKJ8aSok0gKfjL2sRwAGTIK-BxMUtGR5smQAqmJT5xJC3rjutvWoaTWX4UUCRR0cZwzk7iqPjVNRqHaCP1PI2K9AuLgB_qJ38WJwCC0KTy2_B8PAQUOp5Nd7TnwAPx"
              alt="Anggota Penguji"
              class="w-full h-full object-cover"
            />
          </div>
          <h4
            class="text-primary text-xs font-bold uppercase tracking-widest mb-1"
          >
            Anggota Penguji
          </h4>
          <p class="font-bold text-text-main">Hendra Kurniawan, Ph.D.</p>
          <p class="text-xs text-text-secondary mt-1">
            NIP: 198204042008011003
          </p>
        </div>
      </div>
    </div>

    <!-- Document Checklist -->
    <div
      class="bg-primary/5 dark:bg-primary/10 border border-primary/20 rounded-xl p-6 flex flex-col md:flex-row items-center gap-6 mb-10"
    >
      <div class="text-primary shrink-0">
        <span class="material-symbols-outlined text-[48px]">fact_check</span>
      </div>
      <div class="flex-1">
        <h4 class="font-bold text-text-main text-lg mb-1">
          Daftar Kelengkapan Berkas
        </h4>
        <p class="text-sm text-text-secondary">
          Pastikan Anda telah mengunggah revisi proposal dan kelengkapan
          administrasi sebelum hari pelaksanaan ujian.
        </p>
      </div>
      <button
        class="px-5 py-2.5 bg-white dark:bg-surface-dark border border-border-light dark:border-gray-700 text-primary text-sm font-bold rounded-lg hover:shadow-md transition-all"
      >
        Cek Berkas
      </button>
    </div>
  </div>
</template>

<script setup>
// Script logic if needed
</script>
